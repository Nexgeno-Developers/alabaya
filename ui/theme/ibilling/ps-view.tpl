{include file="sections/header.tpl"}

{*$stock = json_decode(product_stock_info($id), true)*}
<div class="row">
	<div class="col-lg-12"  id="application_ajaxrender">
        {*
        <div class="ibox float-e-margins">
            <div class="ibox-content">		
                <h1>Product Name : <b>{$p_name}</b></h1>	
                <h3>Current Stock : {$stock['current_stock_count']} {ucfirst($item['product_stock_type'])}</h3>			
            </div>
        </div>
        *}
        <style>
            .ibox {
                border: 1px solid #e7eaec;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                padding: 20px;
                background-color: #fff;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .ibox-content h1 {
                font-size: 28px;
                font-weight: 600;
                color: #2f4050;
                margin-bottom: 10px;
            }

            .ibox-content h1 b {
                color: #1ab394;
            }

            .ibox-content h3 {
                font-size: 18px;
                font-weight: 500;
                color: #676a6c;
                margin-bottom: 15px;
            }

            .ibox-content ul {
                list-style: none;
                padding-left: 0;
            }

            .ibox-content ul li {
                font-size: 16px;
                line-height: 1.6;
                padding: 6px 6px;
                border-bottom: 1px dashed #e7eaec;
            }

            .ibox-content ul li:last-child {
                border-bottom: none;
            }

            .ibox-content ul li span {
                font-weight: bold;
            }

            .stock-negative {
                color: red;
                font-weight: bold;
            }

            .branch-name {
                font-weight: 600;
                color: #1c84c6;
            }
            .transfer-list .transfer-item { padding: 12px 14px; }
            .transfer-main { font-size: 14px; margin-bottom:6px; }
            .transfer-arrow { margin: 0 8px; color:#999; }
            .transfer-qty { background-color:#1ab394; color:#fff; font-size:14px; padding:6px 10px; border-radius:12px; }
            .transfer-meta code { background:#f5f5f5; padding:2px 6px; border-radius:3px; }
            .transfer-date { font-size:12px; }
        </style>

        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <button style="float:right;" type="button" class="btn btn-primary ctransfer_stock" data-itemid="{$id}">
                    <i class="fa fa-exchange"></i> Transfer Stock
                </button>
                <h1>Product Name : <b>{$p_name}</b></h1>
                <h3>Current Stock by Branch :</h3>
                <ul>
                {foreach $branch_stock as $branch_id => $stock_count}
                    <li>
                        {assign var="branch_name" value=get_branch_name($branch_id, alias)}
                        <span class="branch-name">{if $branch_name != ''}{$branch_name}{else}<i>Unknown Branch</i>{/if}</span> : 
                        {if $stock_count < 0}
                            <span class="stock-negative">{$stock_count}</span>
                        {else}
                            <span>{$stock_count}</span>
                        {/if} {ucfirst($item['product_stock_type'])}
                    </li>
                {/foreach}
                </ul>
            </div>
        </div>

        <div class="ibox float-e-margins">
            <div class="ibox-content">			
                <h3>Credited Stocks</h3>
                <table id="creditedTable" class="table table-bordered sys_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Branch</th>
                            <th>Stock</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$i = 1}
                        {assign var="credited_total" value=0}
                        {foreach $credited_stock as $row}
                            {assign var="credited_total" value=$credited_total + $row['stock']}
                            <tr>
                                <td>{$i++}</td>
                                <td>{get_branch_name($row['branch_id'], alias)}</td>
                                <td>{$row['stock']}</td>
                                <td>{date('Y-m-d H:i:s', strtotime($row['timestamp']))}</td>
                            </tr> 
                        {/foreach}
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <th>{$credited_total}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>			
            </div>
        </div>

        <div class="ibox float-e-margins">
            <div class="ibox-content">			
                <h3>Debited Stocks</h3>
                <table id="debitedTable" class="table table-bordered sys_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Branch</th>
                            <th>Stock</th>
                            <th>Ready Product Name</th>
                            <th>Invoice ID</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$i = 1}
                        {assign var="debited_total" value=0}
                        {foreach $debited_stock as $row}
                            {assign var="debited_total" value=$debited_total + $row['stock']}
                            <tr>
                                <td>{$i++}</td>
                                <td>{get_branch_name($row['branch_id'], alias)}</td>
                                <td>{$row['stock']}</td>
                                <td>
                                    {if !empty($row['parent_item_id'])}
                                        {get_type_by_id('sys_items', 'id', $row['parent_item_id'], 'name')}
                                    {else}
                                        -
                                    {/if}
                                </td>
                                <td>
                                    {if !empty($row['invoice_id'])}
                                        {get_type_by_id('sys_invoices', 'id', $row['invoice_id'], 'invoicenum')}
                                    {else}
                                        -
                                    {/if}
                                </td>
                                <td>{date('Y-m-d H:i:s', strtotime($row['timestamp']))}</td>
                            </tr> 
                        {/foreach}
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <th>{$debited_total}</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>			
            </div>
        </div>

    {*<div class="ibox float-e-margins">
        <div class="ibox-content">			
                <h3>Debited Stocks From Invoice</h3>
                <table class="table table-bordered sys_table">
                    <th>#</th>
                    <th>Invoice ID</th>
                    <th>Stock</th>
                    <th>Date</th>
                    {$i = 1}{foreach $sys_invoiceitems as $row}
                    {if $row['invoice_id'] != 0}
                    <tr>
                        <td>{$i++} {$row['id']}</td>
                        <td>{get_type_by_id('sys_invoices', 'id', $row['invoice_id'], 'invoicenum')}</td>
                        <td>{$row['stock']}</td>
                        <td>{get_type_by_id('sys_invoices', 'id', $row['invoice_id'], 'duedate')}</td>
                    </tr> 
                    {/if}
                    {/foreach}
                </table>			
            </div>
        </div> 
        
        
		<div class="ibox float-e-margins">
            <div class="ibox-content">			
                <h3>Debited Stocks From Ready Product</h3>
                <table class="table table-bordered sys_table">
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Stock</th>
                    <th>Date</th>
                    {$i = 1}{foreach $sys_invoiceitems as $row}
                    {if !empty($row['parent_item_id'])}
                    <tr>
                        <td>{$i++} {$row['id']}</td>
                        <td>{get_type_by_id('sys_items', 'id', $row['parent_item_id'], 'name')}</td>
                        <td>{$row['stock']}</td>
                        <td>{$row['timestamp']}</td>
                    </tr> 
                    {/if}
                    {/foreach}
                </table>			
            </div>
        </div>        
	</div>*}


<div class="ibox float-e-margins">
    <div class="ibox-content">
        <h3>Recent Stock Transfers</h3>

        {if !empty($transfer_data)}
        <div class="list-group transfer-list">
            {foreach $transfer_data as $t}
            <div class="list-group-item transfer-item">
                <div class="row">
                    <div class="col-sm-9">
                        <div class="transfer-main">
                            <strong>From:</strong> {$t.from_branch_name|default:'-'}
                            <i class="fa fa-arrow-right transfer-arrow" aria-hidden="true"></i>
                            <strong>To:</strong> {$t.to_branch_name|default:'-'}
                            <span class="transfer-date text-muted small"> &nbsp; — &nbsp; {if $t.date}{$t.date}{/if}</span>
                        </div>
                        <div class="transfer-meta small text-muted">
                            Ref: <code>{$t.ref}</code>
                        </div>
                    </div>
                    <div class="col-sm-3 text-right">
                        <span class="badge transfer-qty">{$t.qty|default:'-'}</span>
                        <button class="btn btn-xs btn-default view-transfer" data-ref="{$t.ref}" style="margin-left:8px;">
                            <i class="fa fa-eye"></i> Details
                        </button>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
        {else}
            <p class="text-muted">No transfers found</p>
        {/if}

    </div>
</div>



<select id="branch_options_template" style="display:none;">
    {foreach $branch_stock as $branch_id => $stock}
        <option value="{$branch_id}" data-available="{$stock}">{get_branch_name($branch_id, alias)} ({$stock})</option>
    {/foreach}
</select>

{include file="sections/footer.tpl"}

<script>
$(document).ready(function() {
    $('#creditedTable, #debitedTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 10,
        order: [],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records..."
        }
    });

    var $modal = $('#ajax-modal');

    $(document).on('click', '.ctransfer_stock', function(e){
        e.preventDefault();
       
        var item_id = $(this).data('itemid');
        $('body').modalmanager('loading');

        // Build branch option sets (from: only branches with stock >=1, to: all branches)
        var branchOptionsFrom = '';
        var branchOptionsTo = '';
        $('#branch_options_template option').each(function(){
            var available = parseFloat($(this).data('available')) || 0;
            var base = '<option value="'+$(this).val()+'" data-available="'+available+'"';
            var label = $(this).text();
            branchOptionsTo += base + '>' + label + '</option>';
            if(available >= 1){
                branchOptionsFrom += base + '>' + label + '</option>';
            }
        });

        setTimeout(function(){
            var formHtml = `
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Transfer Stock</h3>
                </div>
                <div class="modal-body">
                    <form id="edit_form_transfer" class="form-horizontal">
                        <input type="hidden" name="item_id" value="`+item_id+`">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">From Branch</label>
                            <div class="col-sm-8">
                                <select name="from_branch" id="from_branch" class="form-control" required>
                                    <option value="">Select</option>`+branchOptionsFrom+`
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">To Branch</label>
                            <div class="col-sm-8">
                                <select name="to_branch" id="to_branch" class="form-control" required>
                                    <option value="">Select</option>`+branchOptionsTo+`
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Quantity</label>
                            <div class="col-sm-8">
                                <input type="number" name="qty" min="1" class="form-control" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="update_transfer" class="btn btn-success">
                        <i class="fa fa-exchange"></i> Transfer
                    </button>
                </div>
            `;

            $modal.html(formHtml);
            $modal.modal('show');
        }, 500);
    });

    $modal.on('click', '#update_transfer', function(){
        const form = $('#edit_form_transfer')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        // Validate branches
        var from = $('#from_branch').val();
        var to = $('#to_branch').val();
        var fromAvailable = parseFloat($('#from_branch option:selected').data('available')) || 0;
        var qtyVal = parseFloat($('input[name=\"qty\"]').val()) || 0;
        if(from == to){
            alert('From and To Branch cannot be the same!');
            return false;
        }
        if(fromAvailable < 1){
            alert('Selected From branch has no stock available.');
            return false;
        }
        if(qtyVal > fromAvailable){
            alert('Quantity cannot exceed available stock ('+fromAvailable+').');
            return false;
        }

        $modal.modal('loading');
        setTimeout(function(){
            var _url = $("#_url").val();
            var formData = new FormData(form);
            $.ajax({
                url: _url + 'ps/transfer_post/',
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $modal.modal('loading');

                    try {
                        var res = JSON.parse(data);

                        if(res.status == 'success'){
                            $modal.find('.modal-body').prepend('<div class="alert alert-success fade in">' + res.message + '</div>');
                            setTimeout(function(){
                                location.reload(); // reload to show updated stock
                            }, 1500);
                        } else {
                            $modal.find('.modal-body').prepend('<div class="alert alert-danger fade in">' + res.message + '</div>');
                        }
                    } catch(e){
                        alert("Unexpected response: " + data);
                    }
                },
                error: function () {
                    alert("error in ajax form submission");
                }
            });
        }, 500);
    });

    // when clicking "Details" for a transfer
    $(document).on('click', '.view-transfer', function(e){
        e.preventDefault();
        var ref = $(this).data('ref');
        var _url = $('#_url').val();

        // show loading modal
        var $modal = $('#ajax-modal');
        // $modal.html('<div class="modal-dialog"><div class="modal-content"><div class="modal-body">Loading...</div></div></div>');
        $modal.modal('show');

        // fetch entries for this transfer_ref (AJAX call to controller)
        $.ajax({
            url: _url + 'ps/transfer_entries/',
            type: 'GET',
            data: { ref: ref },
            success: function (data) {
                try {
                    var res = JSON.parse(data);
                    if (res.status === 'success') {
                        // build simple HTML for entries
                        var html = '<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>';
                        html += '<h4 class="modal-title">Transfer: ' + ref + '</h4></div>';
                        html += '<div class="modal-body">';
                        html += '<p><strong>From:</strong> ' + (res.from_name || '-') + ' &nbsp; &nbsp; <strong>To:</strong> ' + (res.to_name || '-') + '</p>';
                        html += '<p><strong>Qty:</strong> ' + (res.qty || '-') + '</p>';
                        html += '<hr>';
                        html += '<table class="table table-condensed"><thead><tr><th>Type</th><th>Branch</th><th>Stock</th><th>Date</th></tr></thead><tbody>';
                        res.entries.forEach(function(en){
                            html += '<tr>';
                            html += '<td>' + en.type + '</td>';
                            html += '<td>' + (en.branch_name || en.branch_id) + '</td>';
                            html += '<td>' + en.stock + '</td>';
                            html += '<td>' + en.timestamp + '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table>';
                        html += '</div>';
                        html += '<div class="modal-footer"><button data-dismiss="modal" class="btn btn-default">Close</button></div>';
                        

                        $modal.html(html);
                        $modal.modal('show');
                    } else {
                        $modal.html('<div class="modal-dialog"><div class="modal-content"><div class="modal-body text-danger">'+res.message+'</div></div></div>');
                    }
                } catch (err) {
                    $modal.find('.modal-body').html('Unexpected response: ' + data);
                }
            },
            error: function(){
                $modal.find('.modal-body').html('Error loading details');
            }
        });

    });

});
</script>
