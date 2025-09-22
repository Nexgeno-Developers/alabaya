{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default border-radius-16px">
            <div class="panel-body border-radius-16px" style="padding-bottom: 0;">
                <form id="contacts-filters" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="filter_branch">Branch</label>
                            <select id="filter_branch" class="form-control">
                                <option value="all">All Branches</option>
                                {foreach $branches as $b}
                                    <option value="{$b.id}" {if $user.branch_id eq $b.id}selected{/if}>{$b.alias}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_group">{$_L['Group']}</label>
                            <select id="filter_group" class="form-control">
                                <option value="all">All Groups</option>
                                {foreach $all_groups as $group}
                                    <option value="{$group.id}">{$group.gname}</option>
                                {/foreach}
                                <option value="0">Other</option>
                            </select>
                        </div>
                         <div class="col-md-4" style="align-self: flex-end; display: flex; justify-content: flex-end;">
                            <div class="col-md-3">
                                <label style="visibility:hidden">---</label>
                                <button id="btn_filter" class="btn btn-primary btn-block"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>			
                            <div class="col-md-3">
                                <label style="visibility:hidden">---</label>
                                <button id="btn_reset" class="btn btn-danger btn-block"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                            </div>                        
                            <div class="col-md-6">
                                {*<label style="visibility:hidden">---</label>
                                <a href="{$_url}contacts/add/" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> {$_L['Add New Contact']}</a>
                            *}</div>                      
                        </div>                        
                    </div>

                </form>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table id="contact-datatable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="display:none;">ID</th>
                                <th>Branch</th>
                                <th>{$_L['Name']}</th>
                                <th>{$_L['Phone']}</th>
                                <th>{$_L['Group']}</th>
                                <th data-orderable="false">{$_L['Manage']}</th>
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

<script>
    $(document).ready(function(){
        // Vanilla JS to get query param
        function getQueryParam(Param) {
            var params = new URLSearchParams(window.location.search);
            return params.get(Param) || '';
        }

        var initialSearch = getQueryParam('search');

        var navbarInput = document.getElementById('navbar-search-input');
        if(navbarInput && initialSearch) {
            navbarInput.value = initialSearch;
        }

        console.log(initialSearch);

        var table = $('#contact-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            search: { search: initialSearch },
            ajax: {
                url: '{$_url}contacts/list/',
                type: 'GET',
                data: function (d) {
                    // only pass group & branch as extra params; DataTables sends global search automatically
                    d.group = $('#filter_group').val();
                    d.branch = $('#filter_branch').val();
                }
            },
            dom: 'lBfrtip',
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ],
            pageLength: 25,
            columns: [
                {
                    data: null, // Serial column
                    render: function (data, type, row, meta) {
                        var order = table.order();
                        // check if ID column is sorted
                        var idSort = order.find(o => o[0] === 0); // ID column is 0 in returned data now
                        var serial;
                        if (idSort && idSort[1] === 'asc') {
                            serial = meta.row + 1 + meta.settings._iDisplayStart;
                        } else {
                            serial = meta.settings._iRecordsDisplay - (meta.row + meta.settings._iDisplayStart);
                        }
                        return serial;
                    }
                },
                { data: 0, visible: false }, // ID
                { data: 1 }, // Branch
                { data: 2 }, // Name
                { data: 3 }, // Phone
                { data: 4 }, // Group
                { data: 5, orderable: false, searchable: false } // Manage
            ],
            order: [[0, 'desc']],
            dom: 'lBfrtip',
            buttons: [
                //{
                //    extend: 'csv',
                //    text: 'Export',
                //    exportOptions: { columns: [0,1,2,3,4] }
                //}
            ],
            language: { processing: "<i class='fa fa-spinner fa-spin'></i> Loading..." }
        });


        // Reload table when filters change
        $('#btn_filter').on('click', function(){ table.ajax.reload(); });
        //$('#filter_group, #filter_branch').on('change', function(){ table.ajax.reload(); });

        // Reset button logic
        $('#btn_reset').on('click', function() {
            $('#filter_group').val('all');
            $('#filter_branch').val('all');
            table.ajax.reload();
        });
        
        // AJAX Delete with Confirm
        $('#contact-datatable').on('click', '.cdelete', function(e) {
            e.preventDefault();
            var id = $(this).attr('id').replace('uid','');

            bootbox.confirm("Are you sure you want to delete this contact?", function(result){
                if(result){
                    $.ajax({
                        url: base_url + "delete/crm-user/" + id + "/",
                        type: "POST",
                        dataType: "json",
                        success: function(data){
                            if(data.success){
                                $('#contact-datatable').DataTable().ajax.reload(null,false);
                                toastr.success(data.message);
                            } else {
                                toastr.error(data.message);
                            }
                        },
                        error: function(){
                            toastr.error("Something went wrong, please try again.");
                        }
                    });
                }
            });
        });

    });
</script>
