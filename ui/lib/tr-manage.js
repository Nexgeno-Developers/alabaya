$(document).ready(function () {

    $('.amount').autoNumeric('init');

    $("#account").select2({
        // theme: "bootstrap",
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    });

    $("#cats").select2({
        // theme: "bootstrap",
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    });

    $("#pmethod").select2({
        // theme: "bootstrap",
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    });

    $(".s2").select2({
        // theme: "bootstrap",
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    });

    $('#tags').select2({
        tags: true,
        tokenSeparators: [','],
        // theme: "bootstrap",
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    });


    var select2Config = {
        placeholder: $("#_lan_select_payer").val() || "Select Contact...",
        allowClear: true,
        width: '100%',   // 👈 keeps full width
        ajax: {
            url: base_url + "contacts/ajax_search_contacts",
            dataType: 'json',
            delay: 100,
            data: function (params) {
                return {
                    q: params.term || '',
                    page: params.page || 1
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
        minimumInputLength: 3,
        language: {
            noResults: function () {
                return $("#_lan_no_results_found").val();
            }
        }
    };

    if ($("#payer").length) {
        $("#payer").select2(select2Config);
    }

    if ($("#payee").length) {
        $("#payee").select2(select2Config);
    }


    $("#a_hide").hide();
    $("#emsg").hide();
    $("#a_toggle").click(function(e){
        e.preventDefault();
        $("#a_hide").toggle( "slow" );
    });


    var trtype =  $('#trtype').val();
     trtype = trtype.toLowerCase();

    var _url = $("#_url").val();

    $("#submit").click(function (e) {
        e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'transactions/edit-post/', {



            date: $('#date').val(),

            id: $('#trid').val(),
            cats: $('#cats').val(),
            description: $('#description').val(),
            tags: $('#tags').val(),

            pmethod: $('#pmethod').val(),
            payee: $('#payee').val(),
            payer: $('#payer').val(),
            ref: $('#ref').val()

        })
            .done(function (data) {

                var sbutton = $("#submit");
                var _url = $("#_url").val();
                if ($.isNumeric(data)) {

                    location.reload();
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

    $("#proforma_submit").click(function (e) {
        e.preventDefault();
        $('#ibox_form').block({ message: null });
        var _url = $("#_url").val();
        $.post(_url + 'transactions/edit-proforma-post/', {
            date: $('#date').val(),
            id: $('#trid').val(),
            cats: $('#cats').val(),
            description: $('#description').val(),
            tags: $('#tags').val(),

            pmethod: $('#pmethod').val(),
            payee: $('#payee').val(),
            payer: $('#payer').val(),
            ref: $('#ref').val()

        })
            .done(function (data) {

                var sbutton = $("#proforma_submit");
                var _url = $("#_url").val();
                if ($.isNumeric(data)) {

                    location.reload();
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