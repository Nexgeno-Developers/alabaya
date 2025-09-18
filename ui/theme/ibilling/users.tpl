{include file="sections/header.tpl"}
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{$_L['Manage_Users']}</h5>

            </div>
            <div class="ibox-content">
                <div class="row" style="margin-bottom:15px;">
                    <div class="col-md-8">
                        <form class="form-inline" method="get" action="{$smarty.server.REQUEST_URI}">
                            <input type="hidden" name="ng" value="settings/users" />
                            <div class="form-group">
                                <label for="branch_id" class="sr-only">Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control">
                                    {if $user->roleid eq 0}
                                        <option value="all" {if $branch_id == '' || $branch_id == 'all'}selected{/if}>All Branches</option>
                                    {/if}
                                    {foreach $branches as $branch}
                                        <option value="{$branch.id}" {if $branch_id != '' && $branch_id == $branch.id}selected{/if}>{$branch.account}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="form-group" style="margin-left:10px;">
                                <label for="q" class="sr-only">Search</label>
                                <input type="text" class="form-control" id="q" name="q" placeholder="Search name or email" value="{$q|escape}">
                            </div>
                            <button type="submit" class="btn btn-default" style="margin-left:8px;"><i class="fa fa-search"></i> Search</button>
                            <a href="{$_url}settings/users/" class="btn btn-default" style="margin-left:6px;">Reset</a>
                        </form>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{$_url}settings/users-add" class="btn btn-primary"><i class="fa fa-plus"></i> {$_L['Add_New_User']}</a>
                    </div>
                </div>

                <table class="table table-striped table-bordered table-responsive">
                    <th style="width: 60px;">{$_L['Avatar']}</th>
                    <th>{$_L['Username']}</th>
                    <th>{$_L['Full_Name']}</th>
                    <th>{$_L['Type']}</th>
                    <th>Branch</th>
                    <th>{$_L['Manage']}</th>
                    {foreach $d as $ds}
                        <tr>
                            <td>{if $ds['img'] eq 'gravatar'}
                                    <img src="http://www.gravatar.com/avatar/{($ds['username'])|md5}?s=60" class="img-circle" alt="{$user['fullname']}">
                                {elseif $ds['img'] eq ''}
                                    <img src="{$app_url}ui/lib/imgs/default-user-avatar.png" style="max-height: 60px;" alt="">
                                {else}
                                    <img src="{$ds['img']}" class="img-circle" style="max-height: 60px;" alt="{$ds['fullname']}">
                                {/if}</td>
                            <td>{$ds['username']}</td>
                            <td>{$ds['fullname']}</td>
                            <td>
                                {if $ds['user_type'] == 'Admin'}
                                    Super Admin
                                {else}
                                    {ib_lan_get_line($ds['user_type'])}
                                {/if}
                            </td>
                            <td>
                                {assign var="branch" value=$ds['branch_id']}
                                {if $branch != '' && $branch != '0'}
                                    {foreach $branches as $b}
                                        {if $b.id == $branch}
                                            {$b.account}
                                        {/if}
                                    {/foreach}
                                {else}
                                    -
                                {/if}
                            </td>
                            <td>
                                {if $user->roleid == 0}
                                    {* Super Admin: edit anyone, delete others except self *}
                                    <a href="{$_url}settings/users-edit/{$ds['id']}" class="btn btn-inverse">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    {if $user->id neq $ds['id']}
                                        <a href="{$_url}settings/users-delete/{$ds['id']}" id="{$ds['id']}" class="btn btn-danger cdelete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    {/if}
                                {else}
                                    {* Regular user *}
                                    {if $user->id eq $ds['id']}
                                        {* Can edit self (no delete) *}
                                        <a href="{$_url}settings/users-edit/{$ds['id']}" class="btn btn-inverse">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    {elseif $ds['roleid'] neq 0}
                                        {* Can edit/delete other users, but not super admin *}
                                        <a href="{$_url}settings/users-edit/{$ds['id']}" class="btn btn-inverse">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="{$_url}settings/users-delete/{$ds['id']}" id="{$ds['id']}" class="btn btn-danger cdelete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    {/if}
                                {/if}
                            </td>



                        </tr>
                    {/foreach}


                </table>

            </div>
        </div>



    </div>



</div>

{literal}
<script>
$(document).ready(function(){
$('.cdelete').off('click').on('click', function(e) {
    e.preventDefault();
    var row = $(this).closest('tr'); // get the table row
    var id = $(this).attr('id').replace('iid','');
    var csrf_token = $('#csrf_token').val();

    bootbox.confirm("Are you sure you want to delete this user?", function(result){
        if(result){
            $.post(base_url + "settings/users-delete/" + id, {_token: csrf_token}, function(data){
                if(data.success){
                    row.remove(); // remove row from table
                    toastr.success(data.message);
                } else {
                    toastr.error(data.message);
                }
            }, 'json');
        }
    });
});
});
</script>
{/literal}

{include file="sections/footer.tpl"}
