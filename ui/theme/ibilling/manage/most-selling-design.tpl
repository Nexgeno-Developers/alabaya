{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Most Selling Designs</h5>
            </div>
            <div class="ibox-content">
                <form id="sellingDesignFilters" style="margin-bottom:15px;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="selling_design_date_from">From Date</label>
                                <input type="date" name="date_from" id="selling_design_date_from" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="selling_design_date_to">To Date</label>
                                <input type="date" name="date_to" id="selling_design_date_to" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="selling-design-date-error" class="text-danger"></div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button id="btnSellingDesignReset" type="button" class="btn btn-default">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table id="selling-design-datatable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Design Name</th>
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
    var $filters = $('#sellingDesignFilters');
    var $dateError = $('#selling-design-date-error');

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
        var from = $('#selling_design_date_from').val();
        var to = $('#selling_design_date_to').val();
        $dateError.text('');

        if (from && to && to < from) {
            $dateError.text('To Date cannot be earlier than From Date.');
            return false;
        }
        return true;
    }

    var table = $('#selling-design-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'manage/most-selling-design-datatable',
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
                title: 'Most Selling Designs',
                action: serverSideExportAction('excelHtml5'),
                exportOptions: { columns: [0,1,2,3,4] }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                title: 'Most Selling Designs',
                action: serverSideExportAction('pdfHtml5'),
                exportOptions: { columns: [0,1,2,3,4] }
            },
            {
                extend: 'print',
                text: 'Print',
                title: 'Most Selling Designs',
                action: serverSideExportAction('print'),
                exportOptions: { columns: [0,1,2,3,4] }
            },
            'pageLength'
        ],
        lengthMenu: [
            [10,25,50,100,-1],
            [10,25,50,100,'All']
        ],
        order: [[2, 'desc']],
        columnDefs: [
            { orderable: false, targets: [0] },
            { className: 'text-right', targets: [2,3,4] }
        ]
    });

    $filters.on('submit', function(e){
        e.preventDefault();
        if (validDateRange()) {
            table.ajax.reload();
        }
    });

    $('#btnSellingDesignReset').on('click', function(){
        $filters[0].reset();
        $dateError.text('');
        table.ajax.reload();
    });
});
</script>
{/literal}
