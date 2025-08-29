<html>

<head>

    <style>

/*

PDF library using PHP have some limitations and all CSS properties may not support. Before Editing this file, Please create a backup, so that You can restore this.

The location of this file is here- application/lib/invoices/pdf-x2.php

*/

        * { margin: 0; padding: 0; }
        body {
            /*

            Important: Do not Edit Font Name, Unless you are sure. It's required for PDF Rendering Properly

            */


            font: 14px/1.4  dejavusanscondensed;


            /*

            Font Name End

            */
        }

        #page-wrap { width: 800px; margin-top: 0px auto; }

        table { border-collapse: collapse; }
        table td, table th { border: 1px solid black; padding: 5px; }


        #customer { overflow: hidden; }

        #logo { text-align: right; float: right; position: relative; margin-top: 5px; border: 1px solid #fff; max-width: 540px; overflow: hidden; }

        #meta { margin-top: 1px; width: 100%; }
        #meta td { text-align: right;  }
        #meta td.meta-head { text-align: left; background: #eee; border: 1px solid black !important; }
        #meta td textarea { width: 100%; height: 20px; text-align: right; }

        #items { clear: both; width: 100%; margin: 10px 0 0 0; border: 1px solid black; }
        #items th { background: #eee; }
        #items textarea { width: 80px; height: 50px; }
        #items tr.item-row td {  vertical-align: top; }
        #items td.description { width: 300px; }
        #items td.item-name { width: 175px; }
        #items td.description textarea, #items td.item-name textarea { width: 100%; }
        #items td.total-line { border-right: 0; text-align: right; }
        #items td.total-value { border-left: 0; padding: 10px; }
        #items td.total-value textarea { height: 20px; background: none; }
        #items td.balance { background: #eee; }
        #items td.blank { border: 0; }

        #terms { text-align: left; margin: 20px 0 0 0; }
        #terms h5 { text-transform: uppercase; font: 13px <?php echo $config['pdf_font']; ?>; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }
        #terms textarea { width: 100%; text-align: center;}
.borderless td, .borderless th {
    border: none;
}
.noborder td{
	  border: 0px solid #ddd !important;
    border-right: 1px solid black !important;
		padding: 5px;
		font-size:12px !important;
		height: 18px;
		line-height: 1em;
}
    </style>

</head>

<body style="font-family:dejavusanscondensed">
	<table width="100%">
		<tr>
			<td style="border: 0;  text-align: left" >
				<img width="200px" id="image" src="<?php echo APP_URL; ?>/application/storage/system/<?php echo $comp['company_logo']; ?>" alt="logo" />
			</td>
			<td style="border: 0;  text-align: right" >
				<span style="font-size: 14px; color: #2f4f4f">
					<strong><?php echo 'Proforma'; ?> # <?php echo ($d['invoicenum']); ?></strong>
				</span>
			</td>
		</tr>
	</table>
	<div id="page-wrap">
		<table width="100%">
			<tr>

				<td style="border: 0; text-align: left" width="62%"><div id="logo" style="font-size:16px">
					<strong>Proforma From:</strong> <br>
					<strong><?php echo $comp['account']; ?>	</strong><br>
					<?php echo $comp['address']; ?><br>
					<strong>Phone : </strong><?php echo $comp['contact_phone']; ?><br>
					<strong>Email : </strong><?php echo $comp['email']; ?><br>
					<strong>GSTIN : </strong><?php echo $comp['gstin']; ?><br>
					<?php if(!empty($comp['pan'])){ ?>
						<strong>PAN : </strong><?php echo $comp['pan']; ?><br>
					<?php } ?>					
					<strong>Account Name : </strong><?php echo get_type_by_id('sys_accounts', 'id', $d['company_id'], 'account'); ?><br>
					<strong>Account No : </strong><?php echo get_type_by_id('sys_accounts', 'id', $d['company_id'], 'account_number'); ?><br>
					<strong>IFSC Code : </strong><?php echo get_type_by_id('sys_accounts', 'id', $d['company_id'], 'ifsc'); ?>
					</div>
				</td>
				<td rowspan="5" width="62%"  style="border: 0; text-align: right; font-size:16px;">
					<strong>Proforma To:</strong> <br>
							<?php if($a['company'] != '') { ?>
									<strong><?php echo $a['company']; ?></strong> <br>
									<strong><?php echo $_L['ATTN']; ?>:</strong> <?php echo $a['account']; ?> <br>
							<?php
							}
							else{
									?>
									<?php echo $d['account']; ?> <br>
							<?php
							}
							?>
							<?php echo $a['address']; ?> <br>
							<?php echo $a['city']; ?> <?php echo $a['state']; ?> <?php echo $a['zip']; ?> <br>
							<?php echo $a['country']; ?> <br>
							<?php
							if(($a['phone']) != ''){
									echo '<strong>Phone:</strong> '. $a['phone']. ' <br>';
							}
							if(($a['email']) != ''){
									echo '<strong>Email:</strong> '. $a['email']. ' <br>';
							}
							foreach ($cf as $cfs){
									echo $cfs['fieldname'].': '. get_custom_field_value($cfs['id'],$a['id']). ' <br>';
							}
							?>
							
							<strong><?php echo $_L['gst'].": ";?></strong> <?php echo $a['gst_no'] ? $a['gst_no'] : '-'; ?>
										<br>			
							<?php if(!empty($a['pan'])){ ?>
								
					    		<strong><?php echo "PAN: ";?></strong> <?php echo $a['pan']; ?>
							<?php } ?>
					</td>
			</tr>
		</table><hr>
    <div class="table-responsive" id="customer">
      <table class="table" id="items">
				<tr>
					<th id="cell-item" class="text-center text-semibold">Status</th>
					<th id="cell-item" class="text-center text-semibold">Proforma Date</th>
					<th id="cell-item" class="text-center text-semibold">Due Date</th>
				</tr>
				<tr>
						<td align="center"><?php echo ib_lan_get_line($d['status']); ?></td>
				
						<td align="center"><?php echo date($config['df'], strtotime($d['date'])); ?></td>
			
						<td align="center"><?php echo date($config['df'], strtotime($d['duedate'])); ?></td>
				</tr>
				
      </table>
     
    </div>
			
		<div class="table-responsive">
			<table class="table" id="items" width="100%">
				<tr>
					<th id="cell-item" class="text-semibold" width="10%">S.NO</th>
					<th id="cell-item" class="text-semibold" width="50%"><?php echo $_L['Description'];?></th>
					
					<th id="cell-qty" class="text-center text-semibold" width="10%"><?php echo $_L['Qty'];?></th>
					<th id="cell-price" class="text-center text-semibold"><?php echo $_L['Rate'];?></th>				
					
					<th id="cell-total" class="text-center text-semibold"><?php echo $_L['Total'];?></th>
				</tr>
				
					<?php  $i=0; 
					$countrow = 0;						
					foreach ($items as $item){
					$countrow++; ?>
							<tr class="item-row noborder">
								<td align="center"><?= ++$i; ?></td>
								<td align="center"><?= $item['description']; ?></td>
								<td align="center"><?= $item['qty']; ?></td>
								<td align="center"><?= $item['amount']; ?></td>
								<td align="center"><span class="price"><?= number_format($item['qty']*$item['amount'],2,".",""); ?></span></td>
							</tr>
					<?php 
					} ?>
					<?php
									if($countrow<15){
							$mktr = 15-$countrow;
							for($i=0; $i<$mktr; $i++){
								?>
										<tr class="noborder" >
									<td>&nbsp;</td><td></td><td></td>
									<td></td><td></td>
								</tr>
								<?php
							}
						}
						?>
			</table>				
		</div>	
		<div class="table-responsive">
			<?php /*<table class="table" id="items" width="100%">
				<tr>
					<th id="cell-item" class="text-semibold"><?php echo $_L['Sub Total']; ?></th>
					<th id="cell-item" class="text-semibold">CGST</th>
					<?php if(get_type_by_id('sys_tax', 'id', $item['tax_id'], 'taxtype')=='GST'){ ?>
					<th id="cell-qty" class="text-center text-semibold">SGST</th>
					<?php } ?>
						<?php if(get_type_by_id('sys_tax', 'id', $item['tax_id'], 'taxtype')=='IGST'){ ?>
					<th id="cell-price" class="text-center text-semibold">IGST</th>				
						<?php } ?>
					<th id="cell-total" class="text-center text-semibold">GST Amount</th>
					<th id="cell-total" class="text-center text-semibold">Grand Total</th>
					<th id="cell-total" class="text-center text-semibold">Advance</th>
					<?php if($d['discount'] > 0){ ?>
					<th id="cell-total" class="text-center text-semibold">Discount</th>
					<?php } ?>
					<th id="cell-total" class="text-center text-semibold">Balance</th>
				</tr>
				
					<?php  $i=0; 
					foreach ($items as $item){ ?>
							<tr class="item-row">
								<td align="center"><?php echo ib_money_format($d['total'],$config,$d['currency_symbol']); ?></td>
								<?php if(get_type_by_id('sys_tax', 'id', $item['tax_id'], 'taxtype')=='GST'){ ?>
								<td align="center"><?php echo ib_money_format($d['taxamt']/2,$config,$d['currency_symbol']); ?></td>
								<td align="center"><?php echo ib_money_format($d['taxamt']/2,$config,$d['currency_symbol']); ?></td>
								<?php } ?>
								<?php if(get_type_by_id('sys_tax', 'id', $item['tax_id'], 'taxtype')=='IGST'){ ?>
								<td align="center"><?php echo ib_money_format($d['taxamt'],$config,$d['currency_symbol']); ?></td>
								<?php } ?>
								<td align="center"><?php echo ib_money_format($d['taxamt'],$config,$d['currency_symbol']); ?></td>
								<td align="center"><?php echo ib_money_format($d['subtotal'],$config,$d['currency_symbol']); ?></td>
								<td align="center"><?php echo ib_money_format($d['credit'],$config,$d['currency_symbol']); ?></td>
								<?php if($d['discount'] > 0){ ?>
								<td align="center"><?php echo ib_money_format($d['discount'],$config,$d['currency_symbol']); ?></td>
								<?php } ?>
								<td align="center"><?php echo ib_money_format($d['subtotal'] - $d['credit'],$config,$d['currency_symbol']); ?></td>
							</tr>
					<?php 
					break; } ?>
			</table>*/ ?>
			
			<table class="table" id="items" width="100%">
				<tr>
					<th id="cell-item" class="text-semibold"><?php echo $_L['Sub Total']; ?></th>
					<?php if(get_type_by_id('sys_tax', 'id', $item['tax_id'], 'taxtype')=='GST'){ ?>
					<th id="cell-item" class="text-semibold">CGST</th>
					<th id="cell-qty" class="text-center text-semibold">SGST</th>
					<?php } ?>
						<?php if(get_type_by_id('sys_tax', 'id', $item['tax_id'], 'taxtype')=='IGST'){ ?>
					<th id="cell-price" class="text-center text-semibold">IGST</th>				
						<?php } ?>
					<th id="cell-total" class="text-center text-semibold">GST Amount</th>
					<th id="cell-total" class="text-center text-semibold">Grand Total</th>
					<th id="cell-total" class="text-center text-semibold">Advance</th>
					<?php if($d['discount'] > 0){ ?>
					<th id="cell-total" class="text-center text-semibold">Discount</th>
					<?php } ?>
					<th id="cell-total" class="text-center text-semibold">Balance</th>
				</tr>
				
					<?php  $i=0; 
					foreach ($items as $item){ ?>
							<tr class="item-row">
								<td align="center"><?php echo ib_money_format($d['total'],$config,$d['currency_symbol']); ?></td>
								<?php if(get_type_by_id('sys_tax', 'id', $item['tax_id'], 'taxtype')=='GST'){ ?>
								<td align="center"><?php echo ib_money_format($d['taxamt']/2,$config,$d['currency_symbol']); ?></td>
								<td align="center"><?php echo ib_money_format($d['taxamt']/2,$config,$d['currency_symbol']); ?></td>
								<?php } ?>
								<?php if(get_type_by_id('sys_tax', 'id', $item['tax_id'], 'taxtype')=='IGST'){ ?>
								<td align="center"><?php echo ib_money_format($d['taxamt'],$config,$d['currency_symbol']); ?></td>
								<?php } ?>
								<td align="center"><?php echo ib_money_format($d['taxamt'],$config,$d['currency_symbol']); ?></td>
								<td align="center"><?php echo ib_money_format($d['subtotal'],$config,$d['currency_symbol']); ?></td>
								<td align="center"><?php echo ib_money_format($d['credit'],$config,$d['currency_symbol']); ?></td>
								<?php if($d['discount'] > 0){ ?>
								<td align="center"><?php echo ib_money_format($d['discount'],$config,$d['currency_symbol']); ?></td>
								<?php } ?>
								<td align="center"><?php echo ib_money_format($d['subtotal'] - $d['credit'],$config,$d['currency_symbol']); ?></td>
							</tr>
					<?php 
					break; } ?>
			</table>			
			
			
				</div>	
		
		
<div class="table-responsive">
			<table class="table noborder" id="items" width="100%">
				<tr>
					<th id="cell-item" class="text-semibold" width="70%" height="10%;">Terms & Conditions</th>
					<th id="cell-item" class="text-semibold" width="30%" height="10%;">Authorised Signatory</th>
					
				</tr>
					
							<tr class="item-row">
								<td><?php if($d['notes'] != '') { echo $d['notes']; } else { echo $app_config['value']; } ?></td>
								<td align="center"><img id="sign" style="max-height:75px;" src="<?php echo APP_URL; ?>/application/storage/system/<?php echo $comp['company_signature']; ?>" alt="sign" /><br><img id="stamp" style="max-height:75px;" src="<?php echo APP_URL; ?>/application/storage/system/<?php echo $comp['company_stamp']; ?>" alt="stamp" /></td>
								
							</tr>
				
			</table>	
				</div>	
		
</div>

</body>

</html>