<style>

    .qrcode_box {

    width: 222px;

    height: 126px;

    

}



.qrcode_box img {

    width: 100px;

    float: left;

    margin-top: 6px;

    margin-left: 6px;

}



.qrcode_box span {

    position: relative;

    left: 10px;

    top: 43px;

    font-size: 14px;

    font-weight: 600 !important;

    float: left;

}

</style>







<?php

//this script will run only in demo mode



if($_app_stage != 'Demo'){

    //exit;

}



$action = route(1);



switch ($action){



    case 'admin':

        // auto login to admin





        Ib_Internal::autoLogin('demo@example.com','123456');





        break;



    case 'client':



        Ib_Internal::autoLogin('customer@example.com','123456','customer');



        break;

        

        case 'qrcode':

$i=0;

            foreach(glob('ui/lib/imgs/qrcode/*') as $filename){

                

                

                echo 

                '

                <div class="qrcode_box">

                <img src="'.$filename.'">

                <span>'.basename($filename).'</span>

                <br>

                </div>

                '

                ;

               $i++;

               if($i>=5) exit;

            }




        break;

        case 'getImg':
        $getReadyImg = file_get_contents('ui/lib/imgs/design/16603017370.jpg');
        var_dump($getReadyImg);
        break;

}



