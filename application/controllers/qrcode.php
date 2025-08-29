<style>

.qrcode_box {
    width: 150px;
    height: 375px;
    transform: rotate(-90deg);
    margin-left: 240px;
    margin-top: -200px;
    position: relative;
    top: 100px;
    text-align: center;
    left: -106px;
}
    
    /*.qrcode_box {*/
    /*       width: 155px;*/
    /*height: 375px;*/
    /*transform: rotate(-90deg);*/
    /*margin-left: 200px;*/
    /*margin-top: -90px;*/
    /*position:relative;*/
    /*top:50px;*/
    /*text-align:center;*/
    /*left:-90px;*/
    /*}*/
    
    /*    .qrcode_box {*/
    /*       width: 150px;*/
    /*height: 395px;*/
    /*transform: rotate(-90deg);*/
    /*margin-left: 100px;*/
    /*margin-top: -90px;*/
    /*position:relative;*/
    /*top:50px;*/
    /*left:-20px;*/
    /*text-align:center;*/
    /*}*/

.qrcode_box div:nth-child(2) {
    padding-top: 0px;
}
.qrcode_box div {
    font-size: 13px;
    font-family: system-ui;
    padding-top: 2px;
    letter-spacing: 0.5px;
     padding-left: 4px;
}
    .qrcode_box img {
        width: 50px;
        margin-left:auto;
        display:block;
        margin-right:auto;
    }

    .qrcode_box span {
        position: relative;
        left: 10px;
        top: 43px;
        font-size: 14px;
        font-weight: 600 !important;
        float: left;
    }
    .name_number
    {
        font-size: 24px !important;
    text-align: center;
    padding-bottom: 4px;
    }
    
    .cmp_name
    {
        text-align:center;
    }
    .price_font
    {
        font-size:24px !important;
        text-align:center;
    }

</style>

<?php

$action = route(1);

// switch ($action){    
//     case 'fetch':
//         $i=0;
//         $search = isset($_GET['search']) ? $_GET['search'] : null;
//         foreach(glob('ui/lib/imgs/qrcode/*') as $filename){

//             $image_name = explode('.', basename($filename));

//             if($search)
//             {
//                 if($search == basename($filename))
//                 {
//                     $param = explode('-', $image_name[0]);
//                     $type = $param[0];
//                     $id   = $param[1];
//                     //var_dump($param);
//                     if($type == 'P')
//                     {
//                          //product name & price
//                          $product = ORM::for_table('sys_items')->find_one($id);
//                          $name = $product['name'];
//                          $sales_price = $product['sales_price'];
//                     }
//                     else
//                     {
//                          //design name & price
//                          $design = ORM::for_table('sys_designs')->find_one($id);
//                          $name = $design['name'];
                         
//                          $fabrics   = json_decode($design['fabrics'], true);
//                          $stones    = json_decode($design['stones'], true);
//                          $handworks = json_decode($design['handworks'], true);
//                          $others    = json_decode($design['others'], true);

//                          $sales_price = array();

//                          foreach($fabrics as $row)
//                          {
//                             $pr = ORM::for_table('sys_items')->find_one($row['fabric_id']);
//                             $sales_price[] = $pr['sales_price'] * $row['fabric_qty'];
//                          }
//                          foreach($stones as $row)
//                          {
//                             $pr = ORM::for_table('sys_items')->find_one($row['stone_id']);
//                             $sales_price[]= $pr['sales_price'] * $row['stone_qty'];
//                          }
//                          foreach($handworks as $row)
//                          {
//                             $pr = ORM::for_table('sys_items')->find_one($row['handwork_id']);
//                             $sales_price[]= $pr['sales_price'] * $row['handwork_qty'];
//                          }
//                          foreach($others as $row)
//                          {
//                             $pr = ORM::for_table('sys_items')->find_one($row['other_id']);
//                             $sales_price[]= $pr['sales_price'] * $row['other_qty'];
//                          }   
                         
//                          $sales_price[]= $design['price'];

//                          $sales_price = array_sum($sales_price);
//                     }

//                     echo 
//                     '
//                         <div class="qrcode_box">
//                             <img src="'.$filename.'">
//                             <div>'.$image_name[0].'</div>
//                             <div>'.$name.'</div>
//                             <div>₹'.$sales_price.'</div>
//                             <br>
//                         </div>
//                     '
//                     ;                    
//                 }
//             }
//             else
//             {
//                 echo 
//                 ' 
//                     <div class="qrcode_box">
//                         <img src="'.$filename.'">
//                         <span>'.$image_name[0].'</span>
//                         <br>
//                     </div>
//                 '
//                 ;
//             }
//             $i++;
//         }
//     break;
// }

switch ($action){    
    case 'fetch':
        $i=0;
        $search = isset($_GET['search']) ? $_GET['search'] : null;
        foreach(glob('ui/lib/imgs/qrcode/*') as $filename){

            $image_name = explode('.', basename($filename));

            if($search)
            {
                if($search == basename($filename))
                {
                    $param = explode('-', $image_name[0]);
                    $type = $param[0];
                    $id   = $param[1];
                    if($type == 'P')
                    {
                        //product name & price
                        $product = ORM::for_table('sys_items')->find_one($id);
                        $name = $product['name'];
                        $sales_price = $product['sales_price'];
                         
                        echo 
                        '
                            <div class="qrcode_box">
                                <div class="name_number"><b>'.ucfirst($image_name[0]).'</b></div>
                                <div class="cmp_name"><b>'.$name.'</b></div>
                                <div class="price_font"><b>₹'.$sales_price.'</b></div>
                                <div><img src="'.$filename.'"></div>
                            </div>
                        ';                           
                    }
                    else
                    {
                         //design name & price
                         $design = ORM::for_table('sys_designs')->find_one($id);
                         $name = $design['name'];
                         
                         $fabrics   = json_decode($design['fabrics'], true);
                         $stones    = json_decode($design['stones'], true);
                         $handworks = json_decode($design['handworks'], true);
                         $others    = json_decode($design['others'], true);
                         $cloth     = ORM::for_table('sys_cloths')->find_one($design['cloth_id']);
                         $clothName = $cloth['name'];

                         $sales_price = array();
                         
                         $fabricNames = [];
                         foreach($fabrics as $row)
                         {
                            $pr = ORM::for_table('sys_items')->find_one($row['fabric_id']);
                            $sales_price[] = $pr['sales_price'] * $row['fabric_qty'];
                            $fabricNames[] = $pr['name'];
                         }
                         
                         $stoneNames = [];
                         foreach($stones as $row)
                         {
                            $pr = ORM::for_table('sys_items')->find_one($row['stone_id']);
                            $sales_price[]= $pr['sales_price'] * $row['stone_qty'];
                            $stoneNames[] = $pr['name'];
                         }
                         
                         $handworkNames = [];
                         foreach($handworks as $row)
                         {
                            $pr = ORM::for_table('sys_items')->find_one($row['handwork_id']);
                            $sales_price[]= $pr['sales_price'] * $row['handwork_qty'];
                            $handworkNames[] = $pr['name'];
                         }
                         foreach($others as $row)
                         {
                            $pr = ORM::for_table('sys_items')->find_one($row['other_id']);
                            $sales_price[]= $pr['sales_price'] * $row['other_qty'];
                         }   
                         
                         $sales_price[]= $design['price'];

                         $sales_price = array_sum($sales_price);
                         
                            echo 
                            '
                                <div class="qrcode_box">
                                    <div class="name_number"><b>'.ucfirst($image_name[0]).'</b></div>
                                    <div class="cmp_name" style="margin-top:13px;"><b>'.$name.'</b></div>
                                    <div><b>Product Type - </b><br>'.$clothName.'</div>
                                    <div><b>Fabric - </b><br>'.implode(',', $fabricNames).'</div>
                                    <div><b>Stone Color & Size - </b><br>'.implode(',', $stoneNames).'</div>
                                    <div><b>Handwork Materials - </b><br>'.implode(',', $handworkNames).'</div>
                                    <div class="price_font" style="margin-top:13px;"><b>₹'.$sales_price.'</b></div>
                                    <div class="qrcode_img"><img src="'.$filename.'"></div>
                                </div>
                            '
                        ;                           
                    }
                }
            }
            $i++;
        }
    break;
}

