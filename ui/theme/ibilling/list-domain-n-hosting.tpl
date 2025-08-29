{include file="sections/header.tpl"}

    <div class="row">

        <div class="col-md-12">

            <div class="panel panel-default">

                <div class="panel-body">

                    <!-- <form class="form-horizontal" method="post" action="{$_url}customers/list/">

                        <div class="form-group">

                            <div class="col-md-8">

                                <div class="input-group">

                                    <div class="input-group-addon">

                                        <span class="fa fa-search"></span>

                                    </div>

                                    <input type="text" name="name" class="form-control" placeholder="{$_L['Search by Name']}..."/>

                                    <div class="input-group-btn">

                                        <button class="btn btn-primary">{$_L['Search']}</button>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-4">



                                <a href="{$_url}domain_n_hosting/add/" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Add New Domain and Hosting</a>



                            </div>

                        </div>

                    </form> -->
										<div class="col-md-8">
											<form class="form-horizontal" method="post" action="{$_url}customers/list/">
												<div class="form-group">
													<div class="col-md-12">
														<div class="input-group">
																<div class="input-group-addon">
																		<span class="fa fa-search"></span>
																</div>
																<input type="text" name="name" id="foo_filter" class="form-control" placeholder="{$_L['Search']}..."/>

														</div>
													</div>
												</div>
											</form>
										</div>
										<div class="col-md-4">
											<a href="{$_url}domain_n_hosting/add/" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Add New Domain and Hosting</a>
										</div>
                </div>
            </div>

        </div>

    </div>


    <div class="row" id="application_ajaxrender">

        {if ($_c['contact_set_view_mode']) eq 'tbl'}

            <div class="col-md-12">

                <div class="panel panel-default">

                    <div class="panel-body">
<div class="table-responsive"> 
                        <table class="table table-bordered table-hover sys_table footable" data-filter="#foo_filter" data-page-size="50">

                            <thead>

                             <tr>

                                <th>#</th>

                                <th>{$_L['Company Name']}</th>

                                <th >Domain Name</th>
                                <th>Service</th>
                                <th>Plan & Package</th>
                                <th>Amount</th>
                                <th>Expiry Date</th>

                                <th class="text-right" data-sort-ignore="true">{$_L['Manage']}</th>

                            </tr>

                            </thead>

                            <tbody>

                            {foreach $d as $ds}
   <tr>

                                    <td><a href="{$_url}domain_n_hosting/edit/{$ds['id']}">{$ds['id']}</a> </td>

                                    <td><a href="{$_url}domain_n_hosting/edit/{$ds['id']}">{get_type_by_id('crm_accounts', 'id', $ds['account'], 'account')}</a> </td>

                                    <td>{$ds['domain_name']}</td>
                                    <td>{get_type_by_id('sys_items', 'id', $ds['service'], 'name')}</td>

                                    <td>
																		{if $ds['service'] == 1 || $ds['service'] == 2 || $ds['service'] == 3}
																		
																			{$ds['d_h_plan_yearly']}
																			
																			{if $ds['d_h_plan_yearly'] == 1}
																				Year
																			{else}
																				Years
																			{/if}
																			
																		{else}
																		
																			{$ds['service_plan_monthly']}
																			
																			{if $ds['service_plan_monthly'] == 12}
																				Year
																			{else}
																				{if $ds['service_plan_monthly'] == 1}
																					Month
																				{else}
																					Months
																				{/if}
																			{/if}
																			
																		{/if}
																		</td>
																		
                                    <td>{$ds['amount']}</td>

                                    <td>{$ds['expiry_date']}</td>

                                    <td class="text-right">
																		 <a href="{$_url}domain_n_hosting/generate-proforma/{$ds['id']}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Generate Proforma</a>
																		 <a href="{$_url}domain_n_hosting/edit/{$ds['id']}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> {$_L['Edit']}</a>
                                     <p href="" class="btn btn-warning btn-xs" onclick="sendReminder({$ds['id']})"><i class="fa fa-bell"></i> Send</p>
                                     <p href="" class="btn btn-info btn-xs" onclick="renewal({$ds['id']})"><i class="fa fa-repeat"></i> Renew</p>
                                     <a href="delete/domain-n-hosting/{$ds['id']}/" class="btn btn-danger btn-xs cdelete_domain" id="uid{$ds['id']}"><i class="fa fa-trash"></i> {$_L['Delete']}</a>

                                    </td>

                                </tr>

                            {/foreach}

                            </tbody>

                        </table>
</div>
                    </div>

                </div>

            </div>

        {/if}

    </div>

    <div class="row">

			<div class="col-md-12">

				{$paginator['contents']}

			</div>

    </div>
		
		<!-- <div id="myModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Modal Header</h4>
					</div>
					<div class="modal-body">
						<p>Some text in the modal.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div> -->
<script>

  function sendReminder(id){
      var _url = $("#_url").val();
      var $modal = $('#ajax-modal');
      console.log('Click');
      console.log(id);
      $('body').modalmanager('loading');
      $modal.load( _url + 'domain_n_hosting/send_reminder_/' + id, '', function(){
          $modal.modal();
          $('.sysedit').summernote({
          });
      });
  }

  function renewal(id){
      var _url = $("#_url").val();
      var $modal = $('#ajax-modal');
      console.log('Click');
      console.log(id);
      $('body').modalmanager('loading');
      $modal.load( _url + 'domain_n_hosting/renewal/' + id, '', function(){
          $modal.modal();
          $('.sysedit').summernote({
          });
      });
  }

  $(document).ready(function () {
    var _url = $("#_url").val();
    var $modal = $('#ajax-modal');
    var sysrender = $('#application_ajaxrender');

    $modal.on('click', '#send', function(){
          $modal.modal('loading');
          /* var attach_pdf = 'No';
          if($("#attach_pdf").prop('checked') == true){
              attach_pdf = 'Yes';
          } */
          var _url = $("#_url").val();
          $.post(_url + 'domain_n_hosting/send_email', {
              message: $('.sysedit').code(),
              subject: $('#subject').val(),
              toname: $('#toname').val(),
              toemail: $('#toemail').val(),
              ccemail: $('#ccemail').val(),
              bccemail: $('#bccemail').val()
          }).done(function (data) {
              var _url = $("#_url").val();
              $modal.modal('loading').find('.modal-body').prepend(data);
          });
      });

    });
</script>

{include file="sections/footer.tpl"}