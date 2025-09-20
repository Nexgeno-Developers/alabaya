$(document).ready(function () {
	var _url = $("#_url").val();
	var $invoice_items = $('#invoice_items');
	call_select2("i_0");
	$('.amount').autoNumeric('init');
	var item_remove = $('#item-remove');
  var $modal = $('#ajax-modal');
  var rowNum = 0;
	var opt = get_tax_opt();
	
	function get_tax_opt(data){
		var data;
			$.ajax({
				url: _url + 'ps/get_tax_opt',
				async: false,  
				success: function(res) {
					data = res;	
				}
			});
		return data;
	}
	
	$invoice_items.on('change keyup', '.qty, .item_price', function() {
		tr=$(this).parents('tr');
		var total = 0;
    var qty = tr.find('[name="qty[]"]').val();
		var rate = tr.find('[name="amount[]"]').val();
		tr.find('[name="total[]"]').val(qty*rate);
	}).keyup();
	
	/* set total calculated value */
	$invoice_items.on('change blur', '.qty, .item_price', function() {	
		sidecalculation();
	});	
	
	$('.taxed').on('change', function() {	
		sidecalculation();
	});	
	
	function sidecalculation(){
		var subtotal = 0;
		$('#invoice_items tr').each(function() { 
			var rowvalue = $(this).find('[name="total[]"]').val() || 0;
			subtotal += parseInt(rowvalue);
		});
		var tax = $('.taxed option:selected').attr('rate');
		var gst = (subtotal*tax)/100;
		/* discount calculation */
		var discount_type = $('#discount_type').val();
		var discount_amount = $('#discount_amount').val();
		var final_dis_amt;
		if(discount_type == "p"){
			final_dis_amt = ((subtotal*discount_amount)/100) || 0.00;
		}else{
			final_dis_amt = discount_amount || 0.00;
		}
		$('#sub_total').html('&#8377;'+subtotal);
		$('#taxtotal').html('&#8377;'+gst);
		$('#discount_amount_total').html('&#8377;'+final_dis_amt);
		$('#total').html('&#8377;'+(gst+subtotal-final_dis_amt));
	}
		
	$('#notes').redactor({
			minHeight: 200, // pixels
			plugins: ['fontcolor']
	});
		
	function update_address(){
			var _url = $("#_url").val();
			var cid = $('#cid').val();
			if(cid != ''){
					$.post(_url + 'contacts/render-contact/', {
							cid: cid

					}).done(function (data) {
						   var result = $.parseJSON(data);
						   $('input[name="cust_name"]').val(result.name);
						   $('input[name="cust_phone"]').val(result.contact);
						   $('input[name="cust_location"]').val(result.location);

						   $('input[name="cust_length"]').val(result.length);
						   $('input[name="cust_shoulder"]').val(result.shoulder);
						   $('input[name="cust_sleeves"]').val(result.sleeves);
						   $('input[name="cust_armole"]').val(result.armole);
						   $('input[name="cust_cuff"]').val(result.cuff);
						   $('input[name="cust_chest"]').val(result.chest);
						   $('input[name="cust_waist"]').val(result.waist);
						   $('input[name="cust_hipps"]').val(result.hipps);
						   $('#already_cust').html('(Existing)');
						});
			}
			else
			{
				$('input[name="cust_name"]').val('');
				$('input[name="cust_phone"]').val('');
				$('input[name="cust_location"]').val('');

				$('input[name="cust_length"]').val('');
				$('input[name="cust_shoulder"]').val('');
				$('input[name="cust_sleeves"]').val('');
				$('input[name="cust_armole"]').val('');
				$('input[name="cust_cuff"]').val('');
				$('input[name="cust_chest"]').val('');
				$('input[name="cust_waist"]').val('');
				$('input[name="cust_hipps"]').val('');
				$('#already_cust').html('(New)');
			}
	}

	function render_tax(){
		/*var _url = $("#_url").val();
		var cid = $('#cid').val();
		if(cid != ''){
			$.post(_url + 'contacts/render-tax/', {
				cid: cid
			}).done(function (data) {
				$('.taxed optgroup option').removeAttr('selected');
				$('.taxed optgroup option[value="' + data + '"]').attr('selected', 'selected');
				//$('.taxed optgroup option[value="' + data + '"]').val(data);
				sidecalculation();
			});
		}*/
    }	
		
	update_address();

	/*$('#cid').select2({
			// theme: "bootstrap",
			language: {
					noResults: function () {
							return $("#_lan_no_results_found").val();
					}
			}
		}).on("change", function(e) {
					// mostly used event, fired to the original element when the value changes
					update_address();
	});*/

	function updateBranchId() {
		let branchId = $('#company').find(':selected').data('branch');
		$('#company_branch_id').val(branchId);
	}

	// On page load
	updateBranchId();

	// On change
	$('#company').on('change', function () {
		updateBranchId();
	});


	$('#cid').select2({
		// theme: 'bootstrap',
		placeholder: $("#_lan_select_contact").val() || "Select Contact...",
		allowClear: true,
		ajax: {
			url: base_url + "contacts/ajax_search_contacts",
			dataType: 'json',
			delay: 100,
			data: function (params) {
				return {
					q: params.term || '',
					page: params.page || 1,
					gid: 1
				};
			},
			processResults: function (data, params) {
				params.page = params.page || 1;
				return {
					results: data.results,
					pagination: {
						more: (params.page * 20) < data.total_count
					}
				};
			},
			cache: true
		},
		minimumInputLength: 3
	}).on("change", function(e) {
		update_address();
	});


	$('.drop-dn').select2();

	item_remove.on('click', function(){
		if(rowNum <= 0){  
			bootbox.alert("Atleast one required");
		}else{
			$("#invoice_items tr:last").fadeOut(300, function(){
					$(this).remove();
					rowNum--;
					sidecalculation();
			});
		}
	});

	$('#item-add').on('click', function(){
			// create the backdrop and wait for next modal to be triggered
			$('body').modalmanager('loading');
			$modal.load( _url + 'ps/modal-list/', '', function(){
					$modal.modal();
			});
	});
    
	$('#contact_add').on('click', function(e){
			e.preventDefault();
			// create the backdrop and wait for next modal to be triggered
			$('body').modalmanager('loading');
			$modal.load( _url + 'contacts/modal_add/', '', function(){
					$modal.modal();
					$("#ajax-modal .country").select2({
							theme: "bootstrap"
					});
			});
	});

	function call_select2(id){
		$("#"+id).select2({
			escapeMarkup: function (markup) { return markup; },
			tags: true,
			//minimumInputLength: 2,
			closeOnSelect: true
		}).on('change', function(){
						var sel_id = $("#"+id).val();
						$.post(_url + 'ps/product-details/', {
                id: sel_id
        })
        .success(function(res) {
						var res = $.parseJSON(res);
						tr=$("#"+id).parents('tr');
            //console.log(tr);
						$("#code"+id).text(res.item_number);
            tr.find('[name="amount[]"]').val(res.sales_price);
            qty = tr.find('[name="qty[]"]').val();			
            amt = tr.find('[name="amount[]"]').val();
						tr.find('[name="total[]"]').val(qty*amt);
						//console.log(qty*amt);
        });
		});
	}
	
	$('#blank-add').on('click', function(){
			rowNum++;
			//console.log(opt);
			$.post(_url + 'ps/product-list/', {    
			})
			.success(function(res) {
				$invoice_items.find('tbody').append( '<tr> <td><span id="codei_' + rowNum + '"></span></td> <td><select class="required form-control" id="i_' + rowNum + '" name="desc[]" required 			data-parsley-errors-container="#proerrors'+ rowNum +'"><option value="">Select Product</option>"' +res+ '"</select><span id="proerrors'+ rowNum +'"></span></td> <td><input type="number" class="form-control qty" value="1" name="qty[]"></td> <td><input type="number" class="form-control item_price" id="price'+rowNum+'" name="amount[]" required></td> <td class="ltotal"><input type="number" name="total[]" class="form-control lvtotal" readonly value='+0+'></td> <td> <select class="form-control taxed" name="taxed[]">'+ opt +'</select></td></tr>'  );
					call_select2("i_"+ rowNum);
					
			});		
	});
    
	$invoice_items.on('click', '.redactor-editor', function(){
			$("tr").removeClass("info");
			$(this).closest('tr').addClass("info");

			item_remove.show();
	});
		
	$modal.on('click', '.update', function(){
			/*var tableControl= document.getElementById('items_table');
			$modal.modal('loading');
			$('input:checkbox:checked', tableControl).each(function() {
					rowNum++;
					var item_code = $(this).closest('tr').find('td:eq(1)').text();
					var pid = $(this).closest('tr').find('td:eq(0) .pid').val();
					var id = $(this).closest('tr').find('td:eq(0) .si').val();
					var item_name = $(this).closest('tr').find('td:eq(2)').text();
					var item_desc = $(this).closest('tr').find('td:eq(3)').text();
					var item_desc = item_desc ? ' (' +item_desc+ ')' : '';
					var item_price = $(this).closest('tr').find('td:eq(4)').text();
					get_tax_opt();
            //  obj.push(innertext);
					$invoice_items.find('tbody').append(
                '<tr>'+
				'<td class="number">' + id + '<input type="hidden" class="form-control sid" name="s_id[]" value="' + id + '" id="s_id"><input type="hidden" class="form-control item_id" name="p_id[]" value="' + pid + '" id="p_id"></td>'+'<td><input type="text" class="form-control item_name" name="desc[]" value="' + item_name  + item_desc + '" id="i_' + rowNum + '" required></td>'+' <td><input type="text" class="form-control qty" value="1" name="qty[]"></td> <td><input type="text" class="form-control item_price" name="amount[]" value="' + item_price + '"></td> <td class="ltotal"><input type="number" class="form-control lvtotal" name="total[]" value="' + item_price + '" readonly  required></td><td class="delete"><i class="fa fa-trash tr-remove"></i></td></tr>' );

				sidecalculation();
				});*/


				var tableControl1= document.getElementById('items_table1');
				$modal.modal('loading');
				$('input:checkbox:checked', tableControl1).each(function() {
						rowNum++;
						var item_code = $(this).closest('tr').find('td:eq(1)').text();
						var pid = $(this).closest('tr').find('td:eq(0) .pid').val();
						var id = $(this).closest('tr').find('td:eq(0) .si').val();
						var pimg = $(this).closest('tr').find('td:eq(0) .pimg').val();
						var pimage = pimg ? '<img class="img-popup" data-img="'+pimg+'" width="50px" height="50px" src="'+pimg+'">' : '-';
						var item_name = $(this).closest('tr').find('td:eq(2)').text();
						var item_desc = $(this).closest('tr').find('td:eq(3)').text();
						var item_desc = item_desc ? ' (' +item_desc+ ')' : '';
						var item_price = $(this).closest('tr').find('td:eq(4)').text();
						get_tax_opt();
				//  obj.push(innertext);
						$invoice_items.find('tbody').append(
					'<tr>'+
					'<td class="number">' + pimage + '<input type="hidden" class="form-control sid" name="s_id[]" value="' + id + '" id="s_id"><input type="hidden" class="form-control item_id" name="p_id[]" value="' + pid + '" id="p_id"><input type="hidden" name="pimg[]" value="'+pimg+'"></td>'+'<td><input type="text" class="form-control item_name" name="desc[]" value="' + item_name  + item_desc + '" id="i_' + rowNum + '" required></td>'+' <td><input type="text" class="form-control qty" value="1" name="qty[]"></td> <td><input type="text" class="form-control item_price" name="amount[]" value="' + item_price + '"></td> <td class="ltotal"><input type="number" class="form-control lvtotal" name="total[]" value="' + item_price + '" readonly  required></td><td><input type="hidden" name="item_type[]" value="product"></td><td class="delete"><i class="fa fa-trash tr-remove"></i></td></tr>' );
	
					//sidecalculation();
					});
	
	
					var tableControl2 = document.getElementById('items_table2');
					$('input:checkbox:checked', tableControl2).each(function() {
							rowNum++;
							var item_code = $(this).closest('tr').find('td:eq(1)').text();
							var pid = $(this).closest('tr').find('td:eq(0) .pid').val();
							var id = $(this).closest('tr').find('td:eq(0) .si').val();
							var pimg = $(this).closest('tr').find('td:eq(0) .pimg').val();
							var pimage = pimg ? '<img class="img-popup" data-img="'+pimg+'" width="50px" height="50px" src="'+pimg+'">' : '-';							
							var item_name = $(this).closest('tr').find('td:eq(2)').text();
							var item_desc = $(this).closest('tr').find('td:eq(3)').text();
							var item_desc = item_desc ? ' (' +item_desc+ ')' : '';
							var item_price = $(this).closest('tr').find('td:eq(4)').text();
							get_tax_opt();
					//  obj.push(innertext);
							$invoice_items.find('tbody').append(
						'<tr>'+
						'<td class="number">' + pimage + '<input type="hidden" class="form-control sid" name="s_id[]" value="' + id + '" id="s_id"><input type="hidden" class="form-control item_id" name="p_id[]" value="' + pid + '" id="p_id"><input type="hidden" name="pimg[]" value="'+pimg+'"></td>'+'<td><input type="text" class="form-control item_name" name="desc[]" value="' + item_name  + item_desc + '" id="i_' + rowNum + '" required></td>'+' <td><input type="text" class="form-control qty" value="1" name="qty[]"></td> <td><input type="text" class="form-control item_price" name="amount[]" value="' + item_price + '"></td> <td class="ltotal"><input type="number" class="form-control lvtotal" name="total[]" value="' + item_price + '" readonly  required></td><td><input type="hidden" name="item_type[]" value="product"></td><td class="delete"><i class="fa fa-trash tr-remove"></i></td></tr>' );
		
						//sidecalculation();
						});


						sidecalculation();


        $modal.modal('hide');
	});
	
	$invoice_items.on('click', '.tr-remove',function(){
		event.preventDefault();
		$(this).parents("tr").remove();
		sidecalculation();
	});

	$modal.on('click', '.contact_submit', function(e){
			e.preventDefault();
			//  var tableControl= document.getElementById('items_table');
			$modal.modal('loading');
			var _url = $("#_url").val();
			$.post(_url + 'contacts/add-post/', {
					account: $('#account').val(),
					company: $('#company').val(),
					address: $('#m_address').val(),
					city: $('#city').val(),
					state: $('#state').val(),
					zip: $('#zip').val(),
					country: $('#country').val(),
					phone: $('#phone').val(),
					gst: $('#gst').val(),
					email: $('#email').val()

			}).done(function (data) {
							var _url = $("#_url").val();
							if ($.isNumeric(data)) {
									// location.reload();
									var is_recurring = $('#is_recurring').val();
									if(is_recurring == 'yes'){
											window.location = _url + 'invoices/add/recurring/' + data + '/';
									}
									else{
											window.location = _url + 'invoices/add/1/' + data + '/';
									}
							}else {
									$modal
											.modal('loading')
											.find('.modal-body')
											.prepend('<div class="alert alert-danger fade in">' + data +
											'<button type="button" class="close" data-dismiss="alert">&times;</button>' +
											'</div>');
									$("#cid").select2('data', {id: newID, text: newText});
							}
				});
	});

	$("#add_discount").click(function (e) {
			e.preventDefault();
			var s_discount_amount = $('#discount_amount');
			var c_discount = s_discount_amount.val();
			var c_discount_type = $('#discount_type').val();
			var p_checked = "";
			var f_checked = "";
			if( c_discount_type == "p" ){
					p_checked = 'checked="checked"';
			}else{
					f_checked = 'checked="checked"';
			}
			bootbox.dialog({
							title: $("#_lan_set_discount").val(),
							message: '<div class="row">  ' +
							'<div class="col-md-12"> ' +
							'<form class="form-horizontal" action="javascript:void(0);"> ' +
							'<div class="form-group"> ' +
							'<label class="col-md-4 control-label" for="set_discount">' + $("#_lan_discount").val() +'</label> ' +
							'<div class="col-md-4"> ' +
							'<input id="set_discount" name="set_discount" type="text" class="form-control input-md" value="' + c_discount + '"> ' +
							'</div> ' +
							'</div> ' +
							'<div class="form-group"> ' +
							'<label class="col-md-4 control-label" for="set_discount_type">' + $("#_lan_discount_type").val() +'</label> ' +
							'<div class="col-md-4"> <div class="radio"> <label for="set_discount_type-0"> ' +
							'<input type="radio" name="set_discount_type" id="set_discount_type-0" value="p" ' + p_checked + '> ' +
							'' + $("#_lan_percentage").val() +' (%) </label> ' +
							'</div><div class="radio"> <label for="set_discount_type-1"> ' +
							'<input type="radio" name="set_discount_type" id="set_discount_type-1" value="f" ' + f_checked + '> ' + $("#_lan_fixed_amount").val() +' </label> ' +
							'</div> ' +
							'</div> </div>' +
							'</form> </div>  </div>',
							buttons: {
									success: {
											label: $("#_lan_btn_save").val(),
											className: "btn-success",
											callback: function () {
													var discount_amount = $('#set_discount').val();
													var discount_type = $("input[name='set_discount_type']:checked").val();
													$('#discount_amount').val(discount_amount);
													$('#discount_type').val(discount_type);
													sidecalculation();
											}
									}
							}
					}
			);
	});

    $(".progress").hide();
    $("#emsg").hide();
    $("#submit").click(function (e) {
        
			if($('#invform').parsley().validate()){
				e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'invoices/add-post/', $('#invform').serialize(), function(data){

            var _url = $("#_url").val();
            if ($.isNumeric(data)) {

                window.location = _url + 'invoices/edit/' + data + '/';
            }
            else {
                $('#ibox_form').unblock();
                var body = $("html, body");
                body.animate({scrollTop:0}, '1000', 'swing');
                $("#emsgbody").html(data);
                $("#emsg").show("slow");
            }
        });
			}
			return false;
    });

   /*  $("#save_n_close").click(function (e) {
			if($('#invform').parsley().validate()){
        e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'invoices/add-post/', $('#invform').serialize(), function(data){
            var _url = $("#_url").val();
            if ($.isNumeric(data)) {
                window.location = _url + 'invoices/view/' + data + '/';
            }
            else {
                $('#ibox_form').unblock();
                var body = $("html, body");
                body.animate({scrollTop:0}, '1000', 'swing');
                $("#emsgbody").html(data);
                $("#emsg").show("slow");
            }
        });
			}
			return false;
    }); */
		
		 /*$("#save_n_close").click(function (e) {
		if($('#invform').parsley().validate()){
        e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'invoices/add-performa-post/', $('#invform').serialize(), function(data){
            var _url = $("#_url").val();
            if ($.isNumeric(data)) {
                window.location = _url + 'invoices/view/' + data + '/'; //performa-view
            }
            else {
                $('#ibox_form').unblock();
                var body = $("html, body");
                body.animate({scrollTop:0}, '1000', 'swing');
                $("#emsgbody").html(data);
                $("#emsg").show("slow");
            }
        });
			}
			return false;
			  });*/

	$("#save_n_close").click(function (e) {
		if($('#invform').parsley().validate()){
			e.preventDefault();
			$('#ibox_form').block({ message: null });
			var _url = $("#_url").val();
			var formData = new FormData(document.getElementById("invform"));

			$.ajax({
				type:'POST',
				url: _url + 'invoices/add-performa-post/',
				data:formData,
				cache:false,
				contentType: false,
				processData: false,
				success:function(data){
					if ($.isNumeric(data)) {
						//new
						window.open(_url + 'invoices/redirect_to_customer_invoice_pdf/' + data + '/', '_blank'); // Opens the second URL in a new tab
						window.location = _url + 'invoices/view/' + data + '/'; // Redirects the current tab to the first URL	
					}
					else {
						$('#ibox_form').unblock();
						var body = $("html, body");
						body.animate({scrollTop:0}, '1000', 'swing');
						$("#emsgbody").html(data);
						$("#emsg").show("slow");
					}
				},
				error: function(data){
					console.log("error");
					console.log(data);
				}
			});
			
		}
		return false;
	});			  

			  
	$('body').on('change', '#cloth_id', function(){
		const _url = $("#_url").val();
		const cloth_id = $(this).val();

		$.ajax
		({ 
			url: _url + 'invoices/get-designs-by-clothid/',
			data: {"cloth_id": cloth_id},
			type: 'post',
			success: function(response)
			{
				console.log(response);
				$('#design_id').html(response);
				$('.design-image').html('');
			}
		});		
	});

	$('body').on('change', '#design_id', function(){
		const val = $(this).val();
		const data_image = $('#design_id option[value="'+val+'"]').attr('data-image');
		if(!data_image)
		{
			$('.design-image').html('');
		}
		else
		{
			$('.design-image').html('<a target="_blank" href="'+data_image+'"><img width="100%" src="'+data_image+'"></a>');	
		}
		
	});	



	$('body').on('click', '#get-customized-items', function(){
		const _url      = $("#_url").val();
		const design_id = $("#design_id").val();
		const last_row  = $('.item_name:last').attr("id");

		if(design_id)
		{
			$.ajax
			({ 
				url: _url + 'invoices/get-customized-items/',
				data: {"design_id": design_id, "last_row":last_row},
				type: 'post',
				success: function(response)
				{
					console.log(response);
					$invoice_items.find('tbody').append(response);
					sidecalculation();
				}
			});
		}
		else
		{
            alert('please select design first');
		}
		
	});	
	
	function fetch_on_scan(param){ //products & designs
		var param      = param.split('-');
		id             = param[1];
		type           = param[0].toUpperCase();
		const _url     = $("#_url").val();
		const last_row = $('.item_name:last').attr("id");

		//console.log(design_id);
		//console.log(type);
		if(id)
		{
			if(type == 'D')
			{
				$.ajax
				({ 
					url: _url + 'invoices/get-customized-items/',
					data: {"design_id": id, "last_row":last_row},
					type: 'post',
					success: function(response)
					{
						console.log(response);
						$invoice_items.find('tbody').append(response);
						sidecalculation();
					}
				});
			}
			else if(type == 'P')
			{
				$.ajax
				({ 
					url: _url + 'invoices/get-scanned-product/',
					data: {"product_id": id, "last_row":last_row},
					type: 'post',
					success: function(response)
					{
						console.log(response);
						$invoice_items.find('tbody').append(response);
						sidecalculation();
					}
				});
			}
	    }
	}
	
	var device = document.getElementById("device");
	device.addEventListener("keydown", function (e) {
		if (e.keyCode === 13) {
			fetch_on_scan($('#device').val());
			$('#device').val('');
			setTimeout(function(){ $('#device').focus(); }, 500);
			e.preventDefault();
			return false;
		}
	});	
			  
});