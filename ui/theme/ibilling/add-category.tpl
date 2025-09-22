{include file="sections/header.tpl"}
<div class="wrapper wrapper-content">
    <div class="row">
    
        <div class="col-md-12">
    
            <div class="ibox float-e-margins border-radius-16px">
                <div class="ibox-title">
                    <h5>Add Category</h5>
                </div>
                <div class="ibox-content" id="ibox_form">
    
                    <form class="form-horizontal" id="addcategoryemployee-form" method="post">
    <div class="toaster" id="toaster" style="display: none; position: relative; margin: 10px 0;
                background-color: #51A351; color: #fff; padding: 10px;"></div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group"><label class="col-md-4 control-label" for="name">Category Name<small class="red">*</small> </label>
                        
                                    <div class="col-lg-8"><input required type="text" id="name" name="name" class="form-control" autofocus>
                        
                                    </div>
                                </div>
                        
                                <div class="form-group"><label class="col-md-4 control-label" for="price">Price</label>
                        
                                    <div class="col-lg-8"><input type="number" id="price" name="price" class="form-control">
                        
                                    </div>
                                </div>
                        
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="status">Status</label>
                                    <div class="col-lg-8">
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="1" checked> Active
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="0"> Inactive
                                        </label>
                                    </div>
                                </div>

                        </div>
                        
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-offset-2 col-lg-10">
                        
                                        <button class="md-btn md-btn-primary waves-effect waves-light addcategoryemployee-post" type="submit"><i class="fa fa-check"></i> Save</button> 
                        
                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
    
    
    </div>
</div>

<script>
var baseUrl = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var URL = baseUrl + '/?ng=';

$(document).on('click', '.addcategoryemployee-post', function(e) {
    e.preventDefault();
    
    // Check for empty required fields
    var $form = $("#addcategoryemployee-form");
    var $requiredFields = $form.find('[required]');
    var emptyFields = $requiredFields.filter(function() {
        return $(this).val().trim() === '';
    });
    
    // If there are empty required fields, display alert and focus on the first one
    if (emptyFields.length > 0) {
        alert("Please fill in all the required fields.");
        emptyFields.first().focus();
        return;
    }
    
    var formData = $form.serialize();
    console.log("Serialized Form Data:", formData);
    
    $.ajax({
        url: URL + 'contacts/addcategoryemployee-post/',
        type: 'POST',
        data: formData, // Pass formData here
        success: function(response) {
            console.log("Success: Response:", response);
            $("#toaster").html("Category added successfully.").css('background-color', '#51A351').fadeIn().delay(3000).fadeOut();
            $form[0].reset();
            // For example, you can reload the page after addition
            location.reload();
        },
        error: function(xhr, status, error) {
            console.log("Error Response:", error);
            $("#toaster").html("Error occurred. Please try again.").css('background-color', '#C9302C').fadeIn().delay(3000).fadeOut();
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("AJAX Request Failed:");
        console.error("Status:", textStatus);
        console.error("Error Thrown:", errorThrown);
        console.error("Response Text:", jqXHR.responseText);
        console.error("Status Code:", jqXHR.status);
        console.error("Status Text:", jqXHR.statusText);
        $("#toaster").html("AJAX request failed. Please try again later.").css('background-color', '#C9302C').fadeIn().delay(3000).fadeOut();
    });
});

</script>


{include file="sections/footer.tpl"}
