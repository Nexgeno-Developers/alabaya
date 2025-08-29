{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>{$_L['List']} {if $type eq 'Product'} {$_L['Products']} {else} {$_L['Services']} {/if}</h5>
                <div class="ibox-tools">
                    <a href="{$_url}ps/p-new" class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> {$_L['Add Product']}</a>
                </div>
            </div>
            <div class="ibox-content" id="ibox_form">
                <div class="input-group">
                    <!--<select name="product_type" class="form-control">
                        <option value="readymade" {if $product_type eq 'readymade'} selected {/if}>Readymade</option>
                        <option value="customize" {if $product_type eq 'customize'} selected {/if}>Customize</option>
                    </select>
                    <span class="input-group-btn">
                        <button type="button" id="search1" class="btn btn-sm btn-primary"> {$_L['Search']}</button>
                    </span>-->
                    <div class="radio-button">
					<input type="radio" name="product_type" onchange="psearch(this.value);" value="readymade" id="readymade" {if $product_type eq 'readymade'} checked {/if}>
                    <label for="readymade"> Readymade</label>
					</div>
                    
					<div class="radio-button">
                    <input type="radio" name="product_type" onchange="psearch(this.value);" value="customize" id="customize" {if $product_type eq 'customize'} checked {/if}>
                    <label for="customize"> Customize</label>  
                      </div>					
                </div>
                <input type="hidden" id="stype" value="{$type}">
                <div class="project-list mt-md">
                    <div id="progressbar">
                    </div>

                    <div id="application_ajaxrender1">
                    <table id="" class="display datatable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Purchase Price</th>
                                <th>Sale Price</th>
                                <th>Stock</th>                                    
                                <!--<th>Type</th>-->
                                {if $product_type eq 'customize'}<th>Category</th> {/if}  
                                <th>Image</th>
                                <th>Desc</th>
                                <th>QRCode</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            {$x = 1}
                            {foreach $d as $ds}
                                <td>{$x++}</td>
                                <td>{$ds['name']}</td>
                                <td>{$ds['purchase_price']}</td>
                                <td>{$ds['sales_price']}</td>
                                <td>
                                    {$stock = json_decode(product_stock_info($ds['id']), true)}
                                    {$stock['current_stock_count']} {$ds['product_stock_type']}
                                </td>
                                <!--<td>{$ds['product_type']}</td>-->
                                {if $product_type eq 'customize'}
                                <td>          
                                    {if $ds['product_category'] eq ''}
                                    -
                                    {else}
                                    {str_replace('_', ' ', $ds['product_category'])}
                                    {/if}                                    
                                </td>
                                {/if}
                                <td>
                                    {if $ds['product_image'] eq ''}
                                    -
                                    {else}
                                    {$thumb = make_thumb($ds['product_image'], 'storage/thumb', '50')}
                                    <!--<img data-img="{$ds['product_image']}" src="{$thumb}" width="50px" height="50px" class="img-popup">-->
                                    <a target="_blank" href="{$ds['product_image']}">View</a>
                                    {/if}
                                </td>
                                <td>
                                {if $ds['description'] eq ''}
                                    -
                                {else}
                                    {$ds['description']}
                                {/if}                                     
                                </td>

                                <td>
                                   {assign var='imagetext' value=""|cat:"P-"|cat:$ds['id']}
                                   {assign qrimage qrcode_generate($imagetext)}
                                   <a target="_blank" href="{$_url}qrcode/fetch&search={basename($qrimage)}">view</a>
                                   {* <img src="{$qrimage}" width="100px" height="100px"/> *}
                                </td>

                                <td class="project-actions">
                                    <a href="{$_url}ps/view/{$ds['id']}" class="btn btn-success btn-xs"><i class="fa fa-bar-chart"></i> Stock History</a>
                                   <a href="#" class="btn btn-warning btn-xs cedit_stock" id="e{$ds['id']}"><i class="fa fa-plus"></i> Add Stock </a>
                                    <a href="#" class="btn btn-primary btn-xs cedit" id="e{$ds['id']}"><i class="fa fa-pencil"></i> Edit </a>
                                    <a href="#" class="btn btn-danger btn-xs cdelete" id="pid{$ds['id']}" data-filter="{$product_type}"><i class="fa fa-trash"></i> Delete </a>
                                </td>                                    
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css"> 
    
<script>
    $(document).ready(function(){
        $('.datatable').DataTable();
    });

    function psearch(val){
        //alert(val);
        const url = $('#_url').val();
        const prodyct_type = val; //$('select[name="product_type"]').val();
        window.location.href = url + "ps/p-list/&product_type=" + prodyct_type;
    }
</script>

<input type="hidden" id="_lan_are_you_sure" value="{$_L['are_you_sure']}"> {include file="sections/footer.tpl"}