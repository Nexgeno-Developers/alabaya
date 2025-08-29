Dropzone.autoDiscover = false;
$(document).ready(function () {
    //$('.amount').autoNumeric('init');

    $("#account").select2({
            theme: "bootstrap",
            language: {
                noResults: function () {
                    return $("#_lan_no_results_found").val();
                }
            }
        }
    );
    $("#cats").select2({
            theme: "bootstrap",
            language: {
                noResults: function () {
                    return $("#_lan_no_results_found").val();
                }
            }
        }
    );
    $("#pmethod").select2({
            theme: "bootstrap",
            language: {
                noResults: function () {
                    return $("#_lan_no_results_found").val();
                }
            }
        }
    );
    $("#payee").select2({
            theme: "bootstrap",
            language: {
                noResults: function () {
                    return $("#_lan_no_results_found").val();
                }
            }
        }
    );

    $("#invoice_id").select2({
            theme: "bootstrap",
            language: {
                noResults: function () {
                    return $("#_lan_no_results_found").val();
                }
            }
        }
    );
    
    $("#vendor_id").select2({
        theme: "bootstrap",
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    }
);    

    $('#tags').select2({
        tags: true,
        tokenSeparators: [','],
        theme: "bootstrap",
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    });

    $('#payee').on('change', function(){
        get_customer_invoices();
    });

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
    
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
    
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };   
    
    function autofillexpenseform()
    {
        const userid = getUrlParameter('userid');
        const invoiceid = getUrlParameter('invoiceid');
        console.log(userid);
        console.log(invoiceid);
        if(userid != false)
        {
            console.log('triggered1');
            $("#payee").val(userid).trigger('change');
            get_customer_invoices();    
            setTimeout(function(){ 
                $("#invoice_id").val(invoiceid).trigger('change'); 
                $('#a_hide').attr('style', 'display: block;'); 
            }, 1000);
        }
    } 
    
    function get_customer_invoices()
    {
        var url = $("#_url").val();
        const id   = $("#payee option:selected").val();
        const name = $("#payee option:selected").text();
        $.post(url + 'transactions/expense-get-customer-invoices/', {
            id: id
        }).done(function (data) {
           $('#invoice_id').html(data);
        });
    }

    autofillexpenseform();

    $("#a_hide").hide();
    $("#emsg").hide();
    $("#a_toggle").click(function(e){
        e.preventDefault();
        $("#a_hide").toggle( "slow" );
    });
    var _url = $("#_url").val();




    //  file attach

    var upload_resp;

    var $ib_form_submit = $("#submit");


    var ib_file = new Dropzone("#upload_container",
        {
            url: _url + "transactions/handle_attachment/",
            maxFiles: 1,
            acceptedFiles: "image/*,application/pdf"
        }
    );


    ib_file.on("sending", function() {

        $ib_form_submit.prop('disabled', true);

    });

    ib_file.on("success", function(file,response) {

        $ib_form_submit.prop('disabled', false);

        upload_resp = response;

        if(upload_resp.success == 'Yes'){

            toastr.success(upload_resp.msg);
            // $file_link.val(upload_resp.file);
            // files.push(upload_resp.file);
            //
            // console.log(files);

            $('#attachments').val(function(i,val) {
                return val + (!val ? '' : ',') + upload_resp.file;
            });


        }
        else{
            toastr.error(upload_resp.msg);
        }







    });


    $ib_form_submit.click(function (e) {
        e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'transactions/expense-post/', {


            account: $('#account').val(),
            date: $('#date').val(),

            amount: $('#amount').val(),
            cats: $('#cats').val(),
            description: $('#description').val(),
            attachments: $('#attachments').val(),
            tags: $('#tags').val(),
            payee: $('#payee').val(),
            pmethod: $('#pmethod').val(),
            ref: $('#ref').val(),
            invoice_id: $('#invoice_id').val(),
            vendor_id: $('#vendor_id').val(),
            timesheet_ids: $('#timesheet_ids').val() 
        })
            .done(function (data) {
                var sbutton = $("#submit");
                var _url = $("#_url").val();
                if ($.isNumeric(data)) {
                    // Reload page without params
                    window.location.href = _url + 'transactions/expense';
                    // location.reload();
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
});