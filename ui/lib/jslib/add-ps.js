$(document).ready(function () {

    $('#design_id').select2();

    $(".progress").hide();

    $("#emsg").hide();

    /*$("#submit").click(function (e) {
        e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'ps/add-post/', {
            name: $('#name').val(),
            sales_price: $('#sales_price').val(),
            item_number: $('#item_number').val(),
            description: $('#description').val(),
            type: $('#type').val(),
            purchase_price: $('input[name="purchase_price"]').val(),
            product_type: $('select[name="product_type"]').val(),
            product_category: $('select[name="product_category"]').val(),
            product_stock: $('input[name="product_stock"]').val(),
            product_stock_type: $('input[name="product_stock_type"]').val(),
        })
        .done(function (data) {
            setTimeout(function () {
                var sbutton = $("#submit");
                var _url = $("#_url").val();
                if ($.isNumeric(data)) {
                    location.reload();
                }
                else {
                    $('#ibox_form').unblock();
                    $("#emsgbody").html(data);
                    $("#emsg").show("slow");
                }
            }, 2000);
        });
    });*/

    $("#submit").click(function (event) {
        $('#ibox_form').block({ message: null });
        event.preventDefault(); 
        var formData = new FormData($('#rform')[0]);
        var _url = $("#_url").val();
        $.ajax({
            url: _url + 'ps/add-post/',
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                setTimeout(function () {
                    var sbutton = $("#submit");
                    var _url = $("#_url").val();
                    if ($.isNumeric(data)) {
                        location.reload();
                    }
                    else {
                        $('#ibox_form').unblock();
                        $("#emsgbody").html(data);
                        $("#emsg").show("slow");
                    }
                }, 2000);
            },
            error: function () {
                alert("error in ajax form submission");
            }
        });
    
        //return false;
    });

    $('body').on('change', '#design_id', function(event){
        //alert($(this).val());
        event.preventDefault();
        var _url = $("#_url").val();
        var design_id = $(this).val();
        $.ajax({
            url: _url + 'ps/get-design-subproduct-amount/',
            type: 'POST',
            data: { design_id : design_id },
            success: function (data) {
                var result = $.parseJSON(data);
                console.log(result);
                $('#purchase_price').val(result.purchase_p);
                $('#sales_price').val(result.sale_p);
                $('#ready_img').attr('src', result.img);
                $('input[name="ready_img"]').val(result.img);                
            },
            error: function () {
                alert("error in ajax form submission");
            }
        });        
    });    


});