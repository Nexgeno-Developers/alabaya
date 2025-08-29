<style>
		.fabric-fields, .stone-fields
		{
			padding-bottom:5px;
		}
		.m-b-10 {
            margin-bottom: 10px;
        }
  </style>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Edit</h3>
</div>
<div class="modal-body">
	<form class="form-horizontal" role="form" id="edit_form" method="post">
		<div class="form-group">
			<label for="name" class="col-sm-2 control-label">{$_L['Name']}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" value="{$d['name']}" name="name" id="name">
			</div>
		</div>
		<div class="form-group">
			<label for="rate" class="col-sm-2 control-label">Sale Price</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="sales_price" value="{$d['price']}" id="price">

			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-2 control-label" for="design_images">Product Image</label>
			<div class="col-lg-7">
				<input type="file" id="design_images" name="design_images[]" class="form-control" autocomplete="off" accept="image/*">
			</div>
			<div class="col-lg-1 text-right">
			{$images = json_decode($d['image'], true)}
			{foreach $images as $img}
				<a target="_blank" href="{$img}"><img src="{$img}" width="50px" height="50px"></a>
			{/foreach}
			</div>
		</div>
		<div class="form-group hide">
			<label for="name" class="col-sm-2 control-label">{$_L['Description']}</label>
			<div class="col-sm-10">
				<textarea id="description" name="description" class="form-control" rows="3">{$d['description']}</textarea>
			</div>
		</div>

		<div class="form-group">
				<label class="col-lg-2 control-label" for="description">Product Type</label>
				<div class="col-lg-10">
					  <select name="cloth_id" class="form-control select2">
							<option value="">--Select Product Type--</option>
							{foreach $cloths as $cloth}
							<option value="{$cloth['id']}" {if $cloth['id'] eq $d['cloth_id']} selected {/if}>{$cloth['name']}</option>
							{/foreach}
					  </select>
				</div>
			 </div>                  

	<div class="form-group">
		  <label class="col-lg-2 control-label" for="fabrics">Fabric</label>
		  <div class="col-lg-10">
				<div class="fabric-block">
				{$x=0}
				{$inbuilt_fabrics = json_decode($d['fabrics'], true)}
				{if empty($inbuilt_fabrics)}

				<div class="fabric-fields">
					<div class="row">
					<div class="col-md-5">
						  <select name="fabric_id[]" class="form-control select2">
						  <option value="">--Select Fabric--</option>
						  {foreach $fabrics as $fabric}
						  <option value="{$fabric['id']}">{$fabric['name']}</option>
						  {/foreach}
						  </select>
					</div>
					<div class="col-md-5">
						  <input type="number" class="form-control" name="fabric_qty[]" placeholder="Enter Qty" required>
					</div>
					<div class="col-md-2 change">
						  <div class="btn btn-block btn-warning bg_greens" onclick="addFabric();"><i class="fa fa-plus" aria-hidden="true"></i></div>
					</div>
					</div>
			  </div>				

				{else}
				{foreach $inbuilt_fabrics as $row}
				<div class="fabric-fields">
					  <div class="row">
					  <div class="col-md-5">
							<select name="fabric_id[]" class="form-control select2">
							<option value="">--Select Fabric--</option>
							{foreach $fabrics as $fabric}
							<option value="{$fabric['id']}" {if $row['fabric_id'] eq $fabric['id']} selected {/if}>{$fabric['name']}</option>
							{/foreach}
							</select>
					  </div>
					  <div class="col-md-5">
							<input type="number" class="form-control" name="fabric_qty[]" placeholder="Enter Qty" value="{$row['fabric_qty']}" required>
					  </div>
					  {if $x eq 0}
					  <div class="col-md-2 change">
							<div class="btn btn-block btn-warning bg_greens" onclick="addFabric();"><i class="fa fa-plus" aria-hidden="true"></i></div>
					  </div>
					  {else}
					    <div class="col-md-2">
						   <div class="btn btn-block btn-danger" onclick="removeFabric(this);"><i class="fa fa-minus" aria-hidden="true"></i>
</div>
						</div>
					  {/if}
					  </div>
				</div>
				{$i=$x++}
				{/foreach}
				{/if}
				</div>
		  </div>
		  </div> 

		  <div class="form-group">
				<label class="col-lg-2 control-label" for="stone_id">Stone Color & Size</label>
				<div class="col-lg-10">
					  <div class="stone-block">
							{$x=0}
							{$inbuilt_stones = json_decode($d['stones'], true)}
							{if empty($inbuilt_stones)}

							<div class="stone-fields">
								<div class="row">
									  <div class="col-md-5">
											<select name="stone_id[]" class="form-control select2">
											<option value="">--Select Stone Color & Size--</option>
											{foreach $stones as $stone}
											<option value="{$stone['id']}">{$stone['name']}</option>
											{/foreach}
											</select>
									  </div>
									  <div class="col-md-5">
											<input type="number" class="form-control" name="stone_qty[]" placeholder="Enter Qty" required>
									  </div>
									  <div class="col-md-2 change">
											<div class="btn btn-block btn-warning bg_greens" onclick="addStone();"><i class="fa fa-plus" aria-hidden="true"></i></div>
									  </div>
								</div>
								</div>							

							{else}
														
							{foreach $inbuilt_stones as $row}						  
							<div class="stone-fields">
							<div class="row">
								  <div class="col-md-5">
										<select name="stone_id[]" class="form-control select2">
										<option value="">--Select Stone Color & Size--</option>
										{foreach $stones as $stone}
										<option value="{$stone['id']}" {if $row['stone_id'] eq $stone['id']} selected {/if}>{$stone['name']}</option>
										{/foreach}
										</select>
								  </div>
								  <div class="col-md-5">
										<input type="number" class="form-control" name="stone_qty[]" placeholder="Enter Qty" value="{$row['stone_qty']}" required>
								  </div>
								  
								  {if $x eq 0}
								  <div class="col-md-2 change">
										<div class="btn btn-block btn-warning bg_greens" onclick="addStone();"><i class="fa fa-plus" aria-hidden="true"></i>
</div>
								  </div>
								  {else}
									<div class="col-md-2">
										<div class="btn btn-block btn-danger" onclick="removeStone(this);"><i class="fa fa-minus" aria-hidden="true"></i></div>
									</div>
								  {/if}								  
							</div>
							</div>
							{$i=$x++}
							{/foreach}	
							{/if}						
					  </div>
				</div>
				</div>


				<!--handwork-->



				<div class="form-group">
					<label class="col-lg-2 control-label" for="handwork_id">Handwork Materials</label>
					<div class="col-lg-10">
						<div class="handwork-block">

							{$x=0}
							{$inbuilt_handworks = json_decode($d['handworks'], true)}
							{if empty($inbuilt_handworks)}
							<div class="handwork-fields">
								<div class="row">
									<div class="col-md-5">
										<select name="handwork_id[]" class="form-control select2">
											<option value="">--Select Handwork Materials--</option>
											{foreach $handwork_materials as $row}
											<option value="{$row['id']}">{$row['name']}</option>
											{/foreach}
										</select>
									</div>
									<div class="col-md-5">
										<input type="number" class="form-control" name="handwork_qty[]" placeholder="Enter Qty" required>
									</div>
									<div class="col-md-2 change">
										<div class="btn btn-block btn-warning bg_greens" onclick="addHandwork();">
											<i class="fa fa-plus" aria-hidden="true"></i>
										</div>
									</div>
								</div>
							</div>
							{else}
							
							{foreach $inbuilt_handworks as $row1}

							<div class="handwork-fields">
								<div class="row">
									<div class="col-md-5">
										<select name="handwork_id[]" class="form-control select2">
											<option value="">--Select Handwork Materials--</option>
											{foreach $handwork_materials as $row}
											<option value="{$row['id']}" {if $row1['handwork_id'] eq $row['id']} selected {/if}>{$row['name']}</option>
											{/foreach}
										</select>
									</div>
									<div class="col-md-5">
										<input type="number" class="form-control" name="handwork_qty[]" placeholder="Enter Qty" value="{$row1['handwork_qty']}" required>
									</div>
									
									{if $x eq 0}
									<div class="col-md-2 change">
										  <div class="btn btn-block btn-warning bg_greens" onclick="addHandwork();"><i class="fa fa-plus" aria-hidden="true"></i>
                                     </div>
									</div>
									{else}
									  <div class="col-md-2">
										  <div class="btn btn-block btn-danger" onclick="removeHandwork(this);"><i class="fa fa-minus" aria-hidden="true"></i></div>
									  </div>
									{/if}									
								</div>
							</div>							

							{$i=$x++}
							{/foreach}


							{/if}

						</div>
					</div>
				</div>


				<div class="form-group">
					<label class="col-lg-2 control-label" for="handwork_id">Others</label>
					<div class="col-lg-10">
						<div class="other-block">

							{$x=0}
							{$inbuilt_others = json_decode($d['others'], true)}
							{if empty($inbuilt_others)}

							<div class="other-fields">
								<div class="row">
									<div class="col-md-5">
										<select name="others_id[]" class="form-control select2">
											<option value="">--Select Others--</option>
											{foreach $others as $row}
											<option value="{$row['id']}">{$row['name']}</option>
											{/foreach}
										</select>
									</div>
									<div class="col-md-5">
										<input type="number" class="form-control" name="others_qty[]" placeholder="Enter Qty" required>
									</div>
									<div class="col-md-2 change">
										<div class="btn btn-block btn-warning bg_greens" onclick="addOthers();">
											<i class="fa fa-plus" aria-hidden="true"></i>
										</div>
									</div>
								</div>
							</div>
							{else}
								{foreach $inbuilt_others as $row1}
								
								<div class="other-fields">
										<div class="row">
											<div class="col-md-5">
												<select name="others_id[]" class="form-control select2">
													<option value="">--Select Others--</option>
													{foreach $others as $row}
													<option value="{$row['id']}" {if $row1['other_id'] eq $row['id']} selected {/if}>{$row['name']}</option>
													{/foreach}
												</select>
											</div>
											<div class="col-md-5">
												<input type="number" class="form-control" name="others_qty[]" placeholder="Enter Qty" value="{$row1['other_qty']}" required>
											</div>
											{if $x eq 0}
											<div class="col-md-2 change">
												  <div class="btn btn-block btn-warning bg_greens" onclick="addOthers();"><i class="fa fa-plus" aria-hidden="true"></i>
											 </div>
											</div>
											{else}
											  <div class="col-md-2">
												  <div class="btn btn-block btn-danger" onclick="removeOthers(this);"><i class="fa fa-minus" aria-hidden="true"></i></div>
											  </div>
											{/if}
										</div>
									</div>

								{$i=$x++}
								{/foreach}
							{/if}


						</div>
					</div>
				</div>
				<!--handwork-->
				
				
				<!--Category Pricing-->
	            <div class="form-group">
                    <label class="col-lg-2 control-label" for="category price" >Category Pricing</label>
                    <div class="col-lg-10">
                        <div class="category-pricing-block">
                            {foreach from=$category_employees item=employee}
                                {assign var=price value=0} 
                                {foreach from=$category_pricing item=pricing}
                                    {if $pricing.category_id == $employee.id}
                                        {assign var=price value=$pricing.price}
                                    {/if}
                                {/foreach}
                                <div class="category-pricing-fields">
                                    <div class="row m-b-10">
                                        <input type="hidden" name="category_id[]" value="{$employee.id}">
					                    <div class="col-md-6">
							                <input type="text" class="form-control" value="{$employee.name}" readonly>
                					    </div>
                					    <div class="col-md-6">
                							<input type="number" class="form-control" name="category_price[]" value="{$price}" required>
                					    </div>
			  					    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>

		<input type="hidden" name="id" value="{$d['id']}">
	</form>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn">{$_L['Close']}</button>
	<button id="update" class="btn btn-primary">{$_L['Update']}</button>
</div>


<script>
    function addFabric()
    {
        var html = '<div class="fabric-fields"> <div class="row"><div class="col-md-5"><select name="fabric_id[]" class="form-control select2"><option value="">--Select Fabric--</option>{foreach $fabrics as $fabric}<option value="{$fabric["id"]}">{$fabric["name"]}</option>{/foreach}</select></div><div class="col-md-5"> <input type="number" class="form-control" name="fabric_qty[]" placeholder="Enter Qty" required> </div><div class="col-md-2"><div class="btn btn-block btn-danger" onclick="removeFabric(this);">Remove</div></div></div></div>';    
        $(".fabric-block").append(html);
        $('.select2').select2();
    }
    
    function removeFabric(elem)
    {
        $(elem).closest('.fabric-fields').remove();
    }    
    
    function addStone()
    {
        var html = ' <div class="stone-fields"> <div class="row"> <div class="col-md-5"> <select name="stone_id[]" class="form-control select2"> <option value="">--Select Stone & Size--</option>{foreach $stones as $stone}<option value="{$stone["id"]}">{$stone["name"]}</option>{/foreach}</select> </div><div class="col-md-5"> <input type="number" class="form-control" name="stone_qty[]" placeholder="Enter Qty" required> </div><div class="col-md-2"><div class="btn btn-block btn-danger" onclick="removeStone(this);">Remove</div></div></div></div>';    
        $(".stone-block").append(html);
        //$('.select2').select2();
    }
    
    function removeStone(elem)
    {
        $(elem).closest('.stone-fields').remove();
    }

function addHandwork() {
        var html = ' <div class="handwork-fields"> <div class="row"> <div class="col-md-5"> <select name="handwork_id[]" class="form-control select2"> <option value="">--Select Handwork Materials--</option>{foreach $handwork_materials as $row}<option value="{$row["id"]}">{$row["name"]}</option>{/foreach}</select> </div><div class="col-md-5"> <input type="number" class="form-control" name="handwork_qty[]" placeholder="Enter Qty" required> </div><div class="col-md-2"><div class="btn btn-block btn-danger" onclick="removeHandwork(this);"><i class="fa fa-minus" aria-hidden="true"></i></div></div></div></div>';
        $(".handwork-block").append(html);
        $('.select2').select2();
    }

    function removeHandwork(elem) {
        $(elem).closest('.handwork-fields').remove();
    }

    function addOthers() {
        var html = ' <div class="other-fields"> <div class="row"> <div class="col-md-5"> <select name="others_id[]" class="form-control select2"> <option value="">--Select Others--</option>{foreach $others as $row}<option value="{$row["id"]}">{$row["name"]}</option>{/foreach}</select> </div><div class="col-md-5"> <input type="number" class="form-control" name="others_qty[]" placeholder="Enter Qty" required> </div><div class="col-md-2"><div class="btn btn-block btn-danger" onclick="removeOthers(this);"><i class="fa fa-minus" aria-hidden="true"></i></div></div></div></div>';
        $(".other-block").append(html);
        $('.select2').select2();
    }

    function removeOthers(elem) {
        $(elem).closest('.other-fields').remove();
    }	 


    //$('.select2').select2();	
</script>