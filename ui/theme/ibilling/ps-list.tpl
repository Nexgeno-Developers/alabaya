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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="product_branch_id">Branch</label>
                                <select name="branch_id" id="product_branch_id" class="form-control">
                                    <option value="">All Branches</option>
                                    {foreach $branches as $branch}
                                        <option value="{$branch.id}">{$branch.alias|default:$branch.account}</option>
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
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Totals</th>
                                <th id="product-purchase-total" class="text-right">0.00</th>
                                <th id="product-sales-total" class="text-right">0.00</th>
                                <th colspan="6"></th>
                            </tr>
                        </tfoot>
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

    function serverSideExportAction(buttonType){
        return function(e, dt, button, config){
            var context = this;
            var oldStart = dt.settings()[0]._iDisplayStart;

            dt.one('preXhr', function(e, settings, data){
                data.start = 0;
                data.length = -1;

                dt.one('preDraw', function(e, settings){
                    $.fn.dataTable.ext.buttons[buttonType].action.call(context, e, dt, button, config);

                    dt.one('preXhr', function(e, settings, data){
                        settings._iDisplayStart = oldStart;
                        data.start = oldStart;
                    });

                    setTimeout(function(){
                        dt.ajax.reload(null, false);
                    }, 0);
                    return false;
                });
            });

            dt.ajax.reload();
        };
    }

    function updateProductTotals(json){
        var page = json && json.totals ? json.totals.page : null;
        var filtered = json && json.totals ? json.totals.filtered : null;

        function totalHtml(pageValue, filteredValue){
            pageValue = pageValue || '0.00';
            filteredValue = filteredValue || '0.00';
            return '<div title="Sum of rows displayed on this page">' + pageValue + '</div>' +
                '<small class="text-muted">Filtered: ' + filteredValue + '</small>';
        }

        $('#product-purchase-total').html(totalHtml(
            page ? page.purchase_price : null,
            filtered ? filtered.purchase_price : null
        ));
        $('#product-sales-total').html(totalHtml(
            page ? page.sales_price : null,
            filtered ? filtered.sales_price : null
        ));
    }

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
            {
                extend: 'excelHtml5',
                text: 'Excel',
                title: 'Readymade Product List',
                action: serverSideExportAction('excelHtml5'),
                exportOptions: { columns: [0,1,2,3,4,5,6,7,9] }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                title: 'Readymade Product List',
                orientation: 'landscape',
                pageSize: 'A4',
                action: serverSideExportAction('pdfHtml5'),
                exportOptions: { columns: [0,1,2,3,4,5,6,7,9] }
            },
            {
                extend: 'print',
                text: 'Print',
                title: 'Readymade Product List',
                action: serverSideExportAction('print'),
                exportOptions: { columns: [0,1,2,3,4,5,6,7,9] }
            },
            'pageLength'
        ],
        lengthMenu: [
            [10,25,50,100,-1],
            [10,25,50,100,'All']
        ],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [8,10,11] },
            { className: 'text-right', targets: [4,5] }
        ],
        drawCallback: function(){
            updateProductTotals(this.api().ajax.json());
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
