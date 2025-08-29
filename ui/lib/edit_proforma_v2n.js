$(document).ready(function () {

	var _url = $("#_url").val();
	var $invoice_items = $('#invoice_items');
	var item_remove = $('#item-remove');
	update_address();
	var rowNum = $("#rowcount").val();
	$(".progress").hide();
  $("#emsg").hide();
	var $modal = $('#ajax-modal');
  var opt = get_tax_opt();
	var i;
	
/* 	for(i=0; i < rowNum; i++){
		call_select2("i_"+i);
		console.log(i);
	} */
	
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
		console.log(tax);
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

    $('.item_name').redactor({paragraphize: false,
        replaceDivs: false,
        linebreaks: true});

    function update_address(){
        var _url = $("#_url").val();
        var cid = $('#cid').val();
        if(cid != ''){
            $.post(_url + 'contacts/render-address/', {
                cid: cid
            })
            .done(function (data) {
								var adrs = $("#address");
                    adrs.html(data);
            });
        }
    }
    
    $('#cid').select2({
        // theme: "bootstrap",
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    })
		.on("change", function(e) {
				update_address();
		});

    item_remove.on('click', function(){
      if(rowNum <= 1){  
				bootbox.alert("Atleast one required");
			}else{
				$("#invoice_items tr:last").fadeOut(300, function(){
            $(this).remove();
						rowNum--;
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

	/* function call_select2(id){
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
	} */
    $('#blank-add').on('click', function(){
				$.post(_url + 'ps/product-list/', {    
        })
        .success(function(res) {
					$invoice_items.find('tbody').append( '<tr> <td><span id="codei_' + rowNum + '"></span></td> <td><select class="required form-control" id="i_' + rowNum + '" name="desc[]" required 			data-parsley-errors-container="#proerrors'+ rowNum +'"><option value="">Select Product</option>"' +res+ '"</select><span id="proerrors'+ rowNum +'"></span></td> <td><input type="number" class="form-control qty" value="1" name="qty[]"></td> <td><input type="number" class="form-control item_price" id="price'+rowNum+'" name="amount[]" required></td> <td class="ltotal"><input type="number" name="total[]" class="form-control lvtotal" readonly value=""></td> <td> <select class="form-control taxed" name="taxed[]">'+ opt +'</select></td></tr>'  );
						call_select2("i_"+ rowNum);	
						rowNum++;
				});				
    });

    $invoice_items.on('click', '.redactor-editor', function(){
        $("tr").removeClass("info");
        $(this).closest('tr').addClass("info");
        item_remove.show();
    });

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
		
    $modal.on('click', '.update', function(){
        var tableControl= document.getElementById('items_table');
        $modal.modal('loading');
        $('input:checkbox:checked', tableControl).each(function() {
            rowNum++;
            var item_code = $(this).closest('tr').find('td:eq(1)').text();
						var pid = $(this).closest('tr').find('td:eq(0) .pid').val();
			var id = $(this).closest('tr').find('td:eq(0) .si').val();
            var item_name = $(this).closest('tr').find('td:eq(2)').text();
            var item_price = $(this).closest('tr').find('td:eq(3)').text();
						get_tax_opt();
            //  obj.push(innertext);
            $invoice_items.find('tbody')
                .append(
                '<tr>'+
				'<td class="number">' + id + '<input type="hidden" class="form-control sid" name="s_id[]" value="' + id + '" id="s_id"><input type="hidden" class="form-control item_id" name="p_id[]" value="' + pid + '" id="p_id"></td>'+'<td><input type="text" class="form-control item_name" name="desc[]" value="' + item_name + '" id="i_' + rowNum + '" required></td>'+' <td><input type="text" class="form-control qty" value="1" name="qty[]"></td> <td><input type="text" class="form-control item_price" name="amount[]" value="' + item_price + '"></td> <td class="ltotal"><input type="number" class="form-control lvtotal" name="total[]" value="" readonly  required></td></tr>' );
				});
        $modal.modal('hide');
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
            email: $('#email').val()

        })
            .done(function (data) {

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

                }
                else {


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
                '<form class="form-horizontal"> ' +
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

    $("#submit").click(function (e) {
        e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'invoices/edit-proforma-post/', $('#invform').serialize(), function(data){

            var _url = $("#_url").val();
            if ($.isNumeric(data)) {

                window.location = _url + 'invoices/proforma-edit/' + data + '/';
            }
            else {
                $('#ibox_form').unblock();
                var body = $("html, body");
                body.animate({scrollTop:0}, '1000', 'swing');
                $("#emsgbody").html(data);
                $("#emsg").show("slow");
            }
        });
    });


    $("#save_n_close").click(function (e) {
        e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'invoices/edit-proforma-post/', $('#invform').serialize(), function(data){

            var _url = $("#_url").val();
            if ($.isNumeric(data)) {
                window.location = _url + 'invoices/performa-view/' + data + '/';
            }
            else {
                $('#ibox_form').unblock();
                var body = $("html, body");
                body.animate({scrollTop:0}, '1000', 'swing');
                $("#emsgbody").html(data);
                $("#emsg").show("slow");
            }
        });
    });

    //function doStuff() {
    //    $('.amount').autoNumeric('init');
    //   // alert('dd');
    //}
    //setInterval(doStuff, 5000);

});