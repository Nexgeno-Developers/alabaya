<?php

// Public, read-only catalog endpoints for WATI.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function wati_catalog_respond($data, $status_code = 200)
{
    http_response_code($status_code);
    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

function wati_catalog_branches()
{
    $rows = ORM::for_table('sys_accounts')
        ->select_many('id', 'account', 'alias')
        ->order_by_asc('account')
        ->find_array();

    $branches = array();
    foreach ($rows as $row) {
        $branch_id = (int) $row['id'];
        $branch_name = trim((string) $row['alias']);

        if ($branch_name === '') {
            $branch_name = trim((string) $row['account']);
        }
        if ($branch_name === '') {
            $branch_name = 'Branch #' . $branch_id;
        }

        $branches[$branch_id] = $branch_name;
    }

    return $branches;
}

function wati_catalog_stock_by_item()
{
    $rows = ORM::for_table('sys_items_stock')
        ->select_many('item_id', 'branch_id')
        ->select_expr(
            "SUM(CASE WHEN type = 'credit' THEN stock WHEN type = 'debit' THEN -stock ELSE 0 END)",
            'quantity'
        )
        ->group_by('item_id')
        ->group_by('branch_id')
        ->find_array();

    $stock = array();
    foreach ($rows as $row) {
        $item_id = (int) $row['item_id'];
        $branch_id = (int) $row['branch_id'];

        if (!isset($stock[$item_id])) {
            $stock[$item_id] = array();
        }

        $stock[$item_id][$branch_id] = (float) $row['quantity'];
    }

    return $stock;
}

function wati_catalog_branch_quantities($branches, $quantities)
{
    $result = array();

    foreach ($branches as $branch_id => $branch_name) {
        $result[] = array(
            'branch_id' => (int) $branch_id,
            'branch_name' => $branch_name,
            'quantity' => isset($quantities[$branch_id])
                ? (float) $quantities[$branch_id]
                : 0,
        );
    }

    return $result;
}

function wati_catalog_invoice_number($value)
{
    return trim((string) $value);
}

function wati_catalog_measurements($value)
{
    $measurements = json_decode((string) $value, true);
    return is_array($measurements) ? $measurements : array();
}

function wati_catalog_invoice_status($value)
{
    return ucwords(str_replace('_', ' ', trim((string) $value)));
}

function wati_catalog_invoices()
{
    $allowed_statuses = array('pending', 'processing', 'completed', 'overdue');
    $requested_status = strtolower(trim(isset($_GET['status']) ? (string) $_GET['status'] : ''));
    $statuses = $requested_status === ''
        ? $allowed_statuses
        : array($requested_status);

    foreach ($statuses as $status) {
        if (!in_array($status, $allowed_statuses, true)) {
            wati_catalog_respond(
                array(
                    'success' => false,
                    'message' => 'Status must be pending, processing, completed, or overdue.',
                ),
                400
            );
        }
    }

    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;
    $offset = isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : 0;
    if ($limit < 0 || $limit > 500) {
        wati_catalog_respond(
            array('success' => false, 'message' => 'Limit must be between 0 and 500.'),
            400
        );
    }

    $invoice_query = ORM::for_table('sys_invoices')
        ->select_many(
            'id', 'userid', 'invoicenum', 'date', 'duedate', 'subtotal',
            'credit', 'currency_symbol', 'vtoken', 'status', 'delivery_status'
        )
        ->order_by_desc('id');

    if ($requested_status === 'overdue') {
        $invoice_query->where_lte('duedate', date('Y-m-d'));
        $invoice_query->where_in('delivery_status', array('pending', 'processing'));
    } else {
        $invoice_query->where_in('delivery_status', $statuses);
    }

    if ($limit > 0) {
        $invoice_query->limit($limit)->offset($offset);
    }

    $invoices = $invoice_query->find_array();
    if (count($invoices) === 0) {
        return array();
    }

    $invoice_ids = array();
    $customer_ids = array();
    foreach ($invoices as $invoice) {
        $invoice_ids[] = (int) $invoice['id'];
        $customer_ids[] = (int) $invoice['userid'];
    }

    $customers = ORM::for_table('crm_accounts')
        ->select_many('id', 'account', 'email', 'phone', 'measurements')
        ->where_in('id', array_unique($customer_ids))
        ->find_array();
    $customers_by_id = array();
    foreach ($customers as $customer) {
        $customers_by_id[(int) $customer['id']] = $customer;
    }

    $items = ORM::for_table('sys_invoiceitems')
        ->select_many(
            'invoiceid', 'id', 'description', 'qty', 'amount',
            'product_id', 'design_id'
        )
        ->where_in('invoiceid', $invoice_ids)
        ->order_by_asc('id')
        ->find_array();
    $items_by_invoice = array();
    foreach ($items as $item) {
        $invoice_id = (int) $item['invoiceid'];
        if (!isset($items_by_invoice[$invoice_id])) {
            $items_by_invoice[$invoice_id] = array();
        }

        $quantity = (float) $item['qty'];
        $unit_amount = (float) $item['amount'];
        $product_id = (int) $item['product_id'];
        $design_id = (int) $item['design_id'];
        $items_by_invoice[$invoice_id][] = array(
            'id' => (int) $item['id'],
            'product_code' => $product_id > 0 ? 'P-' . $product_id : '',
            'design_code' => $design_id > 0 ? 'D-' . $design_id : '',
            'description' => trim((string) $item['description']),
            'quantity' => $quantity,
            'unit_amount' => $unit_amount,
            'line_total' => round($quantity * $unit_amount, 2),
        );
    }

    $base_url = defined('U') ? U : '';
    $data = array();
    foreach ($invoices as $invoice) {
        $invoice_id = (int) $invoice['id'];
        $customer = isset($customers_by_id[(int) $invoice['userid']])
            ? $customers_by_id[(int) $invoice['userid']]
            : array();
        $total = (float) $invoice['subtotal'];
        $paid = (float) $invoice['credit'];
        $invoice_path = 'client/iview/' . $invoice_id . '/token_' . $invoice['vtoken'];
        //$payment_path = 'client/ipay/' . $invoice_id . '/token_' . $invoice['vtoken'];

        $data[] = array(
            'invoice_number' => wati_catalog_invoice_number($invoice['invoicenum']),
            'order_status' => wati_catalog_invoice_status($invoice['delivery_status']),
            'invoice_total_amount' => $total,
            'invoice_paid_amount' => $paid,
            'invoice_due_amount' => max(round($total - $paid, 2), 0),
            'invoice_url' => $base_url . $invoice_path,
            //'payment_url' => $base_url . $payment_path,
            'products_info' => isset($items_by_invoice[$invoice_id])
                ? $items_by_invoice[$invoice_id]
                : array(),
            'measurement_info' => wati_catalog_measurements(
                isset($customer['measurements']) ? $customer['measurements'] : ''
            ),
            'invoice_date' => (string) $invoice['date'],
            'due_date' => (string) $invoice['duedate'],
            'payment_status' => (string) $invoice['status'],
            'customer' => array(
                'name' => isset($customer['account']) ? (string) $customer['account'] : '',
                'email' => isset($customer['email']) ? (string) $customer['email'] : '',
                'phone' => isset($customer['phone']) ? (string) $customer['phone'] : '',
            ),
        );
    }

    return $data;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    wati_catalog_respond(
        array('success' => false, 'message' => 'Only GET requests are allowed.'),
        405
    );
}

$action = route(1);
if ($action !== 'product' && $action !== 'design' && $action !== 'invoice') {
    wati_catalog_respond(
        array('success' => false, 'message' => 'Endpoint not found.'),
        404
    );
}

if ($action === 'invoice') {
    $data = wati_catalog_invoices();
    wati_catalog_respond(
        array(
            'success' => true,
            'count' => count($data),
            'data' => $data,
        )
    );
}

$branches = wati_catalog_branches();
$stock_by_item = wati_catalog_stock_by_item();
$data = array();

if ($action === 'product') {
    $products = ORM::for_table('sys_items')
        ->select_many('id', 'design_id', 'name', 'sales_price')
        ->where('type', 'Product')
        ->where('status', 'Active')
        ->order_by_asc('name')
        ->find_array();

    foreach ($products as $product) {
        $item_id = (int) $product['id'];
        $design_id = (int) $product['design_id'];
        $quantities = isset($stock_by_item[$item_id])
            ? $stock_by_item[$item_id]
            : array();

        $data[] = array(
            'id' => $item_id,
            'product_code' => 'P-' . $item_id,
            'design_code' => $design_id > 0 ? 'D-' . $design_id : '',
            'name' => (string) $product['name'],
            'price' => (float) $product['sales_price'],
            'branch_wise_qty' => wati_catalog_branch_quantities($branches, $quantities),
        );
    }
} else {
    $design_quantities = array();
    $design_product_codes = array();
    $linked_products = ORM::for_table('sys_items')
        ->select_many('id', 'design_id')
        ->where('type', 'Product')
        ->where('status', 'Active')
        ->where_not_equal('design_id', 0)
        ->find_array();

    foreach ($linked_products as $product) {
        $item_id = (int) $product['id'];
        $design_id = (int) $product['design_id'];

        if (!isset($design_quantities[$design_id])) {
            $design_quantities[$design_id] = array();
        }
        if (!isset($design_product_codes[$design_id])) {
            $design_product_codes[$design_id] = array();
        }
        $design_product_codes[$design_id][] = 'P-' . $item_id;

        if (!isset($stock_by_item[$item_id])) {
            continue;
        }

        foreach ($stock_by_item[$item_id] as $branch_id => $quantity) {
            if (!isset($design_quantities[$design_id][$branch_id])) {
                $design_quantities[$design_id][$branch_id] = 0;
            }
            $design_quantities[$design_id][$branch_id] += $quantity;
        }
    }

    $designs = ORM::for_table('sys_designs')
        ->select_many('id', 'name', 'price')
        ->order_by_asc('name')
        ->find_array();

    foreach ($designs as $design) {
        $design_id = (int) $design['id'];
        $quantities = isset($design_quantities[$design_id])
            ? $design_quantities[$design_id]
            : array();

        $data[] = array(
            'id' => $design_id,
            'design_code' => 'D-' . $design_id,
            'product_codes' => isset($design_product_codes[$design_id])
                ? $design_product_codes[$design_id]
                : array(),
            'name' => (string) $design['name'],
            'price' => (float) $design['price'],
            'branch_wise_qty' => wati_catalog_branch_quantities($branches, $quantities),
        );
    }
}

wati_catalog_respond(
    array(
        'success' => true,
        'count' => count($data),
        'data' => $data,
    )
);
