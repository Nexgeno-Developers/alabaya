<?php
_auth();
$ui->assign('_application_menu', 'manage');
$ui->assign('_title', 'Designs'.'- '. $config['CompanyName']);
$ui->assign('_st', 'Designs');
$action = $routes['1'];
$user = User::_info();
$ui->assign('user', $user);
switch ($action) {

    case 'view':
        $id  = $routes['2'];
        $item = ORM::for_table('sys_designs')->find_one($id);
        $invoiceItems = ORM::for_table('sys_invoiceitems')->where('design_id', $id)->find_many();
        $ui->assign('_title', 'History');
        $ui->assign('_st', 'History');      
        $ui->assign('p_name', $item['name']);  
        $ui->assign('invoiceItems', $invoiceItems); 
        $ui->assign('id', $id);
        $ui->display('manage/view-design.tpl');
    break;

    case 'add-design':
        $fabrics = ORM::for_table('sys_items')->where('product_category', 'fabric')->find_many();
        $ui->assign('fabrics', $fabrics); 
        $stones = ORM::for_table('sys_items')->where('product_category', 'stone_&_size')->find_many();
        $ui->assign('stones', $stones); 
         
        $handwork_materials = ORM::for_table('sys_items')->where('product_category', 'handwork_materials')->find_many();
        $ui->assign('handwork_materials', $handwork_materials); 
        $others = ORM::for_table('sys_items')->where('product_category', 'others')->find_many();
        $ui->assign('others', $others);

        $cloths = ORM::for_table('sys_cloths')->find_many();
        $ui->assign('cloths', $cloths);        
        
        $ui->assign('type','Product');

        // Fetch category employees
        $categoryEmployees = ORM::for_table('category_employee')->select('id')->select('name')->find_array();
        $ui->assign('category_employees', $categoryEmployees);
        
        $css_arr = array('s2/css/select2.min');
        $js_arr = array('s2/js/select2.min','numeric');
        $ui->assign('xjq', '$(\'.amount\').autoNumeric(\'init\');');        
        Event::trigger('add_invoice_rendering_form');

        $ui->assign('xheader', Asset::css($css_arr));
        $ui->assign('xfooter', Asset::js($js_arr));

        $max = ORM::for_table('sys_items')->max('id');
        $nxt = $max+1;
        $ui->assign('nxt',$nxt);
        $ui->display('manage/add-design.tpl');
    break;        


    case 'add-post':
        $name = trim(_post('name'));
        $sales_price = Finance::amount_fix(_post('sales_price'));
        $description = _post('description');
        $cloth_id    = _post('cloth_id');
        $fabric_ids  = $_POST['fabric_id'];
        $fabric_qty  = $_POST['fabric_qty'];
        $stone_ids   = $_POST['stone_id'];
        $stone_qty  = $_POST['stone_qty'];

        $handwork_ids   = $_POST['handwork_id'];
        $handwork_qty  = $_POST['handwork_qty'];
        $others_ids   = $_POST['others_id'];
        $others_qty  = $_POST['others_qty'];

        $category_ids    = $_POST['category_id'];
        $category_prices = $_POST['category_price'];
        
        /*echo '<pre>';
        var_dump($fabric_ids);
        var_dump($fabric_qty);
        var_dump($stone_ids);
        var_dump($stone_qty);
        var_dump($handwork_ids);
        var_dump($others_ids);
        echo '</pre>';exit;*/


        $msg = '';
       
        if($name == ''){
            $msg .= 'Item Name is required <br>';
        }
        if($sales_price == ''){
            $msg .= 'Sale price is required <br>';
        }  
        if($category_prices){
            foreach($category_prices as $price) {
                if($price == '') {
                    $msg .= 'Fill Each category price <br>';
                    break; // Optional: Stop after the first missing price
                }
            }
        }
        if($cloth_id == ''){
            $msg .= 'Cloth Type is required <br>';
        }  
        
        $is_exist = ORM::for_table('sys_designs')->where('name', $name)->count();
        if($is_exist > 0)
        {
            $msg .= 'Design Already exist <br>';
        }
        
        if($msg == ''){
            $d = ORM::for_table('sys_designs')->create();
            $d->name = $name;
            $d->price = $sales_price;
            $d->description = $description;
            $d->timestamp = date('Y-m-d H:i:s');

            
            $fabric = array();   
            for ($x = 0; $x <= count($fabric_ids); $x++)
            {
                if(!empty($fabric_ids[$x]) && !empty($fabric_qty[$x]))
                {
                    $fabric[] = array('fabric_id' => $fabric_ids[$x], 'fabric_qty' => $fabric_qty[$x]);
                }
            }
            $d->fabrics = json_encode($fabric);

            $stone = array();   
            for ($x = 0; $x <= count($stone_ids); $x++)
            {
                if(!empty($stone_ids[$x]) && !empty($stone_qty[$x]))
                {
                    $stone[] = array('stone_id' => $stone_ids[$x], 'stone_qty' => $stone_qty[$x]);
                }
            }
            $d->stones = json_encode($stone); 
            
            $handworks = array();   
            for ($x = 0; $x <= count($handwork_ids); $x++)
            {
                if(!empty($handwork_ids[$x]) && !empty($handwork_qty[$x]))
                {
                    $handworks[] = array('handwork_id' => $handwork_ids[$x], 'handwork_qty' => $handwork_qty[$x]);
                }
            }
            $d->handworks = json_encode($handworks);   
            
            $other = array();   
            for ($x = 0; $x <= count($others_ids); $x++)
            {
                if(!empty($others_ids[$x]) && !empty($others_qty[$x]))
                {
                    $other[] = array('other_id' => $others_ids[$x], 'other_qty' => $others_qty[$x]);
                }
            }
            $d->others = json_encode($other);            
            
            $d->cloth_id = _post('cloth_id');
            
            // Handle Category Pricing
            $categoryPricing = [];
            foreach ($category_ids as $key => $category_id) {
                if (!empty($category_id) && isset($category_prices[$key])) {
                    $categoryPricing[] = [
                        'category_id' => $category_id,
                        'price' => $category_prices[$key]
                    ];
                }
            }
            $d->category_pricing = json_encode($categoryPricing);
            
            $img_array = array();
            $count     = count($_FILES['design_images']);

            for ($x = 0; $x <= $count; $x++)
            {
                if($_FILES['design_images']["name"][$x])
                {
                    $filename = 'ui/lib/imgs/design/'.time().$x.'.jpg';
                    $img_array[] = $filename;
                    move_uploaded_file($_FILES['design_images']["tmp_name"][$x], $filename);
                }
            }            

            $d->image = json_encode($img_array);

            $d->save();
            $id = $d->id();
            _msglog('s',$_L['Item Added Successfully']);
            echo $id;
        }
        else{
            echo $msg;
        }
        break;



    case 'list-design':
        $d = ORM::for_table('sys_designs')->order_by_desc('id')->find_many();
        $ui->assign('d',$d);
        $ui->assign('xheader', '
					<link rel="stylesheet" type="text/css" href="' . $_theme . '/css/modal.css"/>');
        $ui->assign('xfooter', '
        <script type="text/javascript" src="' . $_theme . '/lib/modal.js"></script>
					<script type="text/javascript" src="' . $_theme . '/lib/design-list.js"></script>');
        $ui->display('manage/list-design.tpl');
        break;


    case 'edit-post':

    $id = _post('id');
    $name = _post('name');
    $sales_price = Finance::amount_fix(_post('sales_price'));
    $description = _post('description');
    $fabric_ids  = $_POST['fabric_id'];
    $fabric_qty  = $_POST['fabric_qty'];
    $stone_ids   = $_POST['stone_id'];
    $stone_qty  = $_POST['stone_qty'];   
    
    $handwork_ids   = $_POST['handwork_id'];
    $handwork_qty  = $_POST['handwork_qty'];
    $others_ids   = $_POST['others_id'];
    $others_qty  = $_POST['others_qty'];    

    $category_ids    = $_POST['category_id'];
    $category_prices = $_POST['category_price'];
    
    $msg = '';
   
    if($name == ''){
        $msg .= 'Item Name is required <br>';
    }
    if($sales_price == ''){
        $msg .= 'Sale price is required <br>';
    }     
    if($category_prices){
        foreach($category_prices as $price) {
            if($price == '') {
                $msg .= 'Fill Each category price <br>';
                break; // Optional: Stop after the first missing price
            }
        }
    }

    if($msg == ''){
        $d = ORM::for_table('sys_designs')->find_one($id);
        $d->name = $name;
        $d->price = $sales_price;
        $d->description = $description;
        $d->timestamp = date('Y-m-d H:i:s');

        $fabric = array();   
        for ($x = 0; $x <= count($fabric_ids); $x++)
        {
            if(!empty($fabric_ids[$x]) && !empty($fabric_qty[$x]))
            {
                $fabric[] = array('fabric_id' => $fabric_ids[$x], 'fabric_qty' => $fabric_qty[$x]);
            }
        }
        $d->fabrics = json_encode($fabric);

        $stone = array();   
        for ($x = 0; $x <= count($stone_ids); $x++)
        {
            if(!empty($stone_ids[$x]) && !empty($stone_qty[$x]))
            {
                $stone[] = array('stone_id' => $stone_ids[$x], 'stone_qty' => $stone_qty[$x]);
            }
        }
        $d->stones = json_encode($stone);
        
        $handworks = array();   
        for ($x = 0; $x <= count($handwork_ids); $x++)
        {
            if(!empty($handwork_ids[$x]) && !empty($handwork_qty[$x]))
            {
                $handworks[] = array('handwork_id' => $handwork_ids[$x], 'handwork_qty' => $handwork_qty[$x]);
            }
        }
        $d->handworks = json_encode($handworks);   
        
        $other = array();   
        for ($x = 0; $x <= count($others_ids); $x++)
        {
            if(!empty($others_ids[$x]) && !empty($others_qty[$x]))
            {
                $other[] = array('other_id' => $others_ids[$x], 'other_qty' => $others_qty[$x]);
            }
        }
        $d->others = json_encode($other);               
        
        $d->cloth_id = _post('cloth_id');        
        
        // Handle Category Pricing
        $categoryPricing = [];
        foreach ($category_ids as $key => $category_id) {
            if (!empty($category_id) && isset($category_prices[$key])) {
                $categoryPricing[] = [
                    'category_id' => $category_id,
                    'price' => $category_prices[$key]
                ];
            }
        }
        $d->category_pricing = json_encode($categoryPricing);
        
        $old = $d->image;
        $img_array = array();
        $count     = count($_FILES['design_images']);

        if(!empty($_FILES['design_images']["name"][0]))
        {
            for ($x = 0; $x <= $count; $x++)
            {
                if($_FILES['design_images']["name"][$x])
                {
                    $filename = 'ui/lib/imgs/design/'.time().$x.'.jpg';
                    $img_array[] = $filename;
                    move_uploaded_file($_FILES['design_images']["tmp_name"][$x], $filename);
                }
            }   
                     
            $d->image = json_encode($img_array);

            foreach(json_decode($old, true) as $row)
            {
                unlink($row);
            }
        }
        $d->save();
        $id = $d->id();
        echo $id;
    }
    else{
        echo $msg;
    }    

        break;

				
    case 'delete':
        $id = $routes['2'];
        if($_app_stage == 'Demo'){
            r2(U . 'accounts/list', 'e', 'Sorry! Deleting Account is disabled in the demo mode.');
        }
        $d = ORM::for_table('sys_accounts')->find_one($id);
        if($d){
            $d->delete();
            r2(U . 'accounts/list', 's', $_L['account_delete_successful']);
        }

        break;

    case 'edit-form':

        $id = $routes['2'];
        $d = ORM::for_table('sys_designs')->find_one($id);
        if($d)
        {
            $fabrics = ORM::for_table('sys_items')->where('product_category', 'fabric')->find_many();
            $ui->assign('fabrics', $fabrics); 
            $stones = ORM::for_table('sys_items')->where('product_category', 'stone_&_size')->find_many();
            $ui->assign('stones', $stones);  

            $handwork_materials = ORM::for_table('sys_items')->where('product_category', 'handwork_materials')->find_many();
            $ui->assign('handwork_materials', $handwork_materials); 
            $others = ORM::for_table('sys_items')->where('product_category', 'others')->find_many();
            $ui->assign('others', $others);

            $cloths = ORM::for_table('sys_cloths')->find_many();
            $ui->assign('cloths', $cloths);            
            $ui->assign('d',$d);

            // Fetch category employees
            $categoryEmployees = ORM::for_table('category_employee')->select('id')->select('name')->find_array();
            $ui->assign('category_employees', $categoryEmployees);
    
            // // Decode category pricing JSON
            $categoryPricing = json_decode($d->category_pricing, true) ?: [];
            $ui->assign('category_pricing', $categoryPricing);
            
            $ui->display('manage/edit-design.tpl');
        }
        else
        {
            echo 'not found';
        }

        break;

        case 'edit-form-stock':
            $id = $routes['2'];
            $d = ORM::for_table('sys_items')->find_one($id);
            if($d)
            {
                $ui->assign('d',$d);
                $ui->display('edit-ps-stock.tpl');
            }
            
        break;        

    case 'post':

        break;

    default:
        echo 'action not defined';
}