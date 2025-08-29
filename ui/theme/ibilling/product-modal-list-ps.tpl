 

<script>
    $(document).ready(function(){
        $('.datatable').DataTable( {
            "pageLength": 5000,
            "bLengthChange" : false,
            "bInfo" : false,
            "paging": false
        });

        get_list('fabric');
    });


    function get_list(val)
    {
        if(val == 'fabric')
        {
            $('.m_fabric').show();
            $('.m_stone__size').hide();
            $('.m_handwork_materials').hide();
            $('.m_others').hide();
        }
        else if(val == 'stone__size')
        {
            $('.m_fabric').hide();
            $('.m_stone__size').show();
            $('.m_handwork_materials').hide();
            $('.m_others').hide();
        }
        else if(val == 'handwork_materials')
        {
            $('.m_fabric').hide();
            $('.m_stone__size').hide();
            $('.m_handwork_materials').show();
            $('.m_others').hide();
        }
        else if(val == 'others')
        {
            $('.m_fabric').hide();
            $('.m_stone__size').hide();
            $('.m_handwork_materials').hide();
            $('.m_others').show();
        }                
    }    
</script>
<div class="modal-header">
<button type="button" class="close close_buttons_cls" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3>Add Product</h3>
    </div>
<div class="modal-body select_products_body">
<div class="modal_heights">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Readymade">Readymade</a></li>
          <li><a data-toggle="tab" href="#Customized">Customized</a></li>
        </ul>
      
        <div class="tab-content">
          <div id="Readymade" class="tab-pane fade in active">
                <table class="table table1 table-striped datatable" id="items_table1">
                        <thead>
                            <tr>
                                <th width="10%">#</th>
                                <th width="20%">Item Image</th>
                                <th width="20%">Item Name</th>
                                <th width="30%">Description</th>
                                <th width="10%">Price</th>
                                <th width="10%">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $p_readymade as $ds} 
                            <tr>
                                <td>
                                    <input type="checkbox" class="si" value="{$ds['item_number']}">
                                    <input type="hidden" class="pid" value="{$ds['id']}">
                                    <input type="hidden" class="pimg" value="{$ds['product_image']}">
                                </td>
                                <td>
                                    <!--{$ds['item_number']}--> 
                                    {if !empty($ds['product_image']) }
                                    {$thumb = make_thumb($ds['product_image'], 'storage/thumb', '50')}
                                    <img src="{$thumb}" width="50px" height="50px">
                                    {else}
                                    -
                                    {/if}
                                </td>
                                <td>{$ds['name']}</td>
                                <td>{$ds['description']}</td>
                                <td class="price">{$ds['sales_price']}</td>
                                <td>
                                    {$stock = json_decode(product_stock_info($ds['id']), true)}
                                    {$stock['current_stock_count']} {$ds['product_stock_type']} 
                                </td>                               
                            </tr>
                            {/foreach} 
                        </tbody>
                    </table>
          </div>
          <div id="Customized" class="tab-pane fade">

    {assign itemCategory  get_item_categories()}
    {foreach $itemCategory as $cat}
    {assign cat_val  str_replace('&', '', $cat['value'])}
    <div class="radio-button margintop_20">
        <input id="{$cat_val}" type="radio" name="product_category" value="{$cat_val}" onchange="get_list(this.value);" {if $cat_val eq 'fabric'}checked{/if}> 
        <label for="{$cat_val}">{$cat['name']}</label> 
    </div>    
    {/foreach}
<!--<div class="radio-button margintop_20">
				<input id="mfabric" type="radio" name="product_category" value="fabric" onchange="get_list(this.value);" checked> 
				<label for="mfabric">Fabric</label> 
</div>
<div class="radio-button margintop_20">
				<input id="mstone__size" type="radio" name="product_category" value="stone_&_size" onchange="get_list(this.value);"> <label for="mstone__size">Stone Color & Size</label>
</div>-->
                
                <table class="table table1 table-striped datatable" id="items_table2">
                        <thead>
                            <tr>
                                <th width="10%">#</th>
                                <th width="20%">Item Image</th>
                                <th width="20%">Item Name</th>
                                <th width="30%">Description</th>
                                <th width="10%">Price</th>
                                <th width="10%">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $p_customize as $ds} 
                            <tr class="m_{str_replace('&', '', $ds['product_category'])}">
                                <td>
                                    <input type="checkbox" class="si" value="{$ds['item_number']}">
                                    <input type="hidden" class="pid" value="{$ds['id']}">
                                    <input type="hidden" class="pimg" value="{$ds['product_image']}">
                                </td>
                                <td>
                                    <!--{$ds['item_number']}--> 
                                    {if !empty($ds['product_image']) }
                                    {$thumb = make_thumb($ds['product_image'], 'storage/thumb', '50')}
                                    <img src="{$thumb}" width="50px" height="50px">
                                    {else}
                                    -
                                    {/if}
                                </td>
                                <td>{$ds['name']}</td>
                                <td>{$ds['description']}</td>
                                <td class="price">{$ds['sales_price']}</td>
                                <td>
                                    {$stock = json_decode(product_stock_info($ds['id']), true)}
                                    {$stock['current_stock_count']} {$ds['product_stock_type']} 
                                </td>
                            </tr>
                            {/foreach} 
                        </tbody>
                    </table>
          </div>
        </div>    
</div>
</div>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">Close</button>
    <button class="btn btn-primary update">Select</button>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.table1 tr').css('cursor', 'pointer').click(function() {
            var checkBoxes = $(this).find('input:checkbox')
            checkBoxes.prop("checked", !checkBoxes.prop("checked"));
            $(this).toggleClass("checked-p");
        });
    })
</script>
<style>
tr.checked-p {
    color: #000;
    font-weight: 700;
}

input.si {
    pointer-events: none;
}
</style>