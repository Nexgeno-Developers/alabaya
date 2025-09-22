
{include file="sections/header.tpl"}
<div class="row">
    <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{$_L['Add New User']}</h5>

            </div>
            <div class="ibox-content">

                <form role="form" name="accadd" method="post" action="{$_url}settings/users-post">
                    <div class="form-group">
                        <label for="username">{$_L['Username']}</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="fullname">{$_L['Full Name']}</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>
                    {*
                    <div class="form-group">
                        <label for="user_type">{$_L['User']} {$_L['Type']}</label>
                        <select name="user_type" id="user_type" class="form-control">
                            <option value="Admin">{$_L['Full Administrator']}</option>
                            <option value="Employee">{$_L['Employee']}</option>

                        </select>
                        <span class="help-block">{$_L['user_type_help']}</span>

                        <label>{$_L['User']} {$_L['Type']}</label>

                        <div class="i-checks"><label> <input type="radio" value="Admin" name="user_type" checked> <i></i> {$_L['Full Administrator']} </label></div>

                        {foreach $roles as $role}
                            <div class="i-checks"><label> <input type="radio" value="{$role['id']}" name="user_type"> <i></i> {$role['rname']} </label></div>
                        {/foreach}



                    </div>
                    *}

                    <div class="form-group">
                        <label>{$_L['User']} {$_L['Type']}</label>

                        {*
                        {if $user->roleid eq 0}
                            <div class="i-checks">
                                <label>
                                    <input type="radio" value="Admin" name="user_type" required>
                                    <i></i> {$_L['Full Administrator']}
                                </label>
                            </div>
                        {/if}
                        *}

                        {foreach $roles as $role}
                            <div class="i-checks">
                                <label>
                                    <input type="radio" value="{$role['id']}" name="user_type" required>
                                    <i></i> {$role['rname']}
                                </label>
                            </div>
                        {/foreach}
                    </div>

                    <div class="form-group">
                        <label for="branch_id">Branch</label>
                        <select name="branch_id" id="branch_id" class="form-control" required>
                            {if $user->roleid eq 0}
                                <option value="">Select Branch</option>
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


                    <div class="form-group">
                        <label for="password">{$_L['Password']}</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="cpassword">{$_L['Confirm Password']}</label>
                        <input type="password" class="form-control" id="cpassword" name="cpassword" required>
                    </div>


                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {$_L['Submit']}</button>
                    {$_L['Or']} <a href="{$_url}settings/users">{$_L['Cancel']}</a>
                </form>

            </div>
        </div>



    </div>



</div>




{include file="sections/footer.tpl"}
