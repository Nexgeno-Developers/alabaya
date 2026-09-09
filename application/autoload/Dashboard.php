<?php

Class Dashboard{

    private static function chartAmount($value) {
        if ($value === null || $value === '' || $value == '') {
            return '0.00';
        }
        $formatted = number_format((float)$value, 2, '.', '');
        $formatted = rtrim(rtrim($formatted, '0'), '.');
        return $formatted === '' ? '0.00' : $formatted;
    }

    public static function dataLastTwelveMonthsIncExp(){

        $months = array();

        for ($i = 1; $i <= 11; $i++) {
            $months[] = date("M Y", strtotime( date( 'Y-m-01' )." -$i months"));
        }

        $months = array_reverse($months);

        $months[12] = date("M Y", strtotime( date( 'Y-m-01' )));

        $inc = array();
        $exp = array();
        $m = array();
        $month_index = array();
        $range_start = null;
        $range_end = null;

        foreach($months as $month){

            $m[] = $month;
            $first_day_this_month = date("Y-m-d", strtotime("first day of $month"));
            $last_day_this_month = date("Y-m-d", strtotime("last day of $month"));
            $key = date('Y-n', strtotime($first_day_this_month));
            $month_index[$key] = count($m) - 1;
            if ($range_start === null || $first_day_this_month < $range_start) {
                $range_start = $first_day_this_month;
            }
            if ($range_end === null || $last_day_this_month > $range_end) {
                $range_end = $last_day_this_month;
            }
            $inc[] = '0.00';
            $exp[] = '0.00';

        }

        $rows = ORM::for_table('sys_transactions')->raw_query(
            "SELECT YEAR(`date`) AS y, MONTH(`date`) AS m, `type`, SUM(cr) AS cr, SUM(dr) AS dr
             FROM sys_transactions
             WHERE `date` BETWEEN ? AND ?
             GROUP BY YEAR(`date`), MONTH(`date`), `type`",
            array($range_start, $range_end)
        )->find_array();

        foreach ($rows as $row) {
            $key = $row['y'] . '-' . (int)$row['m'];
            if (!isset($month_index[$key])) {
                continue;
            }
            $idx = $month_index[$key];
            if ($row['type'] === 'Income') {
                $inc[$idx] = self::chartAmount($row['cr']);
            } elseif ($row['type'] === 'Expense') {
                $exp[$idx] = self::chartAmount($row['dr']);
            }
        }

        $data = array(
            'Months' => $m,
            'Income' => $inc,
            'Expense' => $exp
        );


        return $data;


    }


    public static function dataIncExpD($select){

        $inc = array();
        $exp = array();
        $day_keys = array();

        for ($d = 1; $d <= 31; $d++) {
            $day_keys[] = date('Y-m-' . str_pad((string)$d, 2, '0', STR_PAD_LEFT));
            $inc[] = '0.00';
            $exp[] = '0.00';
        }

        $range_start = min($day_keys);
        $range_end = max($day_keys);

        $rows = ORM::for_table('sys_transactions')->raw_query(
            "SELECT `date` AS d, `type`, SUM(cr) AS cr, SUM(dr) AS dr
             FROM sys_transactions
             WHERE `date` BETWEEN ? AND ?
             GROUP BY `date`, `type`",
            array($range_start, $range_end)
        )->find_array();

        $by_date = array();
        foreach ($rows as $row) {
            $by_date[$row['d']][$row['type']] = $row;
        }

        foreach ($day_keys as $i => $day_key) {
            if (isset($by_date[$day_key]['Income'])) {
                $inc[$i] = self::chartAmount($by_date[$day_key]['Income']['cr']);
            }
            if (isset($by_date[$day_key]['Expense'])) {
                $exp[$i] = self::chartAmount($by_date[$day_key]['Expense']['dr']);
            }
        }

        $data = array(
            'Income' => $inc,
            'Expense' => $exp
        );


        return $data;


    }


    public static function graphUpdate($user,$config){

        $ib_now = time();
        $ib_u_t = $config['ib_u_t'];
        $msg = '';


        $u_a_m = base64_decode('PGRpdiBjbGFzcz0iYWxlcnQgYWxlcnQtaW5mbyBmYWRlIGluIj48YnV0dG9uIGNsYXNzPSJjbG9zZSIgZGF0YS1kaXNtaXNzPSJhbGVydCIgaWQ9ImdfdG9wIj7DlzwvYnV0dG9uPkFuIFVwZGF0ZSBpcyBhdmFpbGFibGUuIEdvIHRvIFNldHRpbmdzIC0+IEFib3V0IHRvIFVwZGF0ZSBMYXRlc3QgVmVyc2lvbi48L2Rpdj4=');

/*         if($ib_u_t < $ib_now) {

            update_option('ib_u_t', $ib_now+86400);

            $raw = '';
            $p = base64_decode('cHVyY2hhc2VfY29kZQ==');

            $arr = array(
                'app_url' => APP_URL,
                'item_id' => 11021678,
                'fullname' => $user->fullname,
                'email' => $user->username,
                'build' => $config['build'],
                $p => $config[$p]
            );



            try{

                $raw = ib_http_request('http://dashboard.cloudonex.com/envato/jsonapi/version_check/','POST',$arr);


            } catch (Exception $e){


//            $msg = $e->getMessage();

            }

            $resp = json_decode($raw);

            if (json_last_error() === JSON_ERROR_NONE) {

                if(isset($resp->build)){

                    $remote_build = $resp->build;


                    if(($config['build']) < $remote_build){


                        $msg = $u_a_m;
                        update_option('ib_u_a', '1');


                    }

                }

            }
            else{

            }
        }
        elseif ($config['ib_u_a'] == '1'){
            $msg = $u_a_m;
        }
        else{

        } */

//        else{
//            $msg = 'Unable to Connect Update Server';
//        }


        $a = array(
            'msg' => $msg
        );


        return $a;





    }

    public static function dataIncVsExp($month=''){

        $mdate = date('Y-m-d');

        if($month == ''){
            $first_day_month = date('Y-m-01');
        }
        else{
            $first_day_month = $month;
        }

        $row = ORM::for_table('sys_transactions')->raw_query(
            "SELECT
                SUM(CASE WHEN `type` = 'Income' THEN cr ELSE 0 END) AS income,
                SUM(CASE WHEN `type` = 'Expense' THEN dr ELSE 0 END) AS expense
             FROM sys_transactions
             WHERE `date` >= ? AND `date` <= ?",
            array($first_day_month, $mdate)
        )->find_one();

        $mi = ($row && $row->income != '') ? self::chartAmount($row->income) : '0.00';
        $me = ($row && $row->expense != '') ? self::chartAmount($row->expense) : '0.00';


        return array(
            'Income' => $mi,
            'Expense' => $me
        );


    }



}
