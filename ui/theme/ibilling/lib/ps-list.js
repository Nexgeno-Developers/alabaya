$(document).ready(function () {



       var pbar = $('#progressbar');

    pbar.hide();



    pbar.progressbar({

        warningMarker: 100,

        dangerMarker: 100,

        maximum: 100,

        step: 15

    });



    function updateDiv(){

     //   $("#application_ajaxrender").html('Loading...');

        $('#ibox_form').block({ message: null });

        $('#progressbar').show();



        var timer = setInterval(function () {

            pbar.progressbar('stepIt');



        }, 250);



        var btnsearch = $("#search");

        $('.progress').show();

        $('.progress .progress-bar').progressbar();

        //btnsearch.html(_L['Searching']);

        //btnsearch.addClass("btn-danger");

        var _url = $("#_url").val();

        $.post(_url + 'search/ps/', {



            txtsearch: $('#txtsearch').val(),

            stype: $('#stype').val()



        })

            .done(function (data) {



                setTimeout(function () {

                    var sbutton = $("#search");

                    var result =  $("#application_ajaxrender");

                    //sbutton.html('Search');

                    //sbutton.removeClass("btn-danger");

                    $('.progress').hide();

                    $('#ibox_form').unblock();

                    result.html(data);

                    result.show();



                }, 2000);

            });



    }



    updateDiv();



    $("#search").click(function (e) {

        e.preventDefault();

        updateDiv();

    });

    var $modal = $('#ajax-modal');

    //var sysrender = $('#application_ajaxrender');
    var sysrender = $('#application_ajaxrender1');

    sysrender.on('click', '.cdelete', function(e){

        e.preventDefault();

        var id = this.id;

        var data_filter = $(this).attr('data-filter');

        var lan_msg = $("#_lan_are_you_sure").val();

        bootbox.confirm(lan_msg, function(result) {

            if(result){

                var _url = $("#_url").val();

                window.location.href = _url + "delete/ps/" + id + '/' + data_filter;

            }

        });

    });



    sysrender.on('click', '.cedit', function(e){

        e.preventDefault();

        var vid = this.id;

        var id = vid.replace("e", "");

        var id = id.replace("t", "");



       // var id = $(this).closest('tr').attr('id');

        // create the backdrop and wait for next modal to be triggered

        $('body').modalmanager('loading');

        var _url = $("#_url").val();

        setTimeout(function(){

            $modal.load(_url + 'ps/edit-form/' + id, '', function(){

                $modal.modal();

            });

        }, 1000);

    });


    sysrender.on('click', '.cedit_stock', function(e){
        e.preventDefault();
        var vid = this.id;
        var id = vid.replace("e", "");
        var id = id.replace("t", "");
        $('body').modalmanager('loading');
        var _url = $("#_url").val();
        setTimeout(function(){
            $modal.load(_url + 'ps/edit-form-stock/' + id, '', function(){
                $modal.modal();
            });
        }, 1000);
    });    



    /*$modal.on('click', '#update', function(){

        $modal.modal('loading');

        setTimeout(function(){





            var _url = $("#_url").val();

            $.post(_url + 'ps/edit-post/', $('#edit_form').serialize(), function(data){



                setTimeout(function () {



                    var _url = $("#_url").val();

                    if ($.isNumeric(data)) {



                        location.reload();

                    }

                    else {



                        $modal

                            .modal('loading')

                            .find('.modal-body')

                            .prepend('<div class="alert alert-danger fade in">' + data +



                            '</div>');



                    }

                }, 2000);

            });

        }, 1000);



    });*/



    $modal.on('click', '#update', function(){
        $modal.modal('loading');
        setTimeout(function(){
            var _url = $("#_url").val();
            var formData = new FormData($('#edit_form')[0]);
            $.ajax({
                url: _url + 'ps/edit-post/',
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    setTimeout(function () {
                        var _url = $("#_url").val();
                        if ($.isNumeric(data)) {
                            location.reload();
                        }
                        else{
                            $modal.modal('loading').find('.modal-body').prepend('<div class="alert alert-danger fade in">' + data + '</div>');
                        }
                    }, 2000);
                },
                error: function () {
                    alert("error in ajax form submission");
                }
            });
        }, 1000);
    });



    $modal.on('click', '#update_stock', function(){
        const form = $('#edit_form_stock')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return false; // stop if form invalid
        }
        $modal.modal('loading');
        setTimeout(function(){
            var _url = $("#_url").val();
            var formData = new FormData(form);
            $.ajax({
                url: _url + 'ps/edit-stock-post/',
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    setTimeout(function () {
                        var _url = $("#_url").val();
                        if ($.isNumeric(data)) {
                            location.reload();
                        }
                        else{
                            $modal.modal('loading').find('.modal-body').prepend('<div class="alert alert-danger fade in">' + data + '</div>');
                        }
                    }, 2000);
                },
                error: function () {
                    alert("error in ajax form submission");
                }
            });
        }, 1000);
    });    







});