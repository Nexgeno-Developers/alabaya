{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>List Design</h5>
                <div class="ibox-tools">
                    <a href="{$_url}manage/add-design" class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> Add Design</a>
                </div>
            </div>
            <div class="ibox-content" id="ibox_form">
                <div class="project-list mt-md">
                    <div id="progressbar">
                    </div>

                    <div id="application_ajaxrender1">
                    <table id="" class="display datatable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Silai Price</th>    
                                <th>Image</th>
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
                                <td>{$ds['price']}</td>
                                <td>
                                    {$images = json_decode($ds['image'], true)}
                                    {foreach $images as $img}
                                       {$thumb = make_thumb($img, 'storage/thumb', '50')}
                                        <!--<img data-img="{$img}" src="{$thumb}" width="50px" height="50px" class="img-popup">-->
                                        <a target="_blank" href="{$img}">View</a>
                                    {/foreach}
                                </td>

                                <td>
                                    {assign var='imagetext' value=""|cat:"D-"|cat:$ds['id']}
                                    {assign qrimage qrcode_generate($imagetext)}
                                    <a target="_blank" href="{$_url}qrcode/fetch&search={basename($qrimage)}">View</a>
                                    {*<img src="{$qrimage}" width="100px" height="100px"/>*}
                                </td>

                                <!--<td>
                                {if $ds['description'] eq ''}
                                    -
                                {else}
                                    {$ds['description']}
                                {/if}                                     
                                </td>-->
                                <td class="project-actions">
                                    <a href="{$_url}manage/view/{$ds['id']}" class="btn btn-success btn-xs"><i class="fa fa-bar-chart"></i> History</a>
                                    <a href="#" class="btn btn-primary btn-xs cedit" id="e{$ds['id']}"><i class="fa fa-pencil"></i> Edit </a>
                                    <a href="#" class="btn btn-danger btn-xs cdelete" id="pid{$ds['id']}"><i class="fa fa-trash"></i> Delete </a>
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

    $('#search1').click(function(){
        const url = $('#_url').val();
        const prodyct_type = $('select[name="product_type"]').val();
        window.location.href = url + "ps/p-list/&product_type=" + prodyct_type;
    });
</script>

<input type="hidden" id="_lan_are_you_sure" value="{$_L['are_you_sure']}"> {include file="sections/footer.tpl"}