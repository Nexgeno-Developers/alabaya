{include file="sections/header.tpl"}
<style>
      .fabric-fields, .stone-fields, .handwork-fields, .other-fields
      {
          padding-bottom:5px;
      }
    .m-b-10 {
        margin-bottom: 10px;
    }
</style>
<div class="wrapper wrapper-content">
   <div class="row">
      <div class="col-lg-12">
         <div class="ibox float-e-margins">
            <div class="ibox-title">
               <h5>
                  Add Design
               </h5>
               <div class="ibox-tools">
                  <a href="{$_url}manage/list-design" class="btn btn-primary btn-xs">List Designs</a>
               </div>
            </div>
            <div class="ibox-content" id="ibox_form">
               <div class="alert alert-danger" id="emsg">
                  <span id="emsgbody"></span>
               </div>
               <form class="form-horizontal" id="rform">				  	  
                  <div class="form-group">
                     <label class="col-lg-2 control-label" for="name">Design Name</label>
                     <div class="col-lg-10">
                        <input type="text" id="name" name="name" class="form-control" autocomplete="off">
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-lg-2 control-label" for="sales_price">Silai price</label>
                     <div class="col-lg-10">
                        <input type="text" id="sales_price" name="sales_price" class="form-control amount" autocomplete="off" data-a-sign="{$_c['currency_code']} "
                           data-a-dec="{$_c['dec_point']}" data-a-sep="{$_c['thousands_sep']}" data-d-group="2">
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-lg-2 control-label" for="design_images">Design Images</label>
                     <div class="col-lg-10">
                        <input type="file" id="design_images" name="design_images[]" class="form-control" autocomplete="off" accept="image/*">
                     </div>
                  </div> 
                  <div class="form-group hide">
                     <label class="col-lg-2 control-label" for="description">{$_L['Description']}</label>
                     <div class="col-lg-10">
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                     </div>
                  </div>

                  <div class="form-group">
                              <label class="col-lg-2 control-label" for="description">Product Type</label>
                              <div class="col-lg-10">
                                    <select name="cloth_id" class="form-control select2">
                                          <option value="">--Select Product Type--</option>
                                          {foreach $cloths as $cloth}
                                          <option value="{$cloth['id']}">{$cloth['name']}</option>
                                          {/foreach}
                                    </select>
                              </div>
                           </div>                  

                  <div class="form-group">
                        <label class="col-lg-2 control-label" for="fabrics">Fabric</label>
                        <div class="col-lg-10">
                              <div class="fabric-block">
                              <div class="fabric-fields">
                                    <div class="row">
                                    <div class="col-md-5">
                                          <select name="fabric_id[]" class="form-control select2">
                                          <option value="">--Select Fabric--</option>
                                          {foreach $fabrics as $fabric}
                                          <option value="{$fabric['id']}">{$fabric['name']}</option>
                                          {/foreach}
                                          </select>
                                    </div>
                                    <div class="col-md-5">
                                          <input type="number" class="form-control" name="fabric_qty[]" placeholder="Enter Qty" required>
                                    </div>
                                    <div class="col-md-2 change">
                                          <div class="btn btn-block btn-warning bg_greens" onclick="addFabric();"><i class="fa fa-plus" aria-hidden="true"></i></div>
                                    </div>
                                    </div>
                              </div>
                              </div>
                        </div>
                        </div> 

                        <div class="form-group">
                              <label class="col-lg-2 control-label" for="stone_id">Stone Color & Size</label>
                              <div class="col-lg-10">
                                    <div class="stone-block">
                                          <div class="stone-fields">
                                          <div class="row">
                                                <div class="col-md-5">
                                                      <select name="stone_id[]" class="form-control select2">
                                                      <option value="">--Select Stone Color & Size--</option>
                                                      {foreach $stones as $stone}
                                                      <option value="{$stone['id']}">{$stone['name']}</option>
                                                      {/foreach}
                                                      </select>
                                                </div>
                                                <div class="col-md-5">
                                                      <input type="number" class="form-control" name="stone_qty[]" placeholder="Enter Qty" required>
                                                </div>
                                                <div class="col-md-2 change">
                                                      <div class="btn btn-block btn-warning bg_greens" onclick="addStone();"><i class="fa fa-plus" aria-hidden="true"></i></div>
                                                </div>
                                          </div>
                                          </div>
                                    </div>
                              </div>
                              </div> 
                              
                              
                              <div class="form-group">
                                    <label class="col-lg-2 control-label" for="handwork_id">Handwork Materials</label>
                                    <div class="col-lg-10">
                                        <div class="handwork-block">
                                            <div class="handwork-fields">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <select name="handwork_id[]" class="form-control select2">
                                                            <option value="">--Select Handwork Materials--</option>
                                                            {foreach $handwork_materials as $row}
                                                            <option value="{$row['id']}">{$row['name']}</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="number" class="form-control" name="handwork_qty[]" placeholder="Enter Qty" required>
                                                    </div>
                                                    <div class="col-md-2 change">
                                                        <div class="btn btn-block btn-warning bg_greens" onclick="addHandwork();">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="handwork_id">Others</label>
                                    <div class="col-lg-10">
                                        <div class="other-block">
                                            <div class="other-fields">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <select name="others_id[]" class="form-control select2">
                                                            <option value="">--Select Others--</option>
                                                            {foreach $others as $row}
                                                            <option value="{$row['id']}">{$row['name']}</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="number" class="form-control" name="others_qty[]" placeholder="Enter Qty" required>
                                                    </div>
                                                    <div class="col-md-2 change">
                                                        <div class="btn btn-block btn-warning bg_greens" onclick="addOthers();">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                

				
                				<!--Category Pricing-->
                	            <div class="form-group">
                                    <label class="col-lg-2 control-label" for="category price" >Category Pricing</label>
                                    <div class="col-lg-10">
                                        <div class="category-pricing-block">
                                            {foreach from=$category_employees item=employee}
                                                <div class="category-pricing-fields">
                                                    <div class="row m-b-10">
                                                        <input type="hidden" name="category_id[]" value="{$employee.id}">
                					                    <div class="col-md-6">
                							                <input type="text" class="form-control" value="{$employee.name}" readonly>
                                					    </div>
                                					    <div class="col-md-6">
                                							<input type="number" class="form-control" name="category_price[]" value="" placeholder="Enter Price" required>
                                					    </div>
                			  					    </div>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-sm btn-primary" type="submit" id="submit">{$_L['Submit']}</button>
                        </div>
                    </div>
                </form>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
      $(document).ready(function () {

      $(".progress").hide();

      $("#emsg").hide();

      $("#submit").click(function (event) {
      $('#ibox_form').block({ message: null });
      event.preventDefault(); 
      var formData = new FormData($('#rform')[0]);
      var _url = $("#_url").val();
      $.ajax({
            url: _url + 'manage/add-post/',
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                  setTimeout(function () {
                  var sbutton = $("#submit");
                  var _url = $("#_url").val();
                  if ($.isNumeric(data)) {
                        location.reload();
                  }
                  else {
                        $('#ibox_form').unblock();
                        $("#emsgbody").html(data);
                        $("#emsg").show("slow");
                  }
                  }, 2000);
            },
            error: function () {
                  alert("error in ajax form submission");
            }
      });

      //return false;
      });
      });


function addFabric()
{
    var html = '<div class="fabric-fields"> <div class="row"><div class="col-md-5"><select name="fabric_id[]" class="form-control select2"><option value="">--Select Fabric--</option>{foreach $fabrics as $fabric}<option value="{$fabric["id"]}">{$fabric["name"]}</option>{/foreach}</select></div><div class="col-md-5"> <input type="number" class="form-control" name="fabric_qty[]" placeholder="Enter Qty" required> </div><div class="col-md-2"><div class="btn btn-block btn-danger" onclick="removeFabric(this);"><i class="fa fa-minus" aria-hidden="true"></i></div></div></div></div>';    
    $(".fabric-block").append(html);
    $('.select2').select2();
}

function removeFabric(elem)
{
    $(elem).closest('.fabric-fields').remove();
}    

function addStone()
{
    var html = ' <div class="stone-fields"> <div class="row"> <div class="col-md-5"> <select name="stone_id[]" class="form-control select2"> <option value="">--Select Stone & Size--</option>{foreach $stones as $stone}<option value="{$stone["id"]}">{$stone["name"]}</option>{/foreach}</select> </div><div class="col-md-5"> <input type="number" class="form-control" name="stone_qty[]" placeholder="Enter Qty" required> </div><div class="col-md-2"><div class="btn btn-block btn-danger" onclick="removeStone(this);"><i class="fa fa-minus" aria-hidden="true"></i></div></div></div></div>';    
    $(".stone-block").append(html);
    $('.select2').select2();
}

function removeStone(elem)
{
    $(elem).closest('.stone-fields').remove();
} 

function addHandwork() {
        var html = ' <div class="handwork-fields"> <div class="row"> <div class="col-md-5"> <select name="handwork_id[]" class="form-control select2"> <option value="">--Select Handwork Materials--</option>{foreach $handwork_materials as $row}<option value="{$row["id"]}">{$row["name"]}</option>{/foreach}</select> </div><div class="col-md-5"> <input type="number" class="form-control" name="handwork_qty[]" placeholder="Enter Qty" required> </div><div class="col-md-2"><div class="btn btn-block btn-danger" onclick="removeHandwork(this);"><i class="fa fa-minus" aria-hidden="true"></i></div></div></div></div>';
        $(".handwork-block").append(html);
        $('.select2').select2();
    }

    function removeHandwork(elem) {
        $(elem).closest('.handwork-fields').remove();
    }

    function addOthers() {
        var html = ' <div class="other-fields"> <div class="row"> <div class="col-md-5"> <select name="others_id[]" class="form-control select2"> <option value="">--Select Others--</option>{foreach $others as $row}<option value="{$row["id"]}">{$row["name"]}</option>{/foreach}</select> </div><div class="col-md-5"> <input type="number" class="form-control" name="others_qty[]" placeholder="Enter Qty" required> </div><div class="col-md-2"><div class="btn btn-block btn-danger" onclick="removeOthers(this);"><i class="fa fa-minus" aria-hidden="true"></i></div></div></div></div>';
        $(".other-block").append(html);
        $('.select2').select2();
    }

    function removeOthers(elem) {
        $(elem).closest('.other-fields').remove();
    }     
</script>

{include file="sections/footer.tpl"}

<script>
      $('.select2').select2();
</script>

