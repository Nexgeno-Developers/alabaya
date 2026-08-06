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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    wati_catalog_respond(
        array('success' => false, 'message' => 'Only GET requests are allowed.'),
        405
    );
}

$action = route(1);
if ($action !== 'product' && $action !== 'design') {
    wati_catalog_respond(
        array('success' => false, 'message' => 'Endpoint not found.'),
        404
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
            'design_code' => $design_id > 0 ? 'D-' . $design_id : null,
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
