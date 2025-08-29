<?php
_auth();
$ui->assign('_application_menu', 'ps');
$ui->assign('_title', $_L['Products n Services'].'- '. $config['CompanyName']);
$ui->assign('_st', $_L['Products n Services']);
$action = $routes['1'];
$user = User::_info();
$ui->assign('user', $user);
switch ($action) {

/*    case 'modal-list':
		
      $d = ORM::for_table('sys_items')->order_by_asc('name')->find_many();
			
        echo '
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>'.$_L['Products n Services'].'</h3>
</div>
<div class="modal-body">

<table class="table table-striped" id="items_table">
      <thead>
        <tr>
          <th width="10%">#</th>
          <th width="20%">'.$_L['Item Code'].'</th>
          <th width="20%">'.$_L['Item Name'].'</th>
          <th width="30%">Description</th>
          <th width="20%">'.$_L['Price'].'</th>
        </tr>
      </thead>
      <tbody>
       ';

        foreach($d as $ds){
           //$price = number_format($ds['sales_price'],2,$config['dec_point'],$config['thousands_sep']);
					 $price = $ds['sales_price'];
            echo ' <tr>
          <td><input type="checkbox" class="si" value="'.$ds['id'].'"><input type="hidden" class="pid" value="'.$ds['product_id'].'"></td>
          <td>'.$ds['item_number'].'</td>
          <td>'.$ds['name'].'</td>
          <td>'.$ds['description'].'</td>
          <td class="price">'.$price.'</td>
        </tr>';
        }

        echo '

      </tbody>
    </table>

</div>
<div class="modal-footer">

	<button type="button" data-dismiss="modal" class="btn">'.$_L['Close'].'</button>
	<button class="btn btn-primary update">'.$_L['Select'].'</button>
</div>';

		//$ui->assign('nxt',$nxt);
      //$ui->display('add-ps.tpl');
			
        break;*/

        case 'modal-list':
		
        $customize = ORM::for_table('sys_items')->where('product_type', 'customize')->order_by_asc('name')->find_many();
        $ui->assign('p_customize', $customize);    

        $readymade = ORM::for_table('sys_items')->where('product_type', 'readymade')->order_by_asc('name')->find_many();
        $ui->assign('p_readymade', $readymade);         

  
        $ui->display('product-modal-list-ps.tpl');
              
          break;

		
		case 'product-list':

        $lists = ORM::for_table('sys_items')->order_by_asc('name')->find_many();
				$listopt = "";
				foreach ($lists as $list) {
          $listopt .= '<option value=' . $list['id'] . '>' . $list['name'] .' </option>';
        } 
        
				//var_dump($out);
				echo $listopt;

        break;

		case 'product-details' :
				$id = _post('id');
				
        $items = ORM::for_table('sys_items')->select('id')->select('name')->select('sales_price')->select('item_number')->find_one($id);

				$out = array(
					 'id' => utf8_encode($items['id']), 
					 'name' => utf8_encode($items['name']), 
					 'sales_price' => utf8_encode($items['sales_price']), 
					 'item_number' => utf8_encode($items['item_number']), 
				);

				echo json_encode($out);

        break;


    case 'p-new':



        $ui->assign('type','Product');
        //$ui->assign('xfooter', Asset::js(array('numeric','jslib/add-ps')));
        //$ui->assign('xjq', '$(\'.amount\').autoNumeric(\'init\');');

        $css_arr = array('s2/css/select2.min');
        $js_arr = array('s2/js/select2.min','numeric','jslib/add-ps');
        $ui->assign('xjq', '$(\'.amount\').autoNumeric(\'init\');');   
        
        $ui->assign('xheader', Asset::css($css_arr));
        $ui->assign('xfooter', Asset::js($js_arr)); 
        
        $vendorList = ORM::for_table('crm_accounts')->where('gid', 2)->find_many();
        $ui->assign('vendorList',$vendorList);
        
        $max = ORM::for_table('sys_items')->max('id');
        $nxt = $max+1;
        $ui->assign('nxt',$nxt);
        $ui->display('add-ps.tpl');



        break;


    case 's-new':


        $ui->assign('type','Service');
        $ui->assign('xfooter', Asset::js(array('numeric','jslib/add-ps')));

        $ui->assign('xjq', '
 $(\'.amount\').autoNumeric(\'init\');
 ');

        $max = ORM::for_table('sys_items')->max('id');
        $nxt = $max+1;
        $ui->assign('nxt',$nxt);
        $ui->display('add-ps.tpl');



        break;


    case 'add-post':
        $name = _post('name');
        $sales_price = Finance::amount_fix(_post('sales_price'));
        $item_number = _post('item_number');
        $description = _post('description');
        $product_type = _post('product_type');
        $purchase_price = Finance::amount_fix(_post('purchase_price'));
        $product_category = (isset($_POST['product_category'])) ? _post('product_category') : null;
        $product_stock = _post('product_stock');
        $product_stock_type = _post('product_stock_type');
        $type = _post('type');
        $vendor_id = _post('vendor_id');
        //$product_image = _post('product_image');

        $msg = '';
       
        if($name == ''){
            $msg .= 'Item Name is required <br>';
        }
        /*if($_POST['design_id'] == ''){
            $msg .= 'Design is required for readymade product<br>';
        }*/        
        if($sales_price == ''){
            $msg .= 'Sale price is required <br>';
        }
        if($purchase_price == ''){
            $msg .= 'Purchase price is required <br>';
        }
        if($product_stock == ''){
            $msg .= 'Stock is required <br>';
        }
        if($product_stock_type == ''){
            $msg .= 'Stock Unit is required <br>';
        }  
        
        if($product_type == 'customize'){
            if($vendor_id == ''){
                $msg .= 'Vendor field is required <br>';
            }
            if($product_category == ''){
                $msg .= 'Product Category is required <br>';
            }
        }
        
        if($msg == ''){
            $d = ORM::for_table('sys_items')->create();
            $d->name = $name;
            $d->sales_price = $sales_price;
            $d->item_number = $item_number;
            $d->description = $description;
            $d->added = date('Y-m-d');
            $d->type = $type;
            $d->product_type = $product_type;
            $d->purchase_price = $purchase_price;
            $d->product_category = $product_category;
            $d->product_stock = $product_stock;
            $d->product_stock_type = $product_stock_type;
            $d->design_id = $_POST['design_id'];

            if($_FILES['product_image']["name"])
            {
                $d->product_image = 'ui/lib/imgs/product/'.time().'.jpg';
                move_uploaded_file($_FILES['product_image']["tmp_name"], $d->product_image);
            }   
            
            if(!empty($_POST['ready_img']))
            {
                //$source = 'http://alabaya.mbills.in/'.$_POST['ready_img'];
                $d->product_image = $_POST['ready_img'];
                //file_put_contents($d->product_image, file_get_contents($source));
            }            


            $d->save();
            $id = $d->id(); 
            
            if($product_type == 'customize'){
                stock_record($id, $d->product_stock, 'credit', '', '', $vendor_id, $d->purchase_price);
            }else{
                stock_record($id, $d->product_stock, 'credit');
            }
            

            //deduct substock start
            $design_id = $_POST['design_id'];
            if(!empty($design_id)){
                $design    = ORM::for_table('sys_designs')->find_one($design_id);

                $fabrics   = json_decode($design['fabrics'], true);
                $stones    = json_decode($design['stones'], true);
                $handworks = json_decode($design['handworks'], true);
                $others    = json_decode($design['others'], true);

                $sub_product_ids = array();
                $sub_product_qty = array();

                foreach($fabrics as $row)
                {
                    $sub_product_ids[] = $row['fabric_id'];
                    $sub_product_qty[] = $row['fabric_qty']*$product_stock;          
                }
                foreach($stones as $row)
                {
                    $sub_product_ids[] = $row['stone_id'];
                    $sub_product_qty[] = $row['stone_qty']*$product_stock;          
                }  
                foreach($handworks as $row)
                {
                    $sub_product_ids[] = $row['handwork_id'];
                    $sub_product_qty[] = $row['handwork_qty']*$product_stock;          
                }  
                foreach($others as $row)
                {
                    $sub_product_ids[] = $row['other_id'];
                    $sub_product_qty[] = $row['other_qty']*$product_stock;          
                }
                
                $p = 0;
                foreach($sub_product_ids as $product_id)
                {
                    stock_record($product_id, $sub_product_qty[$p], 'debit', '', $id);
                    $p++;
                }
                //deduct substock end  
                //$d->design_id = $design_id;
            }

            _msglog('s',$_L['Item Added Successfully']);
            echo $id;
        }
        else{
            echo $msg;
        }
        break;

        case 'view':
            $id  = $routes['2'];
            //$stock = json_decode(product_stock_info($id, true));
            $item = ORM::for_table('sys_items')->find_one($id);
            $credited_stock = ORM::for_table('sys_items_stock')->where('item_id', $id)->where('type', 'credit')->find_many();
            $debited_stock = ORM::for_table('sys_items_stock')->where('item_id', $id)->where('type', 'debit')->find_many();
            //var_dump($sys_invoiceitems);
            $ui->assign('_title', 'Stock');
            $ui->assign('_st', 'Stock History');      
            $ui->assign('p_name', $item['name']);  
            $ui->assign('id', $id);
            $ui->assign('item', $item);
            $ui->assign('credited_stock', $credited_stock);
            $ui->assign('debited_stock', $debited_stock);
            $ui->display('ps-view.tpl');
        break;

    case 'view1':
//        $id  = $routes['2'];
//        $d = ORM::for_table('sys_items')->find_one($id);
//        if($d){
//
//            //find all activity for this user
//            $ac = ORM::for_table('sys_activity')->where('cid',$id)->limit(20)->order_by_desc('id')->find_many();
//            $ui->assign('ac',$ac);
//            $ui->assign('countries',Countries::all($d['country']));
//
//            $ui->assign('xheader', '
//<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/select2/select2.css"/>
//
//');
//            $ui->assign('xfooter', '
//<script type="text/javascript" src="' . $_theme . '/lib/select2/select2.min.js"></script>
//<script type="text/javascript" src="' . $_theme . '/lib/profile.js"></script>
//
//');
//
//            $ui->assign('xjq', '
// $("#country").select2();
//
// ');
//            $ui->assign('d',$d);
//            $ui->display('ps-view.tpl');
//
//        }
//        else{
//         //   r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
//
//        }

        break;

    case 'p-list':
        //$paginator = Paginator::bootstrap('sys_items','type','Product');
        $product_type = (!empty($_GET['product_type'])) ? $_GET['product_type'] : 'readymade';
        //var_dump($product_type);
        $d = ORM::for_table('sys_items')->where('type','Product')->where('product_type', $product_type)->order_by_desc('id')->find_many();
        $ui->assign('d',$d);
        $ui->assign('product_type',$product_type);
        $ui->assign('type','Product');
        //$ui->assign('paginator',$paginator);
        $ui->assign('xheader', '
					<link rel="stylesheet" type="text/css" href="' . $_theme . '/css/modal.css"/>');
        $ui->assign('xfooter', '
        <script type="text/javascript" src="' . $_theme . '/lib/modal.js"></script>
					<script type="text/javascript" src="' . $_theme . '/lib/ps-list.js"></script>');
        $ui->display('ps-list.tpl');
        break;

    case 's-list':

        $paginator = Paginator::bootstrap('sys_items','type','Service');
        $d = ORM::for_table('sys_items')->where('type','Service')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('id')->find_many();
        $ui->assign('d',$d);
        $ui->assign('type','Service');
        $ui->assign('paginator',$paginator);
        $ui->assign('xheader', '
					<link rel="stylesheet" type="text/css" href="' . $_theme . '/css/modal.css"/>');
        $ui->assign('xfooter', '
                <script type="text/javascript" src="' . $_theme . '/lib/modal.js"></script>
								<script type="text/javascript" src="' . $_theme . '/lib/ps-list.js"></script>');
        $ui->display('ps-list.tpl');
        break;

    case 'edit-post':
        $msg = '';
        $id   = _post('id');
        $name = _post('name');
        $sales_price = Finance::amount_fix(_post('sales_price'));
        $item_number = _post('item_number');
        $description = _post('description');
        $product_type = _post('product_type');
        $purchase_price = Finance::amount_fix(_post('purchase_price'));
        $product_category = (isset($_POST['product_category'])) ? _post('product_category') : null;
        $product_stock = _post('product_stock');
        $product_stock_type = _post('product_stock_type');

        if($name == ''){
            $msg .= 'Item Name is required <br>';
        }
        if($sales_price == ''){
            $msg .= 'Sale price is required <br>';
        }
        if($purchase_price == ''){
            $msg .= 'Purchase price is required <br>';
        }
        if($product_stock == ''){
            $msg .= 'Stock is required <br>';
        }
        if($product_stock_type == ''){
            $msg .= 'Stock Unit is required <br>';
        }

        if($msg == ''){
            $d = ORM::for_table('sys_items')->find_one($id);
            if($d){
                $d->name = $name;
                $d->sales_price = $sales_price;
                $d->item_number = $item_number;
                $d->description = $description;
                $d->product_type = $product_type;
                $d->purchase_price = $purchase_price;
                $d->product_category = $product_category;
                $d->product_stock = $product_stock;
                $d->product_stock_type = $product_stock_type;
                
                if($_FILES['product_image']["name"])
                {
                    $old = $d->product_image;
                    $d->product_image = 'ui/lib/imgs/product/'.time().'.jpg';
                    move_uploaded_file($_FILES['product_image']["tmp_name"], $d->product_image);
                    unlink($old);
                }
                $d->save();
                echo $d->id();
            }
            else{
                echo 'Not Found';
            }
        }
        else{
            echo $msg;
        }

        break;


        case 'edit-stock-post':
        $msg            = '';
        $id             = _post('id');
        $vendor_id      = _post('vendor_id');
        $purchase_price = _post('purchase_price');
        $product_stock  = _post('product_stock');
        $product_type   = _post('product_type');
        
        if($product_type == 'customize'){
            if(empty($vendor_id)){
                $msg .= '<p>Vendor is required</p>';
            }
            if(empty($purchase_price)){
                $msg .= '<p>Purchase Price is required</p>';
            }
            if(empty($product_stock)){
                $msg .= '<p>Stock is required</p>';
            }            
        }
        
        if($msg == ''){
            $d = ORM::for_table('sys_items')->find_one($id);
            if($d){
                $design_id = $d->design_id;
                $d->product_stock = $d->product_stock + $product_stock;
                $d->save();

                if($product_stock > 0)
                {
                    //stock_record($id, abs($product_stock), 'credit');
                    stock_record($id, abs($product_stock), 'credit', '', '', $vendor_id, $purchase_price);
                }
                else
                {
                    //stock_record($id, abs($product_stock), 'debit');
                    stock_record($id, abs($product_stock), 'debit', '', '', $vendor_id, $purchase_price);
                }                



                $main_product_id = $id;
                $design_id       = $design_id;

                if(!empty($design_id)){
                    $design    = ORM::for_table('sys_designs')->find_one($design_id);
        
                    $fabrics   = json_decode($design['fabrics'], true);
                    $stones    = json_decode($design['stones'], true);
                    $handworks = json_decode($design['handworks'], true);
                    $others    = json_decode($design['others'], true);
        
                    $sub_product_ids = array();
                    $sub_product_qty = array();
        
                    foreach($fabrics as $row)
                    {
                        $sub_product_ids[] = $row['fabric_id'];
                        $sub_product_qty[] = $row['fabric_qty']*$product_stock;          
                    }
                    foreach($stones as $row)
                    {
                        $sub_product_ids[] = $row['stone_id'];
                        $sub_product_qty[] = $row['stone_qty']*$product_stock;          
                    }  
                    foreach($handworks as $row)
                    {
                        $sub_product_ids[] = $row['handwork_id'];
                        $sub_product_qty[] = $row['handwork_qty']*$product_stock;          
                    }  
                    foreach($others as $row)
                    {
                        $sub_product_ids[] = $row['other_id'];
                        $sub_product_qty[] = $row['other_qty']*$product_stock;          
                    }
                    
                    $p = 0;
                    foreach($sub_product_ids as $product_id)
                    {
                        /*$stock  = ORM::for_table('sys_items_stock')->where('item_id', $product_id)->where('parent_item_id', $main_product_id)->find_one();
                        $stock->stock = $stock->stock + ($sub_product_qty[$p]*$product_stock);
                        $stock->save();*/

                        if($product_stock < 0)
                        {
                            stock_record($product_id, abs($sub_product_qty[$p]), 'credit', '', $main_product_id);
                        }
                        else
                        {
                            stock_record($product_id, abs($sub_product_qty[$p]), 'debit', '', $main_product_id);
                        }


                        $p++;
                    }
                }


                echo $d->id();
            }
            else{
                echo 'Not Found';
            }
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
        $d = ORM::for_table('sys_items')->find_one($id);
        if($d){
            $price = number_format(($d['sales_price']),2,$config['dec_point'],$config['thousands_sep']);
            
            if($d['type2'] == 'onetime'){
                $onetime = 'selected';
                $recurring = '';
            }elseif($d['type2'] == 'recurring'){
                $onetime = '';
                $recurring = 'selected';
            }
            $ui->assign('d',$d);
            $ui->display('edit-ps.tpl');
            /*echo '
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>'.$_L['Edit'].'</h3>
</div>
<div class="modal-body">

<form class="form-horizontal" role="form" id="edit_form" method="post">

<div class="form-group">
<label class="col-lg-2 control-label" for="name">Service Type</label>
<div class="col-lg-10">
     <select name="service_type" class="form-control" id="service_type">
         <option value="">--Select--</option>
         <option value="onetime" '.$onetime.'>Onetime</option>
         <option value="recurring" '.$recurring.'>Recurring</option>
     </select>			
</div>
</div> 

  <div class="form-group">
    <label for="name" class="col-sm-2 control-label">'.$_L['Name'].'</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" value="'.$d['name'].'" name="name" id="name">
    </div>
  </div>
  <div class="form-group">
    <label for="rate" class="col-sm-2 control-label">'.$_L['Item Number'].'</label>
    <div class="col-sm-2">
      <input type="text" class="form-control" name="item_number" value="'.$d['item_number'].'" id="item_number">
      <input type="hidden" name="id" value="'.$d['id'].'">
    </div>
  </div>
  <div class="form-group">
    <label for="rate" class="col-sm-2 control-label">'.$_L['Price'].'</label>
    <div class="col-sm-2">
      <input type="text" class="form-control" name="price" value="'.$price.'" id="price">
      <input type="hidden" name="id" value="'.$d['id'].'">
    </div>
  </div>
    <div class="form-group">
    <label for="name" class="col-sm-2 control-label">'.$_L['Description'].'</label>
    <div class="col-sm-10">
      <textarea id="description" name="description" class="form-control" rows="3">'.$d['description'].'</textarea>
    </div>
  </div>
</form>

</div>
<div class="modal-footer">

	<button type="button" data-dismiss="modal" class="btn">'.$_L['Close'].'</button>
	<button id="update" class="btn btn-primary">'.$_L['Update'].'</button>
</div>';*/
        }
        else{
            echo 'not found';
        }



        break;

        case 'edit-form-stock':
            $id = $routes['2'];
            $d = ORM::for_table('sys_items')->find_one($id);
            $vendorList = ORM::for_table('crm_accounts')->where('gid', 2)->find_many();
            if($d)
            {
                $ui->assign('d',$d);
                $ui->assign('vendorList',$vendorList);
                $ui->display('edit-ps-stock.tpl');
            }
            
        break;        

		case 'get_tax_opt':
        $taxes = ORM::for_table('sys_tax')->order_by_asc('rate')->find_many();
        $tax_opts = "<optgroup label=GST>";
        foreach ($taxes as $tax) {
					if($tax['taxtype']=='GST'){
						$tax_opts .= '<option value="' . $tax['id'] . '">' . $tax['name'] ." ". $tax['rate'] ." %" .'</option>';
					}
				} 
				$tax_opts .= '</optgroup>';
				$tax_opts .= "<optgroup label=IGST>";
        foreach ($taxes as $tax) {
					if($tax['taxtype']=='IGST'){
						$tax_opts .= '<option value="' . $tax['id'] . '">' . $tax['name'] ." ". $tax['rate'] ." %" .'</option>';
					}
				} 
				$tax_opts .= '</optgroup>';
				echo $tax_opts;

        break;

    case 'post':

        break;

        case 'get-design-subproduct-amount':

            $design_id = $_POST['design_id'];
            $design    = ORM::for_table('sys_designs')->find_one($design_id);

            $fabrics   = json_decode($design['fabrics'], true);
            $stones    = json_decode($design['stones'], true);
            $handworks = json_decode($design['handworks'], true);
            $others    = json_decode($design['others'], true);

            $sub_product_ids = array();
            $sub_product_qty = array();

            foreach($fabrics as $row)
            {
                $sub_product_ids[] = $row['fabric_id'];
                $sub_product_qty[] = $row['fabric_qty'];          
            }
            foreach($stones as $row)
            {
                $sub_product_ids[] = $row['stone_id'];
                $sub_product_qty[] = $row['stone_qty'];          
            }  
            foreach($handworks as $row)
            {
                $sub_product_ids[] = $row['handwork_id'];
                $sub_product_qty[] = $row['handwork_qty'];          
            }  
            foreach($others as $row)
            {
                $sub_product_ids[] = $row['other_id'];
                $sub_product_qty[] = $row['other_qty'];          
            }
            
            $p = 0;
            $purchase_price = 0;
            $sale_price     = 0;
            foreach($sub_product_ids as $product_id)
            {
                $product = ORM::for_table('sys_items')->find_one($product_id);
                $purchase_price += $product['purchase_price']*$sub_product_qty[$p]; 
                $sale_price += $product['sales_price']*$sub_product_qty[$p]; 

                /*echo '<pre>';
                var_dump($product['purchase_price']);
                var_dump($sub_product_qty[$p]);
                echo '<br><br>';
                var_dump($product['sales_price']);
                var_dump($sub_product_qty[$p]);
                echo '</pre>';*/

                $p++;
            }

            $sale_price += $design['price'];

            echo json_encode(array('sale_p' => $sale_price, 'purchase_p' => $purchase_price, 'img' => json_decode($design['image'], true)[0]));
    
        break;


    default:
        echo 'action not defined';
}