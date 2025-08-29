<html>

<head>

	<style>
		/*

PDF library using PHP have some limitations and all CSS properties may not support. Before Editing this file, Please create a backup, so that You can restore this.

The location of this file is here- application/lib/invoices/pdf-x2.php

*/

		* {
			margin: 0;
			padding: 0;
		}

		body {
			/*

            Important: Do not Edit Font Name, Unless you are sure. It's required for PDF Rendering Properly

            */
			font: 14px/1.4 dejavusanscondensed;


			/*

            Font Name End

            */
		}

		#page-wrap {
			width: 800px;
			margin-top: 0px auto;
		}

		table {
			border-collapse: collapse;
		}

		table td,
		table th {
			border: 1px solid black;
			padding: 5px;
		}


		#customer {
			overflow: hidden;
		}

		#logo {
			text-align: right;
			float: right;
			position: relative;
			margin-top: 5px;
			border: 1px solid #fff;
			max-width: 540px;
			overflow: hidden;
		}

		#meta {
			margin-top: 1px;
			width: 100%;
		}

		#meta td {
			text-align: right;
		}

		#meta td.meta-head {
			text-align: left;
			background: #eee;
			border: 1px solid black !important;
		}

		#meta td textarea {
			width: 100%;
			height: 20px;
			text-align: right;
		}

		#items {
			clear: both;
			width: 100%;
			margin: 10px 0 0 0;
			border: 1px solid black;
		}

		#items th {
			background: #eee;
		}

		#items textarea {
			width: 80px;
			height: 50px;
		}

		#items tr.item-row td {
			vertical-align: top;
		}

		#items td.description {
			width: 300px;
		}

		#items td.item-name {
			width: 175px;
		}

		#items td.description textarea,
		#items td.item-name textarea {
			width: 100%;
		}

		#items td.total-line {
			border-right: 0;
			text-align: right;
		}

		#items td.total-value {
			border-left: 0;
			padding: 10px;
		}

		#items td.total-value textarea {
			height: 20px;
			background: none;
		}

		#items td.balance {
			background: #eee;
		}

		#items td.blank {
			border: 0;
		}

		#terms {
			text-align: left;
			margin: 20px 0 0 0;
		}

		#terms h5 {
			text-transform: uppercase;
			font: 13px<?php echo $config['pdf_font']; ?>
			;
			letter-spacing: 10px;
			border-bottom: 1px solid black;
			padding: 0 0 8px 0;
			margin: 0 0 8px 0;
		}

		#terms textarea {
			width: 100%;
			text-align: center;
		}

		.borderless td,
		.borderless th {
			border: none;
		}

		.noborder td {
			border: 0px solid #ddd !important;
			border-right: 1px solid black !important;
			padding: 5px;
			font-size: 12px !important;
			height: 18px;
			line-height: 1em;
		}

		table, p, span {
			font-size: 10px !important;
		}		
	</style>

</head>

<body style="font-family:dejavusanscondensed">
	<table width="100%">
		<tr>
			<td style="border: 0;  text-align: left">
				<img width="100px" id="image" src="<?php echo APP_URL; ?>/application/storage/system/<?php echo $comp['company_logo']; ?>"
				 alt="logo" />
			</td>
			<td style="border: 0;  text-align: right">
				<span style="font-size: 14px; color: #2f4f4f">
					<strong>
						<?php echo $_L['INVOICE']; ?>#
						<?php echo ($d['cn']) ? $d['cn'] : $d['invoicenum']; ?>
					</strong>
					<br>
					<?php 
                      echo '<strong>Customer Name:</strong> '. $d['account']. ' <br>';
					?>
					<p><b>Invoice Date :</b> <?php echo $d['date']; ?></p>
					<p><b>Delivery Date :</b> <?php echo $d['duedate']; ?></p>					
				</span>
			</td>
		</tr>
	</table>
	<div id="page-wrap">
		<table width="100%">
			<tr>

				<td style="border: 0; line-height:1.6em; vertical-align:middle; text-align: left" width="62%">
					<div id="logo" style="font-size:16px; line-height:3em; vertical-align:middle;">

					</div>
				</td>
				<td rowspan="5" width="62%" style="border: 0;line-height:1.5em;  text-align: left; font-size:16px;">
				
				</td>
			</tr>
		</table>

<hr>
<?php if($d['d_measure'] == 'yes'){ ?>
        <p style="margin:0px;"><b>Measurements </b></p>
		<div class="table-responsive" id="customer">
			<table class="table" id="items">
				<tr>
				    <?php foreach (json_decode($a['measurements']) as $key => $val){ ?>
    					<th id="cell-item" class="text-center text-semibold"><?php echo ucfirst($key); ?></th>
					<?php } ?>
				</tr>
				<tr>
					
				    <?php foreach (json_decode($a['measurements']) as $key => $val){ ?>
					<td align="center"><?php echo $val; ?></td>
					<?php } ?>					
				</tr>
			</table>
		</div>		
					<?php } ?>

					<?php if(!empty($d['d_measure']) ){ ?>
						<p><b>Additinal Images </b></p>
						<?php foreach (json_decode($d['additional_imgs']) as $val){ ?>
							<img src="<?php echo $val; ?>" width="50px" height="50px">
						<?php } ?>
		
					<?php } ?>


		<div class="table-responsive">
			<table class="table" id="items" width="100%">
				<tr>
					<th id="cell-item" class="text-semibold" width="10%">S.NO</th>
					<th id="cell-item" class="text-semibold" width="50%">
						<?php echo $_L['Description'];?>
					</th>
					
					<th id="cell-item" class="text-semibold" width="20%">
						Design
					</th>

					<!--<th id="cell-qty" class="text-center text-semibold" width="10%">
						<?php echo $_L['Qty'];?>
					</th>
					<th id="cell-price" class="text-center text-semibold">
						<?php echo $_L['Rate'];?>
					</th>

					<th id="cell-total" class="text-center text-semibold">
						<?php echo $_L['Total'];?>
					</th>-->
				</tr>

				<?php  $i=0; 
					$countrow = 0;						
					foreach ($items as $item){
					$countrow++; ?>
				<tr class="item-row noborder">
					<td align="center" style="border-bottom:1px solid #ccc; vertical-align:middle;font-size: 10px !important;">
						<?= ++$i; ?>
					</td>
					<td align="center" style="border-bottom:1px solid #ccc; vertical-align: middle;font-size: 10px !important;">
						<?= $item['description']; ?>
											
					</td>
					<td align="center" style="border-bottom:1px solid #ccc;">
						<?php if (!empty($item['item_img'])){ ?>
							<img width="50px" height="50px" src="/ui/lib/imgs/invoice-contents/<?= $item['item_img']; ?>">
						<?php } ?>						
					</td>
					<!--<td align="center" style="border-bottom:1px solid #ccc;">
						<?= $item['qty']; ?>
					</td>
					<td align="center">
						<?= $item['amount']; ?>
					</td>
					<td align="center" style="border-bottom:1px solid #ccc;">
						<span class="price">
							<?= number_format($item['qty']*$item['amount'],2,".",""); ?>
						</span>
					</td>-->
				</tr>
				<?php 
					} ?>

				<tr class="noborder">
					<td>&nbsp;</td>
					<td></td>
				</tr>
			</table>
		</div>

	</div>

</body>

</html>