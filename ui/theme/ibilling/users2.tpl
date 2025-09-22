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
                        <form id="usersFilterForm" class="form-inline" method="get" action="{$smarty.server.REQUEST_URI}">
                            <input type="hidden" name="ng" value="settings/users" />

                            <div class="form-group">
                                <label for="branch_id" class="sr-only">Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control">
                                    {if $_user->roleid eq 0}
                                        <option value="all" {if $branch_id == '' || $branch_id == 'all'}selected{/if}>All Branches</option>
                                    {/if}
                                    {foreach $branches as $branch}
                                        <option value="{$branch.id}" {if $branch_id != '' && $branch_id == $branch.id}selected{/if}>{$branch.alias|default:$branch.account}</option>
                                    {/foreach}
                                </select>
                            </div>

                            <div class="form-group" style="margin-left:10px;">
                                <label for="q" class="sr-only">Search</label>
                                <input type="text" class="form-control" id="q" name="q" placeholder="Search name or email" value="{$q|escape}">
                            </div>

                            <button type="button" id="btnFilter" class="btn btn-default" style="margin-left:8px;"><i class="fa fa-search"></i> Search</button>
                            <button type="button" id="btnReset" class="btn btn-default" style="margin-left:6px;">Reset</button>
                        </form>
                    </div>

                    <div class="col-md-4 text-right">
                        <a href="{$_url}settings/users-add" class="btn btn-primary"><i class="fa fa-plus"></i> {$_L['Add_New_User']}</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="usersTable" class="table table-striped table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th style="width: 60px;">{$_L['Avatar']}</th>
                                <th>{$_L['Username']}</th>
                                <th>{$_L['Full_Name']}</th>
                                <th>{$_L['Type']}</th>
                                <th>Branch</th>
                                <th>{$_L['Manage']}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {*
                            Rows will be loaded via AJAX by DataTables
                            *}
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>


<script>
var currentBranchId = '{$branch_id}';
var isSuperAdmin = {if $user->roleid eq 0}true{else}false{/if};
$(document).ready(function() {
    var table = $('#usersTable').DataTable({
        searching: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "settings/users-table",
            type: "POST",
            data: function (d) {
                d.branch_id = $('#branch_id').val();
                d.q = $('#q').val();
            }
        },
        dom: 'Bfrtip',
        buttons: [
            //{
            //    extend: 'csv',
            //    text: 'CSV',
            //    exportOptions: {
            //       // Exclude Avatar (0) and Manage (5) columns
            //        columns: [1, 2, 3, 4]
            //    }
            //},
            'pageLength'
        ],
        lengthMenu: [
            [10,25,50,100,-1],
            [10,25,50,100,'All']
        ],
        columns: [
            { data: 'avatar', orderable: false, searchable: false },
            { data: 'username' },
            { data: 'fullname' },
            { data: 'type' },
            { data: 'branch' },
            { data: 'manage', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']]
    });

    // Filter button
    $('#btnFilter').on('click', function(e){
        e.preventDefault();
        table.ajax.reload();
    });

    // Reset
    $('#btnReset').on('click', function(e){
        e.preventDefault();
        $('#q').val('');
        if (isSuperAdmin) {
            $('#branch_id').val('all');
        } else {
            $('#branch_id').val(currentBranchId);
        }
        table.ajax.reload();
    });

    // Trigger search on Enter in search box
    $('#q').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            table.ajax.reload();
        }
    });

    // Delegate delete click (buttons are created via ajax HTML)
    $('#usersTable').on('click', '.cdelete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var csrf_token = $('#csrf_token').val();

        bootbox.confirm("Are you sure you want to delete this user?", function(result){
            if (result) {
                $.post(base_url + "settings/users-ajax-delete/" + id, { _token: csrf_token, id: id }, function(resp) {
                    if (resp && resp.success) {
                        toastr.success(resp.message);
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error((resp && resp.message) ? resp.message : "Error");
                    }
                }, 'json').fail(function() {
                    toastr.error('Request failed');
                });
            }
        });
    });

});
</script>

{include file="sections/footer.tpl"}
