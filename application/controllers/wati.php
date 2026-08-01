<?php

// Public, read-only endpoint used by WATI AI to answer design price and stock questions.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function wati_ai_reply($message)
{
    echo json_encode(
        array('reply' => (string) $message),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

$input = $_REQUEST;
$content_type = isset($_SERVER['CONTENT_TYPE']) ? (string) $_SERVER['CONTENT_TYPE'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && stripos($content_type, 'application/json') !== false) {
    $json_input = json_decode(file_get_contents('php://input'), true);
    if (is_array($json_input)) {
        $input = array_merge($input, $json_input);
    }
}

$customer_number = '';
foreach (array('customer_number', 'whatsapp_number', 'whatsappNumber', 'phone') as $phone_key) {
    if (isset($input[$phone_key]) && trim((string) $input[$phone_key]) !== '') {
        $customer_number = preg_replace('/\D+/', '', (string) $input[$phone_key]);
        break;
    }
}

if (strlen($customer_number) === 10) {
    $customer_number = '91' . $customer_number;
}

if (strlen($customer_number) < 7 || strlen($customer_number) > 15) {
    wati_ai_reply('Please provide a valid customer number with country code.');
}

$design_input = '';
foreach (array('design', 'design_code', 'search', 'query', 'message') as $design_key) {
    if (isset($input[$design_key]) && trim((string) $input[$design_key]) !== '') {
        $design_input = trim((string) $input[$design_key]);
        break;
    }
}

$design_id = 0;
if (preg_match('/\bD\s*[- ]?\s*(\d+)\b/i', $design_input, $matches)) {
    $design_id = (int) $matches[1];
} elseif (preg_match('/^\s*(\d+)\s*$/', $design_input, $matches)) {
    $design_id = (int) $matches[1];
}

if ($design_id <= 0) {
    wati_ai_reply(
        "customer number: {$customer_number},\n"
        . "Please provide a valid design code, for example D-857."
    );
}

$design_code = 'D-' . $design_id;
$products = ORM::for_table('sys_items')
    ->where('type', 'Product')
    ->where('status', 'Active')
    ->where('design_id', $design_id)
    ->order_by_desc('id')
    ->find_array();

if (empty($products)) {
    wati_log(
        'WATI AI design lookup found no active product for '
        . $design_code
        . ' (customer ending '
        . substr($customer_number, -4)
        . ').'
    );

    wati_ai_reply(
        "customer number: {$customer_number},\n"
        . "Design: {$design_code},\n"
        . "No active product or stock information was found for this design."
    );
}

$product_groups = array();
foreach ($products as $product) {
    $product_name = trim((string) $product['name']);
    $sales_price = (float) $product['sales_price'];
    $group_key = strtolower($product_name) . '|' . number_format($sales_price, 2, '.', '');

    if (!isset($product_groups[$group_key])) {
        $product_groups[$group_key] = array(
            'name' => $product_name,
            'price' => $sales_price,
            'item_ids' => array(),
        );
    }

    $product_groups[$group_key]['item_ids'][] = (int) $product['id'];
}

$branches = ORM::for_table('sys_accounts')->find_array();
$branch_names = array();
foreach ($branches as $branch) {
    $branch_id = (int) $branch['id'];
    $branch_name = trim((string) $branch['alias']);
    if ($branch_name === '') {
        $branch_name = trim((string) $branch['account']);
    }
    if ($branch_name === '') {
        $branch_name = 'Branch #' . $branch_id;
    }
    $branch_names[$branch_id] = $branch_name;
}

$format_number = function ($value) {
    $formatted = number_format((float) $value, 2, '.', '');
    $formatted = rtrim(rtrim($formatted, '0'), '.');
    return ($formatted === '' || $formatted === '-0') ? '0' : $formatted;
};

$reply_lines = array(
    'customer number: ' . $customer_number . ',',
    'Design: ' . $design_code . ',',
);

foreach ($product_groups as $product_group) {
    $stock_by_branch = array();
    $stock_rows = ORM::for_table('sys_items_stock')
        ->select_many('branch_id', 'stock', 'type')
        ->where_in('item_id', $product_group['item_ids'])
        ->find_array();

    foreach ($stock_rows as $stock_row) {
        $branch_id = (int) $stock_row['branch_id'];
        $stock = (float) $stock_row['stock'];
        if (strtolower((string) $stock_row['type']) === 'debit') {
            $stock *= -1;
        } elseif (strtolower((string) $stock_row['type']) !== 'credit') {
            continue;
        }

        if (!isset($stock_by_branch[$branch_id])) {
            $stock_by_branch[$branch_id] = 0;
        }
        $stock_by_branch[$branch_id] += $stock;
    }

    $available_branches = array();
    foreach ($stock_by_branch as $branch_id => $stock) {
        if ($stock <= 0) {
            continue;
        }

        $branch_name = isset($branch_names[$branch_id])
            ? $branch_names[$branch_id]
            : 'Branch #' . $branch_id;
        $available_branches[$branch_name] = $stock;
    }
    ksort($available_branches, SORT_NATURAL | SORT_FLAG_CASE);

    $safe_product_name = str_replace('*', '', $product_group['name']);
    $reply_lines[] = 'product: **' . $safe_product_name . '**';
    $reply_lines[] = 'Price ₹' . $format_number($product_group['price']);

    if (empty($available_branches)) {
        $reply_lines[] = '**Currently unavailable at all branches**';
    } else {
        foreach ($available_branches as $branch_name => $stock) {
            $safe_branch_name = str_replace('*', '', $branch_name);
            $reply_lines[] = '**' . $safe_branch_name . '** : **' . $format_number($stock) . '** Qty';
        }
    }
}

$reply = implode("\n", $reply_lines);
wati_log(
    'WATI AI design lookup succeeded for '
    . $design_code
    . ' with '
    . count($product_groups)
    . ' product result(s) (customer ending '
    . substr($customer_number, -4)
    . ').'
);

wati_ai_reply($reply);
