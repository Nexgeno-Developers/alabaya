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
        
        if(!has_access($user->roleid, 'products_n_services') || $user->roleid != 0) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }

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
        if($user->roleid != 0){
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }
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
        if(!has_access($user->roleid, 'products_n_services')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }
        $cloths = ORM::for_table('sys_cloths')->select('id')->select('name')->order_by_asc('name')->find_array();
        $ui->assign('cloths', $cloths);
        $ui->assign('xheader', Asset::css(['datatables.min', 'buttons.dataTables.min', 'modal']));
        $ui->assign('xfooter', Asset::js(['datatables.min', 'dataTables.buttons.min', 'buttons.print.min', 'modal']));
        $ui->assign('xfooter2', '<script type="text/javascript" src="' . $_theme . '/lib/design-list.js"></script>');
        $ui->display('manage/list-design.tpl');
        break;

    case 'list-design-datatable':
        if(!has_access($user->roleid, 'products_n_services')) {
            header('Content-Type: application/json');
            echo json_encode(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
            break;
        }

        $request = $_REQUEST;

        $columns = [
            0 => 'd.id',
            1 => 'd.name',
            2 => 'c.name',
            3 => 'd.price',
            4 => 'd.created_at'
        ];

        $length = isset($request['length']) ? (int)$request['length'] : 25;
        $start  = isset($request['start']) ? max(0, (int)$request['start']) : 0;
        $order_index = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 0;
        $order_col = isset($columns[$order_index]) ? $columns[$order_index] : 'd.id';
        $order_dir = (isset($request['order'][0]['dir']) && strtolower($request['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';

        $totalData = (int) ORM::for_table('sys_designs')->count();

        $base_q = ORM::for_table('sys_designs')->table_alias('d')
            ->select('d.*')
            ->select('c.name', 'cloth_name')
            ->left_outer_join('sys_cloths', ['d.cloth_id', '=', 'c.id'], 'c');

        // filters
        if (!empty($request['cloth_id'])) {
            $base_q->where('d.cloth_id', $request['cloth_id']);
        }

        if (!empty($request['min_price'])) {
            $base_q->where_gte('d.price', Finance::amount_fix($request['min_price']));
        }

        if (!empty($request['max_price'])) {
            $base_q->where_lte('d.price', Finance::amount_fix($request['max_price']));
        }

        if (!empty($request['search']['value'])) {
            $s = '%' . $request['search']['value'] . '%';
            $base_q->where_raw('(d.name LIKE ? OR d.description LIKE ? OR c.name LIKE ?)', [$s, $s, $s]);
        }

        if (!empty($request['design_name'])) {
            $n = '%' . $request['design_name'] . '%';
            $base_q->where_like('d.name', $n);
        }

        $count_q = clone $base_q;
        $totalFiltered = (int) $count_q->count();

        $data_q = clone $base_q;
        $data_q->order_by_expr($order_col . ' ' . $order_dir);
        if ($length != -1) {
            $data_q->offset($start)->limit($length);
        }

        $rows = $data_q->find_array();

        $data = [];
        $serial = $start + 1;
        foreach ($rows as $r) {
            $images = json_decode($r['image'], true);
            $first_img = (is_array($images) && !empty($images[0])) ? $images[0] : '';
            $img_link = $first_img ? '<a target="_blank" href="'.$first_img.'">View</a>' : '-';

            $qr_image = qrcode_generate('D-' . $r['id']);
            $qr_link = '<a target="_blank" href="'. U .'qrcode/fetch&search='.basename($qr_image).'">View</a>';

            $actions = '<a href="'. U .'manage/view/'. $r['id'] .'" class="btn btn-success btn-xs"><i class="fa fa-bar-chart"></i> History</a> ';
            if($user->roleid == 0){
                $actions .= '<a href="#" class="btn btn-primary btn-xs cedit" data-id="'.$r['id'].'"><i class="fa fa-pencil"></i> Edit</a> ';
                $actions .= '<a href="#" class="btn btn-danger btn-xs cdelete cdelete-design" data-id="'.$r['id'].'"><i class="fa fa-trash"></i> Delete</a>';
            }

            $data[] = [
                $serial,
                htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'),
                !empty($r['cloth_name']) ? htmlspecialchars($r['cloth_name'], ENT_QUOTES, 'UTF-8') : '-',
                number_format((float)$r['price'], 2, '.', ''),
                $img_link,
                $qr_link,
                $actions
            ];
            $serial++;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'draw' => intval($request['draw'] ?? 0),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data
        ]);

        break;


    case 'edit-post':
        if($user->roleid != 0){
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }

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

    case 'ajax-delete':
        header('Content-Type: application/json; charset=utf-8');

        if($user->roleid != 0){
            echo json_encode(['success' => false, 'message' => $_L['You do not have permission']]);
            break;
        }

        $id = _post('id');
        if(empty($id)){
            echo json_encode(['success' => false, 'message' => 'Invalid design id']);
            break;
        }

        $design = ORM::for_table('sys_designs')->find_one($id);
        if(!$design){
            echo json_encode(['success' => false, 'message' => 'Design not found']);
            break;
        }

        $images = json_decode($design->image, true);
        if(is_array($images)){
            foreach($images as $img){
                if(!empty($img) && file_exists($img)){
                    @unlink($img);
                }
            }
        }

        $design->delete();

        _log('Design Deleted: '.$design->name.' [ID: '.$id.']','Admin',$user['id']);

        echo json_encode(['success' => true, 'message' => 'Design deleted successfully']);
        break;

    default:
        echo 'action not defined';
}
