<?php 
require_once dirname(__DIR__, 2) . '/system_log.php';

function get_client_ip(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

function ib_post($param, $defvalue = '')
{
    if (!isset($_POST[$param])) {
        return $defvalue;
    } else {
        return $_POST[$param];
    }
}



function _log($description,$type='',$userid='0'){
    $d = ORM::for_table('sys_logs')->create();
    $d->date = date('Y-m-d H:i:s');
    $d->type = $type;
    $d->description = $description;
    $d->userid = $userid;
    $d->ip = $_SERVER["REMOTE_ADDR"];
    $d->save();

}

$admin_extra_nav = array(
    0 => '',
    1 => '',
    2 => '',
    3 => '',
    4 => '',
    5 => '',
    6 => '',
    7 => '',
    8 => '',
    9 => '',
    10 => ''
);

$client_extra_nav = array(
    0 => '',
    1 => '',
    2 => '',
    3 => '',
    4 => '',
    5 => '',
    6 => '',
    7 => '',
    8 => '',
    9 => '',
    10 => ''
);


/*
 *
 * Create Menu dynamically for plugins and hooks
 *
 * @param string $name name of the menu
 * @param string $link link of the menu
 * @param string $c controller name to set menu active
 * @param string fontawesome or iBilling icon name
 * @param int $position position of the menu
 * @param array $submenu submenu items
 *
 * */


function add_menu_admin($name,$link='#',$c='',$icon='icon-leaf',$position=5,$submenu=array()){

    global $admin_extra_nav,$routes;

    $active = '';
    if((isset($routes['0'])) AND ($routes['0']) == $c){
        $active = 'active';
    }
    if(!empty($submenu)){

        $s = '';

        foreach($submenu as $menu){
            if(isset($menu['target'])){
                $target = 'target="'.$menu['target'].'"';
            }
            else{
                $target = '';
            }
            $s .= ' <li><a href="'.$menu['link'].'" '.$target.'>'.$menu['name'].'</a></li>';
        }

        $admin_extra_nav[$position] .= '<li class="'.$active.'" id="li_'.$c.'">
                    <a href="'.$link.'"><i class="'.$icon.'"></i> <span class="nav-label">'.$name.' </span><span class="fa arrow"></span></a>

<ul class="nav nav-second-level">
'.$s.'
</ul>
</li>';

    }
    else{
        $admin_extra_nav[$position] .= '<li class="'.$active.'" id="li_'.$c.'"><a href="'.$link.'"><i class="'.$icon.'"></i> <span class="nav-label">'.$name.'</span></a></li>';
    }

}


function add_menu_client($name,$link='#',$c='',$icon='icon-leaf',$position=3,$submenu=array()){

    global $client_extra_nav,$routes;

    $active = '';
    if((isset($routes['0'])) AND ($routes['0']) == $c){
        $active = 'active';
    }
    elseif((isset($routes['2'])) AND ($routes['2']) == $c){
        $active = 'active';
    }
    else{

    }
    if(!empty($submenu)){
        $s = '';

        foreach($submenu as $menu){
            if(isset($menu['target'])){
                $target = 'target="'.$menu['target'].'"';
            }
            else{
                $target = '';
            }
            $s .= ' <li><a href="'.$menu['link'].'" '.$target.'>'.$menu['name'].'</a></li>';
        }

        $client_extra_nav[$position] .= '<li class="'.$active.'">
                    <a href="'.$link.'"><i class="'.$icon.'"></i> <span class="nav-label">'.$name.' </span><span class="fa arrow"></span></a>

<ul class="nav nav-second-level">
'.$s.'
</ul>
</li>';

    }
    else{
        $client_extra_nav[$position] .= '<li class="'.$active.'"><a href="'.$link.'"><i class="'.$icon.'"></i> <span class="nav-label">'.$name.'</span></a></li>';
    }

}




$sub_menu_admin = array();
$sub_menu_admin['settings'] = array();
$sub_menu_admin['appearance'] = array();
$sub_menu_admin['crm'] = array();
$sub_menu_admin['reports'] = array();

function add_sub_menu_admin($parent,$name,$link){
    global $sub_menu_admin;
    $sub_menu_admin[$parent][] = '<li><a href="'.$link.'">'.$name.'</a></li>
';

}


function add_option($option, $value){

    $d = ORM::for_table('sys_appconfig')->where('setting',$option)->find_one();
    if($d){
        return false;
    }
    else{
        //add option
        $c = ORM::for_table('sys_appconfig')->create();
        $c->setting = $option;
        $c->value = $value;
        $c->save();
        return true;
    }

}


function get_option($option){
    $d = ORM::for_table('sys_appconfig')->where('setting',$option)->find_one();
    if($d){
        return $d['value'];
    }
    else{
        return false;
    }
}

function update_option($option,$value){

    $d = ORM::for_table('sys_appconfig')->where('setting',$option)->find_one();
    if($d){
        $d->value = $value;
        $d->save();
        return true;
    }
    else{
        return false;
    }

}

function delete_option($option){
    $d = ORM::for_table('sys_appconfig')->where('setting',$option)->find_one();
    if($d){
        $d->delete();
        return true;
    }
    else{
        return false;
    }
}


function ib_die($msg=''){
    echo $msg;
    exit;
}

function ib_close(){
    exit;
}


function get_custom_field_value($fid,$rid){

    $d = ORM::for_table('crm_customfieldsvalues')->where('fieldid',$fid)->where('relid',$rid)->find_one();

    return $d['fvalue'];

}


function ib_pg_count(){
    $d = ORM::for_table('sys_pg')->where('status','Active')->count();
    return $d;
}


function ib_add_field_value($fieldid,$relid,$fvalue){
    $d = ORM::for_table('crm_customfieldsvalues')->create();
    $d->fieldid = $fieldid;
    $d->relid = $relid;
    $d->fvalue = $fvalue;
    $d->save();
    return true;
}


// Date Related

function ib_today(){
    return date('Y-m-d');
}

function ib_after_1_month($from = '', $format = 'mysql'){

    if($from == ''){
        $from = strtotime(date('Y-m-d'));
    }

    if($format == 'mysql'){
        $format = 'Y-m-d';
    }

    return date($format,strtotime('+1 month',$from));

}

function ib_lan_get_line($line,$fallback=''){

    global $_L;
    if(isset($_L[$line])){
        return str_replace($line,$_L[$line],$line);
    }
    elseif($fallback != ''){
        return $fallback;
    }
    else{

        return $line;

    }

}



function d2($msg = 'I am here!'){

    if(is_array($msg)){
        foreach ($msg as $m){
            echo $m. ' |
';
        }
    }
    else{
        echo $msg;
    }

    exit;

}

function d2c( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}

function lan(){
    global $config;
    return $config['language'];
}

function add_js($f=array(),$v=''){

    global $ui;
    global $pl_path;

    if($v == ''){
        $ver = '';
    }
    else{
        $ver = '?ver='.$v;
    }
    $gen = '';
    if(is_array($f)){
        foreach($f as $p){
            $gen .= '<script type="text/javascript" src="'.$pl_path.'js/'.$p.'.js'.$ver.'"></script>
        ';
        }

        $ui->assign('xfooter', $gen);

        return true;

    }

    return false;

}


function add_js_external($url=array()){



    $gen = '';
    if(is_array($url)){
        foreach($url as $u){
            $gen .= '<script type="text/javascript" src="'.$u.'.js"></script>
        ';
        }



        return $gen;

    }

    return false;

}

function add_js_internal($paths=array()){



    $gen = '';
    if(is_array($paths)){
        foreach($paths as $u){
            $gen .= '<script type="text/javascript" src="'.APP_URL.'/'.$u.'.js"></script>
        ';
        }



        return $gen;

    }

    return false;

}

function set_tpl($path){
    global $ui;
    $ui->assign('tplheader', $path.'header');
    $ui->assign('tplfooter', $path.'footer');
}


function set_admin_nav($path){
    global $ui;
    $ui->assign('tplnav', $path.'nav');
}

function language_append($path){
    global $_L;
    $file = 'application/plugins/'.$path;
    include ($file);
}

function lan_register($path){

    $x = include $path;


    var_dump($x);
    exit;


}


function add_plugin_ui_header_admin($header=''){
    global $plugin_ui_header_admin;
    array_push($plugin_ui_header_admin,$header);
}

function add_css_admin($path){
    global $plugin_ui_header_admin_css;
    array_push($plugin_ui_header_admin_css,$path);
}

function add_plugin_ui_header_client($header=''){
    global $plugin_ui_header_client;
    array_push($plugin_ui_header_client,$header);
}

function add_css_client($path){
    global $plugin_ui_header_client_css;
    array_push($plugin_ui_header_client_css,$path);
}



function i_close($msg = ''){
    echo $msg;
    exit;
}

function inner_contents($lk){

}


function ib_http_request($url,$method='GET',$params=array(),$headers=array(),$resp_header=false,$follow_redirect=false){

    $response = '';



    if (!is_callable('curl_init')) {

        throw new Exception('CURL Not available in this Server.');

    }

    switch ($method) {

        case 'GET':

            $q = '';
            foreach($params as $key=>$value) {

                $value = urlencode($value);

                $q .= $key.'='.$value.'&';


            }

            $req = $url;

            if($q != ''){
                $q = rtrim($q, '&');

                $req = $url.'?'.$q;

            }



            try {
                $ch = curl_init();

                if (FALSE === $ch)
                    throw new Exception('failed to initialize');

                if (!empty($headers)) {

                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                }

                curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $req);

                if($follow_redirect){
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                }

                if($resp_header){
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                }

                $response = curl_exec($ch);

                if (FALSE === $response)
                    throw new Exception(curl_error($ch), curl_errno($ch));
                curl_close($ch);

            } catch(Exception $e) {

                throw new Exception($e->getCode(). ' '. $e->getMessage());

            }










            break;

        case 'POST':

            try {
                $ch = curl_init();

                if (FALSE === $ch)
                    throw new Exception('failed to initialize');

                if (!empty($headers)) {

                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                }

                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                if($resp_header){
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                }

                $response = curl_exec($ch);

                if (FALSE === $response)
                    throw new Exception(curl_error($ch), curl_errno($ch));
                curl_close($ch);

            } catch(Exception $e) {

                throw new Exception($e->getCode(). ' '. $e->getMessage());

            }




            break;

    }

    return $response;

}


function db_find_many($table,$columns=array()){

    $d = ORM::for_table($table);

    foreach($columns as $column){
        $d->select($column);
    }

    $ret = $d->find_many();

    return $ret;

}

function db_find_array($table,$columns=array(),$order_by=''){

    $d = ORM::for_table($table);

    foreach($columns as $column){
        $d->select($column);
    }

    if($order_by != ''){

        $o = explode(':',$order_by);

        if($o[0] == 'asc'){
            $d->order_by_asc($o[1]);
        }
        elseif ($o[0] == 'desc'){
            $d->order_by_desc($o[1]);
        }
        else{
            $d->order_by_desc($order_by);
        }

    }

    $ret = $d->find_array();

    return $ret;

}

function db_find_one($table,$id){

    $d = ORM::for_table($table)->find_one($id);

    if($d){
        return $d;
    }
    else{
        return false;
    }

}

function db_delete_row($table,$id){

    $d = ORM::for_table($table)->find_one($id);
    if($d){
        $d->delete();
        return true;
    }
    else{
        return false;
    }

}


function route($id){

    global $routes;

    if(isset($routes[$id]) && $routes[$id] != ''){
        return $routes[$id];
    }
    else{
        return false;
    }

}


function is_dev(){

    global $_app_stage;

    if($_app_stage != "Dev"){

        die("Unable to handle your request in Live Mode.");

    }

}




function ib_money_format($amount,$config,$symbol=''){

    if($symbol == ''){
        $currency_code = $config['currency_code'];
    }
    else{
        $currency_code = $symbol;
    }


    $thousand_separator_placement = $config['thousand_separator_placement'];
    $currency_decimal_digits = $config['currency_decimal_digits'];
    $currency_symbol_position = $config['currency_symbol_position'];
    $dec_point = $config['dec_point'];
    $thousands_sep = $config['thousands_sep'];


    if($currency_decimal_digits == 'true'){
        $dec_digit = 2;
    }
    else{
        $dec_digit = 0;
    }

    if($currency_symbol_position == 's'){
        $retval = number_format($amount,$dec_digit,$dec_point,$thousands_sep).$currency_code;
    }
    else{
        $retval = $currency_code.' '.number_format($amount,$dec_digit,$dec_point,$thousands_sep);
    }



    return $retval;


}

/*
 * @deprecated
 * use ib_posted_data
 * */

function ib_get_posted_data(){

    $data = array();

    foreach($_POST as $key => $value) {
        $data[$key] = trim($value);
    }

    return $data;

}

function ib_posted_data(){

    $data = array();

    foreach($_POST as $key => $value) {
        $data[$key] = trim($value);
    }

    return $data;

}

function expiry_date($e_date){

    $today = date_create(date("Y-m-d"));
    $ex_date = date_create($e_date);
    $diff = date_diff($today,$ex_date);
    if($diff->format("%R%a") <= 30 || $diff->format("%R%a") <= 0){
      echo '<span style="color:red>'.$e_date.'.</span>';
    }else{
      echo $e_date;
    }


}


function ib_js_date_format($format,$js='moment'){

    if($js == 'moment'){

        $format = str_replace('d','DD',$format);
        $format = str_replace('M','MMM',$format);
        $format = str_replace('m','MM',$format);
        $format = str_replace('Y','YYYY',$format);
        $format = str_replace('jS','Do',$format);
        return $format;

    }
    elseif ($js='picker'){
        $format = str_replace('d','dd',$format);
        $format = str_replace('m','mm',$format);
        $format = str_replace('Y','yyyy',$format);
        $format = str_replace('M','mmm',$format);
        $format = str_replace('jS','d',$format);
        return $format;
    }

    else{

        $format = str_replace('d','DD',$format);
        $format = str_replace('m','MM',$format);
        $format = str_replace('Y','YYYY',$format);
        $format = str_replace('M','MMM',$format);
        $format = str_replace('jS','Do',$format);
        return $format;

    }


}


function has_access($rid,$shortname,$action='view'){

    if($rid == 0){
        return true;
    }

    $d = ORM::for_table('sys_staffpermissions')->where('rid',$rid)->where('shortname',$shortname)->find_one();

    if($d){

        switch ($action){

            case 'view':

                if($d->can_view == 1){
                    return true;
                }
                else{
                    return false;
                }

                break;

            case 'edit':

                if($d->can_edit == 1){
                    return true;
                }
                else{
                    return false;
                }

                break;

            case 'create':

                if($d->can_create == 1){
                    return true;
                }
                else{
                    return false;
                }

                break;

            case 'delete':

                if($d->can_delete == 1){
                    return true;
                }
                else{
                    return false;
                }

                break;

            default:
                return false;
        }

    }
    else{
        return false;
    }

}

function getTax_opt(){

	$taxes = ORM::for_table('sys_tax')->order_by_asc('rate')->find_many();
	$tax_opts = "<optgroup label=GST>";
	foreach ($taxes as $tax) {
		if($tax['taxtype']=='GST'){
			$tax_opts .= '<option value="'.$tax['id'].'" rate="'.$tax['rate'].'">'.$tax['name']." ".$tax['rate']." %".'</option>';
		}
	} 
	$tax_opts .= '</optgroup>
								<optgroup label=IGST>';
	foreach ($taxes as $tax) {
		if($tax['taxtype']=='IGST'){
			$tax_opts .= '<option value="' . $tax['id'] . '" rate="'.$tax['rate'].'">' . $tax['name'] ." ". $tax['rate'] ." %" .'</option>';
		}
	} 
	$tax_opts .= '</optgroup>';
			
	return $tax_opts;
}


function get_Account($id){

    $d = ORM::for_table('crm_accounts')->find_one($id);
    
  return $d['account'];

}

function get_amt_inWords($number){
 $no = round($number);
   $point = round($number - $no, 2) * 100;
   $hundred = null;
   $digits_1 = strlen($no);
   $i = 0;
   $str = array();
   $words = array('0' => '', '1' => 'One', '2' => 'Two',
    '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
    '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
    '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
    '13' => 'Thirteen', '14' => 'Fourteen',
    '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
    '18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
    '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
    '60' => 'Sixty', '70' => 'Seventy',
    '80' => 'Eighty', '90' => 'Ninety');
   $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
   while ($i < $digits_1) {
     $divider = ($i == 2) ? 10 : 100;
     $number = floor($no % $divider);
     $no = floor($no / $divider);
     $i += ($divider == 10) ? 1 : 2;
     if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
     } else $str[] = null;
  }
  $str = array_reverse($str);
  $result = implode('', $str);
  $points = ($point) ?
    "." . $words[$point / 10] . " " . 
          $words[$point = $point % 10] : '';
  return $result . "Rupees  Only.";// . $points . " Paise";

}

function get_type_by_id($table, $tid, $sid, $field){
	//var_dump($table);
    $d = ORM::for_table($table)->select($field)->where($tid,$sid)->find_one();
    if($d){
        return $d[$field];
    }
    else{
        return false;
    }
}
function sendSMS($u,$sms){
	$seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
	shuffle($seed);
	$rand = ''; 
	foreach(array_rand($seed, 5) as $k) { $rand .= $seed[$k]; }
	$sms_working_key='A16cfa1aa81471e43347d5600fc9f37c6';
	$sms_sender_id='PDLCIN';
	
	$sms_url=sprintf("http://www.kit19.com/ComposeSMS.aspx?username=market609443&password=10298&sender=%s&to=%s&message=%s&priority=1&dnd=1&unicode=0", $sms_sender_id, $u, urlencode($sms)); 

	$ch=curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_URL,$sms_url);
	//curl_setopt($ch,CURLOPT_TIMEOUT,3);  
	$content = trim(curl_exec($ch));  
	curl_close($ch);
	return $content;
}

function get_type_by_id_multi($table, $tid, $sid, $fields){

	$fields = explode(',', $fields);
    $d = ORM::for_table($table);
    foreach($fields as $field){
       $d->select($field);
    }
    $d->where($tid,$sid);
    $d = $d->find_one();
    
    if($d){
        return $d;
    }
    else{
        return false;
    }
}


function get_tds_amt($id)
{
    $trs = ORM::for_table('sys_transactions')->where('category', 'TDS')->where('iid', $id)->order_by_desc('id')->find_many();
    $amt = 0;
    foreach($trs as $tr){
        $amt += $tr['amount'];
    }
    return $amt ? $amt : 0;
}


function SaleID($id = "")
{
    if(!empty($id))
    {
        return 'MK-'.$id;
    }
    else
    {
        return '';
    }
}

function add_sale_log($sale_id, $message, $data = "")
{
    $sale = ORM::for_table('crm_sales_logs')->create();
    $sale->admin_id    = 1;
    $sale->sale_id     = $sale_id;
    $sale->action      = $message;
    $sale->timestamp   = date('Y-m-d H:i:s');

    $sale->save();
}

function clean_url($url) 
{
    $disallowed = array('http://', 'https://');
    foreach($disallowed as $d) {
       if(strpos($url, $d) === 0) {
          return str_replace($d, '', $url);
       }
    }
    return $url;
 }


 function stock_record($item_id, $stock, $type, $invoice_id = "", $parent_item_id = "", $vendor_id = "", $purchase_price = "", $branch_id = "", $transfer_ref = "")
 {
    $record = ORM::for_table('sys_items_stock')->create();

    $record->item_id        = (int)$item_id;
    $record->stock          = $stock;
    $record->type           = $type;
    $record->invoice_id     = $invoice_id;
    $record->parent_item_id = $parent_item_id;
    $record->vendor_id      = $vendor_id;
    $record->purchase_price = $purchase_price;
    $record->branch_id      = $branch_id;
    // Only set transfer_ref if not empty
    if (!empty($transfer_ref)) {
        $record->transfer_ref = $transfer_ref;
    }
    $record->timestamp      = date('Y-m-d H:i:s'); 
     
    $record->save();  
 }



 function product_stock_info($product_id)
 {
    $sys_items_stock = ORM::for_table('sys_items_stock')->select('stock')->where('item_id', $product_id)->where('type', 'credit')->find_many();
    $sys_invoiceitems = ORM::for_table('sys_items_stock')->select('stock')->where('item_id', $product_id)->where('type', 'debit')->find_many();    

    $allcredit = 0;
    foreach($sys_items_stock as $row)
    {
        $allcredit += $row['stock'];
    }

    $alldebit = 0;
    foreach($sys_invoiceitems as $row)
    {
        $alldebit += $row['stock'];
    }
    
    $response = array(
        'credited_stock_count' => $allcredit,
        'debited_stock_count' => $alldebit,
        'current_stock_count' => $allcredit - $alldebit,
    );

    return json_encode($response);

 }

function product_stock_info_by_branch($product_id)
{
    $rows = ORM::for_table('sys_items_stock')
        ->select_many('branch_id', 'stock', 'type')
        ->where('item_id', $product_id)
        ->find_array();

    $stock_by_branch = [];

    foreach ($rows as $r) {
        $b = $r['branch_id'];
        $s = ($r['type'] == 'credit') ? $r['stock'] : -$r['stock'];
        $stock_by_branch[$b] = ($stock_by_branch[$b] ?? 0) + $s;
    }

    // Optional: sort by branch name
    ksort($stock_by_branch);

    return $stock_by_branch;
}


 function make_thumb($src, $dest, $desired_width)
{
    // Make directory if not made
    if(!is_dir($dest))
    mkdir($dest,0755,true);

    // Get path info
    $pInfo = pathinfo($src);
    $mime = getimagesize($src)['mime'];

    // Save the new path using the current file name
    $dest = $dest."/".$pInfo['basename'];

    if (file_exists($dest))
    {
        return $dest;
    }
    else
    {  
        // Do the rest of your stuff and things...
        //$source_image = imagecreatefromjpeg($src);

        switch($mime){
            case 'image/jpeg':
                $source_image = imagecreatefromjpeg($src);
                break;
            case 'image/png':
                $source_image = imagecreatefrompng($src);
                break;
            case 'image/gif':
                $source_image = imagecreatefromgif($src);
                break;
            default:
                $source_image = imagecreatefromjpeg($src);
        }         

        $width = imagesx($source_image);
        $height = imagesy($source_image);
        $desired_height = floor($height * ($desired_width / $width));
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
        imagejpeg($virtual_image, $dest);
        return $dest;        
    }
}


function compressImage($source, $destination, $quality) {
	// Get image info
	$imgInfo = getimagesize($source);
	$mime = $imgInfo['mime'];
	
	// Create a new image from file
	switch($mime){
		case 'image/jpeg':
			$image = imagecreatefromjpeg($source);
			break;
		case 'image/png':
			$image = imagecreatefrompng($source);
			break;
		case 'image/gif':
			$image = imagecreatefromgif($source);
			break;
		default:
			$image = imagecreatefromjpeg($source);
	}
	
	// Save image
  imagejpeg($image, $destination, $quality);

}


function SMS($phone, $message)
{
    $ch=curl_init('https://www.smsgatewayhub.com/api/mt/SendSMS?APIKey=zjCx80GAyUKUtWdGIEWZRA&senderid=KAAKEC&channel=2&DCS=0&flashsms=0&number='.$phone.'&text='.$message.'&route=1');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,"");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,2);

    $data = curl_exec($ch);
    return($data);
}


function invoice_count($branch_id = '')
{
    $query = ORM::for_table('sys_invoices')->select('duedate')->select('delivery_status');

    // If branch is not 'all' and not empty, filter by branch
    if (!empty($branch_id) && $branch_id != 'all') {
        $query->where('company_id', $branch_id);
    }

    $invoice = $query->find_many();

    $pending    = 0;
    $processing = 0;
    $completed  = 0;
    $delivered  = 0;
    $overdue    = 0;
    foreach($invoice as $inv)
    {
        if($inv['delivery_status'] == 'pending')
        {
            $pending += 1;
        }
        if($inv['delivery_status'] == 'processing')
        {
            $processing += 1;
        }
        if($inv['delivery_status'] == 'completed')
        {
            $completed += 1;
        } 
        if($inv['delivery_status'] == 'delivered')
        {
            $delivered += 1;
        }    
        if($inv['duedate'] <= date('Y-m-d') && in_array($inv['delivery_status'], array('pending', 'processing')) )
        {
            $overdue += 1;
        }                           
    }

    return json_encode(array('total' => $pending+$processing+$completed+$delivered,'pending' => $pending, 'processing' => $processing, 'completed' => $completed, 'delivered' => $delivered, 'overdue' => $overdue));
}

function get_item_categories()
{
    $categories = ORM::for_table('sys_items_category')->find_many();
    return $categories;
}

function get_designs()
{
    $designs = ORM::for_table('sys_designs')->find_many();
    return $designs;
}

include 'third_party/phpqrcode/qrlib.php';

function qrcode_generate($text)
{
    // Include the qrlib file
    
    //$text = "GEEKS FOR GEEKS";
    
    // $path variable store the location where to 
    // store image and $file creates directory name
    // of the QR code file by using 'uniqid'
    // uniqid creates unique id based on microtime
    $path = 'ui/lib/imgs/qrcode/';
    $file = $path.$text.".jpg";
    
    // $ecc stores error correction capability('L')
    $ecc = 'L';
    $pixel_Size = 4;
    $frame_Size = 1;
    
    // Generates QR Code and Stores it in directory given
    QRcode::png($text, $file, $ecc, $pixel_Size, $frame_Size);
    
    // Displaying the stored QR code from directory
    //echo "<center><img src='".$file."'></center>";

    return $file;
}

function wati_notifiation($name, $invoicenumber, $paymentstatus, $orderstatus, $ordertrack_url, $phone){

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://live-server-115146.wati.io/api/v2/sendTemplateMessages',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
      "template_name": "order_status2",
      "broadcast_name": "order_status2",
      "receivers": [
        {
          "whatsappNumber": "' . $phone . '",
          "customParams": [
                    {
                        "name": "name",
                        "value": "' . $name . '"
                    },
                    {
                        "name": "order_number",
                        "value": "' . $invoicenumber . '"
                    },
                    {
                        "name": "order_payment_status",
                        "value": "' . $paymentstatus . '"
                    },
                    {
                        "name": "order_status_url",
                        "value": "' . $orderstatus . '"
                    },
                    {
                        "name": "order_status_url_partial_variable",
                        "value": "' . $ordertrack_url . '"
                    }
                ]
        }
      ]
    }',
      CURLOPT_HTTPHEADER => array(
        'accept: */*',
        'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJiMTZhZThiZC04NmNiLTQ4YTItOTkwZS0yOGViNTRlN2Q2OGEiLCJ1bmlxdWVfbmFtZSI6ImNvbnRhY3RAYWJheWFkZXNpZ25lci5jb20iLCJuYW1laWQiOiJjb250YWN0QGFiYXlhZGVzaWduZXIuY29tIiwiZW1haWwiOiJjb250YWN0QGFiYXlhZGVzaWduZXIuY29tIiwiYXV0aF90aW1lIjoiMDEvMDkvMjAyNCAwNTo0Njo1MiIsImRiX25hbWUiOiIxMTUxNDYiLCJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL3dzLzIwMDgvMDYvaWRlbnRpdHkvY2xhaW1zL3JvbGUiOiJBRE1JTklTVFJBVE9SIiwiZXhwIjoyNTM0MDIzMDA4MDAsImlzcyI6IkNsYXJlX0FJIiwiYXVkIjoiQ2xhcmVfQUkifQ.ek5oVVij0bYtUfQFbug2OXbcJNg_WQd-KIQFJMMjt8Q',
        'Content-Type: application/json-patch+json',
        'Cookie: affinity=1704793805.327.42379.834121|4101bd3ddf1811378a5246c0880e0831'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    
    return $response;

}

function send_email_brevo_api($to, $username, $subject, $txt, $headers) {
    
    // API endpoint
    $url = 'https://api.brevo.com/v3/smtp/email';

    // API key from config
    $apiKey = BREVO_API_KEY;
    
    // Data to be sent
    $data = array(
        "sender" => array(
            "name" => "Abaya Designer",
            "email" => "contact@abayadesigner.com"
        ),
        "to" => array(
            array(
                "email" => $to,
                "name" => $username
            )
        ),
        "subject" => $subject,
        "htmlContent" => $txt
    );
    
    // Convert data to JSON format
    $postData = json_encode($data);
    
    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'accept: application/json',
        'api-key: ' . $apiKey,
        'content-type: application/json'
    ));
    
    // Execute cURL session
    $response = curl_exec($ch);
    
    // Close cURL session
    curl_close($ch);
    
    // Output response
    //echo $response;    
}

function filter_by_branch($table, $branch_id) {
    $q = ORM::for_table($table);
    if (!empty($branch_id) && $branch_id !== 'all') {
        $q->where('branch_id', $branch_id);
    }
    return $q;
}

/*
function get_branch_name($branch_id, $account='account') {
    // Fetch branch info using ORM
    $branch = ORM::for_table('sys_accounts')
        ->select($account, 'branch_name')
        ->where('id', $branch_id)
        ->find_one();

    if ($branch) {
        return $branch->branch_name; // Return branch name
    } else {
        return null; // Return null if branch not found
    }
}
*/

function get_branch_name($branch_id, $account = 'account') {
    static $cache = [];

    if (!$branch_id) return null;
    $key = $branch_id . '|' . $account;

    if (isset($cache[$key])) return $cache[$key];

    $branch = ORM::for_table('sys_accounts')
        ->select($account, 'branch_name')
        ->where('id', $branch_id)
        ->find_one();

    $cache[$key] = $branch ? $branch->branch_name : null;
    return $cache[$key];
}

function get_branch_id($table, $id) {
    $record = ORM::for_table($table)->find_one($id);

    if ($record) {
        $branch_id = $record->branch_id;
        // system_log("Found branch_id={$branch_id} for {$table}.id={$id}");
        return $branch_id;
    } else {
        // system_log("No record found in {$table} for id={$id}");
        return null;
    }
}

// function update_collection_status($collection_id) {
//     $c = ORM::for_table('branch_collections')->find_one($collection_id);
//     if (!$c) return;
//     $total_handover = ORM::for_table('branch_handover_entries')
//         ->where('collection_id', $collection_id)
//         ->sum('amount_paid') ?: 0;
//     if ($total_handover == 0) {
//         $c->status = 'Pending';
//     } elseif ($total_handover < $c->collected_amount) {
//         $c->status = 'Partially Paid';
//     } else {
//         $c->status = 'Paid';
//     }
//     $c->updated_at = date('Y-m-d H:i:s');
//     $c->save();
// }

function update_collection_status($collection_id) {
    $c = ORM::for_table('branch_collections')->find_one($collection_id);
    if (!$c) return;

    $total_handover = ORM::for_table('branch_handover_entries')
        ->where('collection_id', $collection_id)
        ->where('status', 'Approved') // only approved handovers count
        ->sum('amount_paid') ?: 0;

    if ($total_handover == 0) {
        $c->status = 'Pending';
    } elseif ($total_handover < $c->collected_amount) {
        $c->status = 'Partially Paid';
    } else {
        $c->status = 'Paid';
    }

    $c->updated_at = date('Y-m-d H:i:s');
    $c->save();
}
