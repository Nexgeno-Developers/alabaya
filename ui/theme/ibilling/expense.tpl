<style>
/*.select2-dropdown {
    height: 250px;
    overflow-x: hidden;
}*/
</style>


{include file="sections/header.tpl"}
<div class="row">
    <div class="col-md-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{$_L['Add Expense']}</h5>

            </div>
            <div class="ibox-content" id="ibox_form">
                <div class="alert alert-danger" id="emsg">
                    <span id="emsgbody"></span>
                </div>
                <form class="form-horizontal" method="post" id="tform" role="form">
                    <div class="form-group">
                        <label for="account" class="col-sm-3 control-label">{$_L['Account']}</label>
                        <div class="col-sm-9">
                            {*<select id="account" name="account" class="form-control">
                                <!--<option value="">{$_L['Choose an Account']}</option>-->
                                {foreach $d as $ds}
                                    <option value="{$ds['account']}">{$ds['account']}</option>
                                {/foreach}
                            </select>*}
                            <select id="account" name="account" class="form-control" required>
                                {if $user->roleid eq 0}
                                    <option value="">{$_L['Choose an Account']}</option>
                                    {foreach $d as $ds}
                                        <option value="{$ds['account']}" data-branch="{$ds['id']}" {if $ds.id eq $user.branch_id}selected{/if}>
                                            {$ds['account']}
                                        </option>
                                    {/foreach}
                                {else}
                                    {foreach $d as $ds}
                                        {if $ds['id'] eq $user->branch_id}
                                            <option value="{$ds['account']}" 
                                                    data-branch="{$ds['id']}" 
                                                    {if $ds.id eq $user.branch_id}selected{/if}>
                                                {$ds['account']}
                                            </option>
                                        {/if}
                                    {/foreach}
                                {/if}
                            </select>

                            <!-- Hidden branch_id field -->
                            <input type="hidden" id="branch_id_hidden" name="branch_id" value="{$user['branch_id']}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date" class="col-sm-3 control-label">{$_L['Date']}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{$mdate}" name="date" id="date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true" required pattern="\d{4}-\d{2}-\d{2}" title="Please enter a valid date in YYYY-MM-DD format">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-3 control-label">{$_L['Description']}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="description" name="description">
                            <div class="help-block"><a data-toggle="modal" href="#modal_add_item"><i class="fa fa-paperclip"></i> {$_L['Attach File']}</a> </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label">{$_L['Amount']}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control amount" id="amount" name="amount" required min="1" step="0.01">
                        </div>
                    </div>






                    <div class="form-group">
                        <div class="col-sm-3">
                            &nbsp;
                        </div>
                        <div class="col-sm-9">
                            <h4 class="a_toggle"><a href="#" id="a_toggle">{$_L['Advanced']}</a> </h4>
                        </div>
                    </div>
                    <div id="a_hide">
                        <div class="form-group">
                            <label for="cats" class="col-sm-3 control-label">{$_L['Category']}</label>
                            <div class="col-sm-9">
                                <select id="cats" name="cats" class="form-control">
                                    <option value="Uncategorized">{$_L['Uncategorized']}</option>
                                    {foreach $cats as $cat}
                                        <option value="{$cat['name']}">{$cat['name']}</option>
                                    {/foreach}


                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tags" class="col-sm-3 control-label">{$_L['Tags']}</label>
                            <div class="col-sm-9">
                                <select name="tags[]" id="tags" class="form-control" multiple="multiple">
                                    {foreach $tags as $tag}
                                        <option value="{$tag['text']}">{$tag['text']}</option>
                                    {/foreach}

                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <!--<label for="payee" class="col-sm-3 control-label">{$_L['Payee']}</label>-->
                            <label for="payee" class="col-sm-3 control-label">Contact</label>
                            <div class="col-sm-9">
                                {*<select id="payee" name="payee" class="form-control">
                                    <option value="">{$_L['Choose Contact']}</option>
                                    {foreach $p as $ps}
                                        <option value="{$ps['id']}">{$ps['account']} / {$ps['phone']}</option>
                                    {/foreach}
                                </select>*}
                                <select id="payee" name="payee" class="form-control">
                                    <option value="">{$_L['Choose Contact']}...</option>
                                    {if isset($employee)}
                                        <option value="{$employee->id}" selected>
                                            {$employee->account} / {$employee->phone}
                                        </option>
                                    {/if}
                                </select>
                            </div>
                        </div>
                        <div class="form-group hide">
                            <label for="invoice_id" class="col-sm-3 control-label">Invoice</label>
                            <div class="col-sm-9">
                                <select id="invoice_id" name="invoice_id" class="form-control">
                                    <option value="">Choose Invoice</option>    
                                </select>
                            </div>
                        </div>

                        <div class="form-group hide">
                            <!--<label for="payee" class="col-sm-3 control-label">{$_L['Payee']}</label>-->
                            <label for="vendor_id" class="col-sm-3 control-label">Vendor</label>
                            <div class="col-sm-9">
                                <select id="vendor_id" name="vendor_id" class="form-control">
                                    <option value="">Choose Vendor</option>
                                    {foreach $v as $ps}
                                        <option value="{$ps['id']}">{$ps['account']}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>                        

                        <div class="form-group">
                            <label for="pmethod" class="col-sm-3 control-label">{$_L['Method']}</label>
                            <div class="col-sm-9">
                                <select id="pmethod" name="pmethod" class="form-control">
                                    <option value="">{$_L['Select Payment Method']}</option>
                                    {foreach $pms as $pm}
                                        <option value="{$pm['name']}">{$pm['name']}</option>
                                    {/foreach}


                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ref" class="col-sm-3 control-label">{$_L['Ref']}#</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="ref" name="ref">
                                <span class="help-block">{$_L['ref_example']}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <input type="hidden" name="attachments" id="attachments" value="">
                            <input type="hidden" name="timesheet_ids" id="timesheet_ids" value="">
                            <button type="submit" id="submit" class="btn btn-primary"><i class="fa fa-check"></i> {$_L['Submit']}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{$_L['Recent Expense']}</h5>

            </div>
            <div class="ibox-content">
   <div class="table-responsive">
                <table class="table table-bordered sys_table">
                    <thead>
                    <tr>
                        <th>{$_L['Description']}</th>
                        <th>{$_L['Amount']}</th>

                    </tr>
                    </thead>
                    <tbody>

                    {foreach $tr as $trs}
                        <tr>
                            <td><a href="{$_url}transactions/manage/{$trs['id']}">
                                    {if $trs['attachments'] neq ''}
                                        <i class="fa fa-paperclip"></i>
                                    {/if}
                                    {$trs['description']}
                                </a> </td>
                            <td class="amount">{$trs['amount']}</td>
                        </tr>
                    {/foreach}

                    </tbody>
                </table>
         </div>
            </div>
        </div>
    </div>

</div>

<input type="hidden" id="_lan_no_results_found" value="{$_L['No results found']}">

<div id="modal_add_item" class="modal fade" tabindex="-1" data-width="600" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">{$_L['Attach File']}</h4>
    </div>
    <div class="modal-body">
        <div class="row">



            <div class="col-md-12">
                <form action="" class="dropzone" id="upload_container">

                    <div class="dz-message">
                        <h3> <i class="fa fa-cloud-upload"></i>  {$_L['Drop File Here']}</h3>
                        <br />
                        <span class="note">{$_L['Click to Upload']}</span>
                    </div>

                </form>


            </div>




        </div>
    </div>
    <div class="modal-footer">

        <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>

    </div>
</div>

{include file="sections/footer.tpl"}
