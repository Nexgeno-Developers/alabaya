$(document).ready(function () {
  $('.amount').autoNumeric('init');
	var item_remove = $('#item-remove');
	var invoice_items = $('#invoice_items');
	var _url = $("#_url").val();
	var $modal = $('#ajax-modal');
	var rowNum = 0;
	call_select2("i_0");
  update_address();
	$(".progress").hide();
  $("#emsg").hide();
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
					invoice_items.find('tbody').append( '<tr> <td><span id="codei_' + rowNum + '"></span></td> <td><select class="required form-control" id="i_' + rowNum + '" name="desc[]" required 			data-parsley-errors-container="#proerrors'+ rowNum +'"><option value="">Select Product</option>"' +res+ '"</select><span id="proerrors'+ rowNum +'"></span></td> <td><input type="number" class="form-control qty" value="1" name="qty[]"></td> <td><input type="number" class="form-control item_price" id="price'+rowNum+'" name="amount[]" required></td> <td class="ltotal"><input type="text" name="total[]" class="form-control lvtotal" readonly value=""></td> <td> <select class="form-control taxed" name="taxed[]">'+ opt +'</select></td></tr>'  );
						call_select2("i_"+ rowNum);		
				});		
		});

		invoice_items.on('change keyup', '.qty, .item_price', function() {
				tr=$(this).parents('tr');
				var qty = tr.find('[name="qty[]"]').val();
				var rate = tr.find('[name="amount[]"]').val();
				tr.find('[name="total[]"]').val(qty*rate);
		}).keyup();
	
    function update_address(){
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
        theme: "classic",
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    }).on("change", function(e) {
            update_address();
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

		item_remove.on('click', function(){
      if(rowNum <= 0){  
				bootbox.alert("Atleast one required");
			}else{
				$("#invoice_items tr:last").fadeOut(300, function(){
            $(this).remove();
						rowNum--;
        });
			}
    });

    $modal.on('click', '.update', function(){
        var tableControl= document.getElementById('items_table');
        $modal.modal('loading');

        $('input:checkbox:checked', tableControl).each(function() {

            var item_code = $(this).closest('tr').find('td:eq(1)').text();
            var item_name = $(this).closest('tr').find('td:eq(2)').text();
            var item_price = $(this).closest('tr').find('td:eq(3)').text();

            //  obj.push(innertext);
            $("#invoice_items").find('tbody')
                .append(
                '<tr> <td>' + item_code + '</td> <td><textarea class="form-control item_name" name="desc[]" rows="3">' + item_name + '</textarea></td> <td><input type="text" class="form-control qty" value="1" name="qty[]"></td> <td><input type="text" class="form-control item_price" name="amount[]" value="' + item_price + '"></td> <td class="ltotal"><input type="text" class="form-control lvtotal" readonly value=""></td> <td> <select class="form-control taxed" name="taxed[]"> <option value="Yes">Yes</option> <option value="No" selected>No</option></select></td></tr>'
            );
        });
        //  console.debug(obj); // Write it to the console
        $modal.modal('hide');
    });

    $modal.on('click', '.contact_submit', function(e){
        e.preventDefault();
        //  var tableControl= document.getElementById('items_table');
        $modal.modal('loading');
        //  $modal.modal('loading');

        var _url = $("#_url").val();
        $.post(_url + 'contacts/add-post/', {
            account: $('#account').val(),
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
                    window.location = _url + 'quotes/new/1/' + data + '/';

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
                        }
                    }
                }
            }
        );
    });

    $("#submit").click(function (e) {
			if($('#invform').parsley().validate()){
        e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'quotes/add-post/', $('#invform').serialize(), function(data){

            var _url = $("#_url").val();
            if ($.isNumeric(data)) {

                window.location = _url + 'quotes/edit/' + data + '/';
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

		$('#customer_notes, #proposal_text').redactor({
            minHeight: 100, // pixels
            plugins: ['fontcolor']
    });

});