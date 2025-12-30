{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{$_L['List']} {$_L['Products']}</h5>
                {if $user->roleid eq 0}
                    <div class="ibox-tools">
                        <a href="{$_url}ps/p-new" class="btn btn-primary btn-xs">
                            <i class="fa fa-plus"></i> {$_L['Add Product']}</a>
                    </div>
                {/if}
            </div>

            <div class="ibox-content">
                <form id="productFilters" style="margin-bottom:15px;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="product_type">Product Type</label>
                                <select name="product_type" id="product_type" class="form-control">
                                    <option value="all">All</option>
                                    <option value="readymade" {if $product_type eq 'readymade'}selected{/if}>Readymade</option>
                                    <option value="customize" {if $product_type eq 'customize'}selected{/if}>Customize</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="product_category">Category</label>
                                <select name="product_category" id="product_category" class="form-control">
                                    <option value="">All</option>
                                    {foreach $categories as $cat}
                                        <option value="{$cat.product_category}">{str_replace('_', ' ', $cat.product_category)}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    {*
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="query">Name / Code</label>
                                <input type="text" name="query" id="query" class="form-control" placeholder="Search name or code">
                            </div>
                        </div>
                    *}
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button id="btnProductFilter" class="btn btn-primary">Filter</button>
                            <button id="btnProductReset" type="button" class="btn btn-default">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table id="product-datatable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{$_L['Item Code']}</th>
                                <th>{$_L['Item Name']}</th>
                                <th>Type</th>
                                <th>Purchase Price</th>
                                <th>{$_L['Price']}</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Image</th>
                                <th>{$_L['Description']}</th>
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

<script>
    var defaultProductType = '{$product_type|escape:"javascript"}';
</script>
{literal}
<script>
$(function(){
    var $filters = $('#productFilters');
    var $modal = $('#ajax-modal');
    var defaultType = window.defaultProductType || 'readymade';

    $('#product_type').val(defaultType);

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

    var table = $('#product-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "ps/p-list-datatable",
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
            { orderable: false, targets: [11] },
            { className: 'text-right', targets: [4,5] }
        ],
        drawCallback: function(){
            attachRowHandlers();
        }
    });

    $('#btnProductFilter').on('click', function(e){
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btnProductReset').on('click', function(){
        $filters[0].reset();
        $('#product_type').val(defaultType || 'readymade');
        table.ajax.reload();
    });

    $('#productFilters input').on('keypress', function(e){
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
                $modal.load(base_url + 'ps/edit-form/' + id, '', function(){
                    $modal.modal();
                });
            }, 200);
        });

        $('.cedit_stock').off('click').on('click', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            $('body').modalmanager('loading');
            setTimeout(function(){
                $modal.load(base_url + 'ps/edit-form-stock/' + id, '', function(){
                    $modal.modal();
                });
            }, 200);
        });

        $('.cdelete-product').off('click').on('click', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            var type = $('#product_type').val() || 'all';
            var csrf = $('#csrf_token').val();
            bootbox.confirm($("#_lan_are_you_sure").val(), function(result){
                if(result){
                    $.post(base_url + 'ps/ajax-delete', {id: id, product_type: type, _token: csrf}, function(res){
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
