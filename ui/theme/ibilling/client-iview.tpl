<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{$_L['INVOICE']} - {$d['invoicenum']}{if $d['cn'] neq ''} {$d['cn']} {else} {$d['id']} {/if}</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{$app_url}application/storage/icon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="{$app_url}application/storage/icon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="{$app_url}application/storage/icon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="{$app_url}application/storage/icon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="{$app_url}application/storage/icon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="{$app_url}application/storage/icon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="{$app_url}application/storage/icon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="{$app_url}application/storage/icon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="{$app_url}application/storage/icon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="{$app_url}application/storage/icon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{$app_url}application/storage/icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="{$app_url}application/storage/icon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{$app_url}application/storage/icon/favicon-16x16.png">
    <link rel="manifest" href="{$app_url}application/storage/icon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{$app_url}application/storage/icon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link href="{$_theme}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{$_theme}/lib/fa/css/font-awesome.min.css" rel="stylesheet">
    <link href="{$_theme}/lib/icheck/skins/all.css" rel="stylesheet">
    <link href="{$app_url}ui/lib/css/animate.css" rel="stylesheet">
    <link href="{$_theme}/css/style.css" rel="stylesheet">
    <link href="{$_theme}/css/custom.css" rel="stylesheet">

    {if $_c['rtl'] eq '1'}
        <link href="{$_theme}/css/bootstrap-rtl.min.css" rel="stylesheet">
        <link href="{$_theme}/css/style-rtl.min.css" rel="stylesheet">
    {/if}

    {if isset($xheader)}
        {$xheader}
    {/if}
    <style type="text/css">
        body {

            background-color: #FAFAFA;
            overflow-x: visible;
        }
        .paper {
            margin: 50px auto;
            width: 980px;
            border: 2px solid #DDD;
            background-color: #FFF;
            position: relative;
            width: 980px;
        }
    </style>
</head>

<body class="fixed-nav">

<div class="paper">
	<section class="panel">
		<div class="panel-body">
			<div class="invoice">
				{if isset($notify)}
						{$notify}
				{/if}
				<header class="clearfix">
					<div class="row">
						<div class="col-sm-6 mt-md">
							<h2 class="h2 mt-none mb-sm text-dark text-bold">{$_L['INVOICE']}</h2>
							<h4 class="h4 m-none text-dark text-bold">#{$d['invoicenum']}</h4>
							{if $d['status'] eq 'Unpaid'}
									<h3 class="alert alert-danger">{$_L['Unpaid']}</h3>
									{elseif $d['status'] eq 'Paid'}
									<h3 class="alert alert-success">{$_L['Paid']}</h3>
							{elseif $d['status'] eq 'Partially Paid'}
									<h3 class="alert alert-info">{$_L['Partially Paid']}</h3>
									{else}
									<h3 class="alert alert-info">{$d['status']}</h3>
							{/if}
							<div class="bill-to" style="font-size:12px;">
								<p class="h5 mb-xs text-dark text-semibold"><strong>{$_L['Invoiced To']}</strong></p>
								<address style="margin:0px">
										{if $a['company'] neq ''}
												<strong>{$a['company']}</strong>
												<br>
												<strong>{$_L['ATTN']}</strong>: {$d['account']}
												<br>
										{else}
												{$d['account']}
												<br>
										{/if}
										{$a['address']} <!--<br>
										{$a['city']} <br>
										{$a['state']} - {$a['zip']} <br>
										{$a['country']}-->
										<br>
										<strong>{$_L['Phone']}:</strong> {$a['phone']}
										<br>
										<!--<strong>{$_L['Email']}:</strong> {$a['email']}-->
										{foreach $cf as $cfs}
												<br>
												<strong>{$cfs['fieldname']}: </strong> {get_custom_field_value($cfs['id'],$a['id'])}
										{/foreach}<br>
										<!--<strong>{$_L['gst']}:</strong> {$a['gst_no']}-->
										{$x_html}
								</address>
								<h3 class="alert alert-info" style="color: #383d41;background-color: #e2e3e5;border-color: #d6d8db;">Invoice Status : {$d['delivery_status']}</h3>
							</div>
						</div>
						<div class="col-sm-6 text-right mt-md mb-md">
							<div class="ib">
									<img width="100px" src="{$app_url}application/storage/system/{$comp['company_logo']}" alt="Logo">
							</div>
							<address class="ib mr-xlg">
											<table width="100%">			<tr>				<td style="border: 0; text-align: left" width="62%"><div id="logo" style="font-size:12px; margin-top:13%;">					<strong>Invoice From:</strong> <br><br>					<strong>{$comp['account']}</strong><br>					{$comp['address']}<br>					<strong>Phone : </strong>{$comp['contact_phone']}<br>					<strong>Email : </strong>{$comp['email']}<br>					<!--<strong>GSTIN : </strong>{$comp['gstin']}<br>-->					<strong>Account Name : </strong>{get_type_by_id('sys_accounts', 'id', $d['company_id'], 'account')}<br>					<strong>Account No : </strong>{get_type_by_id('sys_accounts', 'id', $d['company_id'], 'account_number')}<br>					<strong>IFSC Code : </strong>{get_type_by_id('sys_accounts', 'id', $d['company_id'], 'ifsc')}					</div>				</td>						</tr>		</table>
							</address>
						</div>
					</div>
				</header>
					<div class="bill-info">
						<div class="row">
							<div class="col-md-6">
								<h2> {$_L['Invoice Total']}: {ib_money_format($d['subtotal'],$_c,$d['currency_symbol'])} </h2>
											{if ($d['credit']) neq '0.00'}
													<h2> {$_L['Total Paid']}: {ib_money_format($d['credit'],$_c,$d['currency_symbol'])}</h2>
													<h2> {$_L['Amount Due']}: {ib_money_format($i_due,$_c,$d['currency_symbol'])}</h2>
											{/if}
											{if (($d['status']) neq 'Paid') AND (ib_pg_count() neq '0' AND (($d['status']) neq 'Cancelled'))}	
							</div>
							<div class="col-md-6">
								<div class="bill-data text-right">
									<p class="mb-none">
											<span class="text-dark">{$_L['Invoice Date']}</span>
											<span class="value">{date( $_c['df'], strtotime($d['date']))}</span>
									</p>
									<p class="mb-none">
											<span class="text-dark">Delivery Date:</span>
											<span class="value">{date( $_c['df'], strtotime($d['duedate']))}</span>
									</p>
									<!--<input type="hidden" name="amount" value="{$i_due}">-->
									<form class="form-inline" method="post" action="{$_url}client/ipay/{$d['id']}/token_{$d['vtoken']}">
										<input placeholder="Enter Amount" type="number" min="500" max="{$i_due}" step="0.01" class="no-spinners form-control" name="amount" value="{$i_due}" name="amount" value="{$i_due}">
										<input type="hidden" name="firstname" value="{$a['account']}">
										<input type="hidden" name="email" value="bills@abayadesigner.com">
										<input type="hidden" name="phone" value="{$a['phone']}">
										<input type="hidden" name="productinfo" value="{$d['invoicenum']}@{$d['id']}">
										<input type="hidden" name="surl" value="{$_url}client/ipay_success">
										<input type="hidden" name="furl" value="{$_url}client/ipay_cancel">
										<input type="hidden" name="txnid" value="{$txnid}">
										{if ($d['company_id']) eq '1'}
										<input type="hidden" name="key" value="ByhijwgB">
										
										{else}
										<input type="hidden" name="key" value="ByhijwgB">
										{/if}
											<div class="form-group has-success">
										
											</div>
											<button type="submit" class="btn btn-info ml-sm"><i class="fa fa-credit-card"></i> {$_L['Pay Now']}</button>
									</form>
											{/if}
											{*<a href="{$_url}client/ipay/{$d['id']}/token_{$d['vtoken']}" class="btn btn-info ml-sm"><i class="fa fa-credit-card"></i> Pay Now</a>*}
								</div>
							</div>
						</div>
					</div>

					{if $d['d_measure'] eq 'yes'}
					<div class="invoice_tb">			<h3>Measurements :</h3>
											
											
<table class="table">
	<tbody>
	{foreach json_decode($a['measurements']) as $key => $val}
	<tr>
		<th >{ucfirst($key)}</th>
	<td>{$val} </td>
	</tr>
	{/foreach}
	</tbody>
</table>

<div>
		<h3>Files :</h3>
		{foreach json_decode($d['additional_imgs']) as $val}
		<img data-img="{$val}" src="{$val}" width="50px" height="50px" class="img-popup">
		{/foreach}
	</div>
	<br>
					</div>
					{/if}

					<div class="table-responsive">
						<table class="table table-bordered invoice-items">
							<thead>
								<tr class="h4 text-dark">
									<th rowspan="2" id="cell-item" class="text-semibold">#</th>
									<th rowspan="2" id="cell-item" class="text-semibold">{$_L['Description']}</th>
									<th rowspan="2" id="cell-item" class="text-semibold">Image</th>
									<th rowspan="2" id="cell-qty" class="text-center text-semibold">{$_L['Quantity']}</th>
									<!--<th rowspan="2" id="cell-price" class="text-center text-semibold">{$_L['Rate']}</th>-->			
									<!--<th colspan="2" id="cell-cgst" class="text-center text-semibold">CGST</th>				
									<th colspan="2" id="cell-cgst" class="text-center text-semibold">SGST</th>				
									<th colspan="2" id="cell-igst" class="text-center text-semibold">IGST</th>-->				
									<!--<th rowspan="2" id="cell-total" class="text-center text-semibold">{$_L['Total']}</th>-->
								</tr>
								<!--<tr>
									<th>%</th>
									<th>AMT</th>
									<th>%</th>
									<th>AMT</th>
									<th>%</th>
									<th>AMT</th>
								</tr>-->
							</thead>
							<tbody>
								{$i=1} 
								{foreach $items as $item} 
									<tr class="item-row">
										<td class="center">{$i++}</td>
										<td class="">{$item['description']}</td>
										<td>
											{if !empty($item['item_img'])}
												<img data-img="/ui/lib/imgs/invoice-contents/{$item['item_img']}" class="img-popup" width="40px" height="40px" src="/ui/lib/imgs/invoice-contents/{$item['item_img']}">
											{/if}
										</td>
										<td align="right">{$item['qty']}</td>
										<!--<td align="right">{ib_money_format($item['amount'],$_c,$d['currency_symbol'])}</td>-->
										<!--{if ($item['taxtype'] eq "GST")}
										<td align="right">{($item['taxrate']/2)}</td>
										<td align="right">{ib_money_format(($item['taxamount']/2),$_c,$d['currency_symbol'])}</td>
										<td align="right">{($item['taxrate']/2)}</td>
										<td align="right">{ib_money_format(($item['taxamount']/2),$_c,$d['currency_symbol'])}</td>
										{else}
										<td align="right"></td>
										<td align="right"></td>
										<td align="right"></td>
										<td align="right"></td>
										{/if}
										{if ($item['taxtype'] eq "IGST")}
										<td align="right">{$item['taxrate']}</td>
										<td align="right">{ib_money_format($item['taxamount'],$_c,$d['currency_symbol'])}</td>
										{else}
										<td align="right"></td>
										<td align="right"></td>
										{/if}-->
										<!--<td align="right"><span class="price">{ib_money_format($item['total'],$_c,$d['currency_symbol'])}</span></td>-->
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>

					<div class="invoice-summary">
							<div class="row">
									<div class="col-sm-4 col-sm-offset-8">
											<table class="table h5 text-dark">
													<tbody>
													<tr class="b-top-none">
															<td colspan="2">{$_L['Sub Total']}</td>
															<td class="text-right">{ib_money_format($d['total'],$_c,$d['currency_symbol'])}</td>
													</tr>

													{if ($d['discount']) neq '0.00'}
															<tr>
																	<td colspan="2">{$_L['Discount']}
																	 {if $d['discount_type'] eq 'p'}({$d['discount_value']}%){/if}
																	</td>
																	<td class="text-right">{ib_money_format($d['discount'],$_c,$d['currency_symbol'])}</td>
															</tr>
													{/if}

													<!--<tr>
															<td colspan="2">{$_L['TAX']} Amount</td>
															<td class="text-right">{ib_money_format($d['taxamt'],$_c,$d['currency_symbol'])}</td>
													</tr>-->
													{if ($d['credit']) neq '0.00'}
															<tr>
																	<td colspan="2">Invoice {$_L['Total']}</td>
																	<td class="text-right">{ib_money_format($d['subtotal'],$_c,$d['currency_symbol'])}</td>
															</tr>
															<tr>
																	<td colspan="2">{$_L['Total Paid']}</td>
																	<td class="text-right">{ib_money_format($d['credit'],$_c,$d['currency_symbol'])}</td>
															</tr>
															<tr class="h4">
																	<td colspan="2">{$_L['Amount Due']}</td>
																	<td class="text-right">{ib_money_format($i_due,$_c,$d['currency_symbol'])}</td>
															</tr>
													{else}
															<tr class="h4">
																	<td colspan="2">{$_L['Grand Total']}</td>
																	<td class="text-right">{ib_money_format($d['subtotal'],$_c,$d['currency_symbol'])}</td>
															</tr>
													{/if}
													</tbody>
											</table>
									</div>
							</div>
					</div>
			</div>

			{if ($trs_c neq '')}
					<h3>{$_L['Related Transactions']}</h3>
					<table class="table table-bordered sys_table">
							<th>{$_L['Date']}</th>
							<th>{$_L['Account']}</th>
							<th align="left">{$_L['Method']}</th>
							<th class="text-right">{$_L['Amount']}</th>
							<th>{$_L['Description']}</th>
							{foreach $trs as $tr}
									<tr class="{if $tr['cr'] eq '0.00'}warning {else}info{/if}">
											<td>{date( $_c['df'], strtotime($tr['date']))}</td>
											<td>{$tr['account']}</td>
											<td align="left">{$tr['method']}</td>
											<td class="text-right">{ib_money_format($tr['amount'],$_c,$d['currency_symbol'])}</td>
											<td>{$tr['description']}</td>
									</tr>
							{/foreach}
					</table>
			{/if}
			{if ($d['notes']) neq ''}
					<div class="well m-t">
							{$d['notes']}
					</div>
			{/if}
			<div class="text-right">
					 <br>
					{*<a href="{$_url}client/ipdf/{$d['id']}/token_{$d['vtoken']}" class="btn btn-primary ml-sm"><i class="fa fa-print"></i> {$_L['Download PDF']}</a>
					<a href="{$_url}iview/print/{$d['id']}/token_{$d['vtoken']}" target="_blank" class="btn btn-primary ml-sm"><i class="fa fa-print"></i> {$_L['Printable Version']}</a>*}
			</div>
		</div>
	</section>
</div>

<div style="display: none;" class="pop-outer text-right">
        <div class="pop-inner">
            <button class="close-popup btn btn-danger">X</button>
            <img src="" width="100%">
        </div>
    </div>

	<style>
			.pop-outer {
				background-color: rgba(0, 0, 0, 0.5);
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
			}
			.pop-inner {
				background-color: #fff;
				width: 500px;
				height: auto;
				padding: 10px;
				margin: 5% auto;
			}
	
			img.img-popup {
				cursor: pointer;
			}    
			
.no-spinners::-webkit-inner-spin-button,
.no-spinners::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.no-spinners {
    appearance: textfield;
    -moz-appearance: textfield; /* Firefox */
}			
		</style>    

<!-- Mainly scripts -->
<script src="{$_theme}/js/jquery-1.10.2.js"></script>
<script src="{$_theme}/js/jquery-ui-1.10.4.min.js"></script>
<script>
    var _L = [];
    var config_animate = 'No';
    {if ($_c['animate']) eq '1'}
    var config_animate = 'Yes';
    {/if}
    {$jsvar}
</script>
<script src="{$_theme}/js/bootstrap.min.js"></script>
<script src="{$_theme}/js/jquery.metisMenu.js"></script>
<script src="{$_theme}/js/jquery.slimscroll.min.js"></script>
<!-- Custom and plugin javascript -->
<script src="{$_theme}/lib/moment.js"></script>

<script src="{$_theme}/js/app.js"></script>
<script src="{$_theme}/js/pace.min.js"></script>
<script src="{$_theme}/lib/progress.js"></script>
<script src="{$_theme}/lib/bootbox.min.js"></script>

<!-- iCheck -->
<script src="{$_theme}/lib/icheck/icheck.min.js"></script>
{if isset($xfooter)}
    {$xfooter}
{/if}
<script>
    jQuery(document).ready(function() {
        // initiate layout and plugins

        {if isset($xjq)}
        {$xjq}
        {/if}

    });

</script>

<script>
        $(document).ready(function (){
            $('body').on('click', '.img-popup', function (){
                $('.pop-outer img').attr("src", $(this).attr("data-img"));
                $(".pop-outer").fadeIn("slow");       
            });
            $(".close-popup").click(function (){
                $(".pop-outer").fadeOut("slow");
            });
        });
	</script>
	
</body>

</html>
