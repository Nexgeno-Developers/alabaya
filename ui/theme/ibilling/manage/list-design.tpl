{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>List Design</h5>
                {if $user->roleid eq 0}
                    <div class="ibox-tools">
                        <a href="{$_url}manage/add-design" class="btn btn-primary btn-xs">
                            <i class="fa fa-plus"></i> Add Design</a>
                    </div>
                {/if}
            </div>
            <div class="ibox-content">

                <form id="designFilters" style="margin-bottom:15px;">
                    <div class="row">
                        {*
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="design_name">Design Name</label>
                                <input type="text" name="design_name" id="design_name" class="form-control" placeholder="Search name">
                            </div>
                        </div>
                        *}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cloth_id">Cloth</label>
                                <select name="cloth_id" id="cloth_id" class="form-control">
                                    <option value="">All</option>
                                    {foreach $cloths as $cloth}
                                        <option value="{$cloth.id}">{$cloth.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="min_price">Min Price</label>
                                <input type="number" step="0.01" name="min_price" id="min_price" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="max_price">Max Price</label>
                                <input type="number" step="0.01" name="max_price" id="max_price" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button id="btnDesignFilter" class="btn btn-primary">Filter</button>
                            <button id="btnDesignReset" type="button" class="btn btn-default">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table id="design-datatable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Cloth</th>
                                <th>Silai Price</th>
                                <th>Image</th>
                                <th>QRCode</th>
                                <th class="text-right">{$_L['Manage']}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<input type="hidden" id="_lan_are_you_sure" value="{$_L['are_you_sure']}">
{include file="sections/footer.tpl"}

{literal}
<script>
$(function(){
    var $filters = $('#designFilters');
    var $modal = $('#ajax-modal');

    $.fn.serializeObject = function(){
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    var table = $('#design-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "manage/list-design-datatable",
            type: 'POST',
            data: function(d){
                return $.extend({}, d, $filters.serializeObject());
            }
        },
        dom: 'Bfrtip',
        buttons: [
            'pageLength'
        ],
        lengthMenu: [
            [10,25,50,100,-1],
            [10,25,50,100,'All']
        ],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [6] },
            { className: 'text-right', targets: [3,6] }
        ],
        drawCallback: function(){
            attachRowHandlers();
        }
    });

    $('#btnDesignFilter').on('click', function(e){
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btnDesignReset').on('click', function(){
        $filters[0].reset();
        table.ajax.reload();
    });

    $('#designFilters input').on('keypress', function(e){
        if (e.which == 13) {
            e.preventDefault();
            table.ajax.reload();
        }
    });

    function attachRowHandlers(){
        $('.cedit').off('click').on('click', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            $('body').modalmanager('loading');
            setTimeout(function(){
                $modal.load(base_url + 'manage/edit-form/' + id, '', function(){
                    $modal.modal();
                });
            }, 200);
        });

        $('.cdelete-design').off('click').on('click', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            var csrf = $('#csrf_token').val();
            bootbox.confirm($("#_lan_are_you_sure").val(), function(result){
                if(result){
                    $.post(base_url + 'manage/ajax-delete', {id: id, _token: csrf}, function(res){
                        if(res.success){
                            table.ajax.reload(null, false);
                            toastr.success(res.message);
                        }else{
                            toastr.error(res.message || 'Unable to delete');
                        }
                    }, 'json');
                }
            });
        });
    }
});
</script>
{/literal}
