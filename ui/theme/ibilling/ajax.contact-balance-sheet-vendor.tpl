
<style>
    #example td.sorting_1 {
    background: inherit !important;
}

td p {
    margin: 0;
}

.btn-data-export {
    position: absolute;
    right: 0;
    top: -33px;
}

.btn-data-export-1 {
    position: absolute;
    right: 70px;
    top: -33px;
}
</style>

{*assign invoice_sum_of_agent invoice_sum_of_vendor($cid)*}
{*assign tr_income_sum_of_agent tr_income_sum_of_vendor($cid)*}

<div id="no-more-tables">
<div id="date-range-training-batch-form">
    <div class="row">
        <div class="col-md-5">
            <span>Date From : </span><input type="date" class="form-control" id="min_bs_date" name="min_bs_date">
        </div>
        <div class="col-md-5">
            <span>Date To : </span><input type="date" class="form-control" id="max_bs_date" name="max_bs_date">
        </div>        
    </div>   
</div>
        <table id="example" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>{$_L['Date']}</th>
                        <th>{$_L['Description']}</th>
                        <th>Amount Credit (Rs)</th>
                        <th>Amount Debit (Rs)</th>    
                        <th>Balance (Rs)</th>
                        <th>{$_L['Manage']}</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <!---->
                    {foreach $creditStock as $ds}
                    <tr style="background: #fcf8e3">
                        <td data-search="{date('dM,Y', strtotime($ds['timestamp']))}" data-order="{$ds['timestamp']}" style="background-color: inherit;">
                            {date( $_c['df'], strtotime($ds['timestamp']))}
                        </td>
                        <td>{$ds['stock']} stock added of product {$ds['item_id']} (per stock price : {$ds['purchase_price']})</td>
                        <td class="text-right dr">{$ds['stock']*$ds['purchase_price']}</td>  
                        <td class="text-right cr">0</td> 
                        <td class="text-right bl">0</td>                                             
                        <td>
                            <a target="_blank" href="-">-</a> <!--{$_url}-->
                        </td>
                    </tr>
                    {/foreach}  
                    
                    <!---->
                    {foreach $debitStock as $ds}
                    <tr style="background: #d9edf7">
                        <td data-search="{date('dM,Y', strtotime($ds['timestamp']))}" data-order="{$ds['timestamp']}" style="background-color: inherit;">
                            {date( $_c['df'], strtotime($ds['timestamp']))}
                        </td>
                        <td>{$ds['stock']} stock removed of product {$ds['item_id']} (per stock price : {$ds['purchase_price']})</td>
                        <td class="text-right dr">0</td>  
                        <td class="text-right cr">{$ds['stock']*$ds['purchase_price']}</td> 
                        <td class="text-right bl">0</td>                                             
                        <td>
                            <a target="_blank" href="-">-</a> <!--{$_url}-->
                        </td>
                    </tr>
                    {/foreach}                    
                    
                    <!---->
                    {foreach $expenseTr as $ds}
                    <tr style="background: #d9edf7">
                        <td data-search="{date('dM,Y', strtotime($ds['date']))}" data-order="{$ds['datetime']}" style="background-color: inherit;">
                            {date( $_c['df'], strtotime($ds['date']))}
                        </td>
                        <td>Desc : {$ds['description']}<br>Category : {$ds['category']}<br>Method : {$ds['method']}</td>
                        <td class="text-right dr">0</td>  
                        <td class="text-right cr">{$ds['amount']}</td> 
                        <td class="text-right bl">0</td>                                             
                        <td>
                            <a target="_blank" href="{$_url}transactions/manage/{$ds['id']}/">{$_L['Manage']}</a>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>  
<hr>



    <div style="margin-bottom: 10px;font-size: 16px;">
        <b>Credit :</b>
        <span style="font-size: 16px;" class="label label-info amount g_credit" data-a-dec="{$_c['dec_point']}" data-a-sep="{$_c['thousands_sep']}" data-a-pad="{$_c['currency_decimal_digits']}" data-p-sign="{$_c['currency_symbol_position']}" data-a-sign="{$_c['currency_code']} " data-d-group="{$_c['thousand_separator_placement']}" data-v-min="-999999999" data-v-max="999999999">{*$invoice_sum_of_agent*}</span>    
    </div>
    
    <div style="margin-bottom: 10px;font-size: 16px;">
        <b>Debit :</b>
        <span style="font-size: 16px;" class="label label-primary amount g_debit" data-a-dec="{$_c['dec_point']}" data-a-sep="{$_c['thousands_sep']}" data-a-pad="{$_c['currency_decimal_digits']}" data-p-sign="{$_c['currency_symbol_position']}" data-a-sign="{$_c['currency_code']} " data-d-group="{$_c['thousand_separator_placement']}" data-v-min="-999999999" data-v-max="999999999">{*$tr_income_sum_of_agent*}</span>    
    </div>

    <div style="margin-bottom: 10px;font-size: 16px;">
        <b>Balance :</b>
        <span style="font-size: 16px;" class="label label-warning amount g_balance" data-a-dec="{$_c['dec_point']}" data-a-sep="{$_c['thousands_sep']}" data-a-pad="{$_c['currency_decimal_digits']}" data-p-sign="{$_c['currency_symbol_position']}" data-a-sign="{$_c['currency_code']} " data-d-group="{$_c['thousand_separator_placement']}" data-v-min="-999999999" data-v-max="999999999">{*$invoice_sum_of_agent - $tr_income_sum_of_agent*}</span>    
    </div>


    </div>


    <script>

$(document).ready(function () {

    $('#example').DataTable({
        paging: false,       
        order: [[0, 'asc']]
    });

    balanceCalc();

    $("#example").dataTable().fnDestroy();

    setTimeout(function(){ reinit_datatable(); }, 3000);
}); 

function reinit_datatable()
{
    $('#example').DataTable({
        paging: false, 
        "columnDefs": [
                { "orderable": true, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { "orderable": false, "targets": 2 },
                { "orderable": false, "targets": 3 },
                { "orderable": false, "targets": 4 },
                { "orderable": false, "targets": 5 },

            ],        
        initComplete : function() {
                $('.dataTables_filter').attr('style', 'display:none;');
                $('.dataTables_length').attr('style', 'display:none;');
                $('.dataTables_paginate').attr('style', 'display:none;');
            },                
        order: [[0, 'asc']],
        dom: 'lBfrtip',
    		buttons: [
    		    {
                    extend: 'csvHtml5',
                    filename: $('.ibox-title h5').text() + ' transactions', 
                    title: $('.ibox-title h5').text() + ' Report', 
                    text: 'Export',
                    className: 'btn-sm btn-secondary btn-data-export',
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4 ],
                        format: {
                            body: function ( data, row, column, node ) {
                                return data.replace(/<br\s*\/?>/ig, "\r\n");
                            }
                        }                        
                    }                    
                },
    		    {
                    extend: 'pdf',
                    filename: $('.ibox-title h5').text() + ' transactions',
                    title: exportTitle('title'), 
                    messageTop: exportTitle('messageTop'),
                    messageBottom: exportTitle('messageBottom'),
                    customize: function(doc) {
                        doc.styles.title = {
                            color: 'black',
                            fontSize: '10',
                            alignment: 'left'
                        },
                        doc.styles.messageTop = {
                            color: 'black',
                            fontSize: '12',
                            alignment: 'center'
                        }                            
                    },                    
                    text: 'Pdf',
                    className: 'btn-sm btn-secondary btn-data-export-1',
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4 ],
                        format: {
                            body: function ( data, row, column, node ) {
                                return data.replace(/<br\s*\/?>/ig, "\r\n");
                            }
                        }                         
                    }                    
                }                
            ]        
    });
}

function balanceCalc()
{
    var append_bal = 0;
    var g_debit = 0;
    var g_credit = 0;    
    $('#example > tbody  > tr').each(function(index, tr) { 
        var debit   = $(tr).find('.dr').html();
        var credit  = $(tr).find('.cr').html();
        var balance = $(tr).find('.bl').html();
        
        if(parseFloat(credit) > 0)
        {
            append_bal += (parseFloat(balance) + parseFloat(debit)) - parseFloat(credit);
        }else{
            append_bal += parseFloat(balance) + parseFloat(debit);
        } 

        $(tr).find('.bl').html(append_bal);

        g_debit   += parseFloat(debit);
        g_credit  += parseFloat(credit);        

    });

    $('.g_debit').html(g_debit);
    $('.g_credit').html(g_credit);
    $('.g_balance').html(append_bal); 

}   
</script>


<script>
    /* Custom filtering function which will search data in column four between two values */
    
    // setup an array of the ids of tables that should be allowed
    var allowFilter = ['example'];

    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            
            // check if current table is part of the allow list
            if ( $.inArray( settings.nTable.getAttribute('id'), allowFilter ) == -1 )
            {
               // if not table should be ignored
               //console.log('other table allowed');
               return true;
            }             
    		
    		var A = new Date($('#min_bs_date').val() + ' 00:00:00');
    		var min = A.getTime();		
    
    		var B = new Date($('#max_bs_date').val() + ' 23:59:59');
    		var max = B.getTime();
    
    		var C = new Date(data[0]);
    		var age = C.getTime();

            console.log(age);
     
            if ( ( isNaN( min ) && isNaN( max ) ) ||
                 ( isNaN( min ) && age <= max ) ||
                 ( min <= age   && isNaN( max ) ) ||
                 ( min <= age   && age <= max ) )
            {
                return true;
            }
            return false;
        }
    );
    
        
    // Event listener to the two range filtering inputs to redraw on input
    $('body').on('change', '#min_bs_date, #max_bs_date', function() {
        $("#example").dataTable().fnDestroy();
        var batch_table = reinit_datatable();
        //batch_table.draw();
        //balanceCalc();
    });
    
    $('body').on('keyup', '#example input', function() {
        //balanceCalc();
    }); 
    
    
function exportTitle(type)
{
    var from = $('#min_bs_date').val();
    var to = $('#max_bs_date').val();
    var name = $('.ibox-title h5').text();

    var company_name = "{$company['account']}";
    var company_addr = "{$company['address']}";
    var company_addr = company_addr.replaceAll('<br>', '\n');    
    if(type == 'messageTop')
    {
        if(from == '' || to == '')
        {
            var data1 = name + ' all reports';
        }
        else
        {
            var data1 = name + ' report from ' + from + ' to ' + to;
        }
    }else if(type == 'title'){
        var data1 = company_name + '\n' + company_addr;
    }else if(type == 'messageBottom')
    {
        var data1 = '\n\nCredit: ' + $('.g_credit').html() + '\nDebit: '+ $('.g_debit').html() +'\nBalance: ' + $('.g_balance').html();
    }

    return data1;
}   
    
</script> 
