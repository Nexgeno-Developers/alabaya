{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Most Selling Readymade Products</h5>
            </div>
            <div class="ibox-content">
                <form id="sellingProductFilters" style="margin-bottom:15px;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="selling_product_date_from">From Date</label>
                                <input type="date" name="date_from" id="selling_product_date_from" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="selling_product_date_to">To Date</label>
                                <input type="date" name="date_to" id="selling_product_date_to" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="selling_product_branch_id">Branch</label>
                                <select name="branch_id" id="selling_product_branch_id" class="form-control">
                                    {if $user->roleid eq 0 or $user->user_type eq 'Tailor'}
                                        <option value="">All Branches</option>
                                        {foreach $branches as $branch}
                                            <option value="{$branch.id}">{$branch.alias|default:$branch.account}</option>
                                        {/foreach}
                                    {else}
                                        {foreach $branches as $branch}
                                            {if $branch.id eq $user->branch_id}
                                                <option value="{$branch.id}" selected>{$branch.alias|default:$branch.account}</option>
                                            {/if}
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="selling-product-date-error" class="text-danger"></div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button id="btnSellingProductReset" type="button" class="btn btn-default">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table id="selling-product-datatable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Variant / Code</th>
                                <th class="text-right">Total Quantity Sold</th>
                                <th class="text-right">Total Sales Amount</th>
                                <th class="text-right">Invoices</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}

{literal}
<script>
$(function(){
    var $filters = $('#sellingProductFilters');
    var $dateError = $('#selling-product-date-error');

    $.fn.serializeObject = function(){
        var o = {};
        var a = this.serializeArray();
        $.each(a, function(){
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

    function validDateRange(){
        var from = $('#selling_product_date_from').val();
        var to = $('#selling_product_date_to').val();
        $dateError.text('');

        if (from && to && to < from) {
            $dateError.text('To Date cannot be earlier than From Date.');
            return false;
        }
        return true;
    }

    var table = $('#selling-product-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'ps/most-selling-readymade-datatable',
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
                title: 'Most Selling Readymade Products',
                action: serverSideExportAction('excelHtml5'),
                exportOptions: { columns: [0,1,2,3,4,5] }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                title: 'Most Selling Readymade Products',
                action: serverSideExportAction('pdfHtml5'),
                exportOptions: { columns: [0,1,2,3,4,5] }
            },
            {
                extend: 'print',
                text: 'Print',
                title: 'Most Selling Readymade Products',
                action: serverSideExportAction('print'),
                exportOptions: { columns: [0,1,2,3,4,5] }
            },
            'pageLength'
        ],
        lengthMenu: [
            [10,25,50,100,-1],
            [10,25,50,100,'All']
        ],
        order: [[3, 'desc']],
        columnDefs: [
            { orderable: false, targets: [0] },
            { className: 'text-right', targets: [3,4,5] }
        ]
    });

    $filters.on('submit', function(e){
        e.preventDefault();
        if (validDateRange()) {
            table.ajax.reload();
        }
    });

    $('#btnSellingProductReset').on('click', function(){
        $filters[0].reset();
        $dateError.text('');
        table.ajax.reload();
    });
});
</script>
{/literal}
