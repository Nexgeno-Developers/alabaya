<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
   <h3>Edit Category</h3>
</div>
<div class="modal-body">
   <div class="row">
      <div class="col-md-12">
         <div class="">
            <div class="" id="">
               <div class="alert alert-danger" id="emsg" style="display:none;">
                  <span id="emsgbody"></span>
               </div>
               <div class="alert alert-success" id="emsg-success" style="display:none;">
                  <span id="emsgbody-success"></span>
               </div>
               
               <form class="form-horizontal" id="edit-category-form" method="post">
                   <input type="hidden" id="category_id" name="category_id" value="{$category.id}">
                   <div class="row">
                       <div class="col-md-6 col-sm-12">
                           <div class="form-group">
                               <label class="col-md-4 control-label" for="name">Category Name<small class="red">*</small> </label>
                               <div class="col-lg-8">
                                   <input required type="text" id="name" name="name" class="form-control" value="{$category.name}">
                               </div>
                           </div>
                           
                           <div class="form-group">
                               <label class="col-md-4 control-label" for="price">Price</label>
                               <div class="col-lg-8">
                                   <input type="number" id="price" name="price" class="form-control" value="{$category.price}">
                               </div>
                           </div>
                           
                           <div class="form-group">
                               <label class="col-md-4 control-label" for="status">Status</label>
                               <div class="col-lg-8">
                                   <label class="radio-inline">
                                       <input type="radio" name="status" value="1" {if $category.status == 1}checked{/if}> Active
                                   </label>
                                   <label class="radio-inline">
                                       <input type="radio" name="status" value="0" {if $category.status == 0}checked{/if}> Inactive
                                   </label>
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
<div class="modal-footer">
   <button id="update-category" class="btn btn-primary">Update</button>
   <button type="button" data-dismiss="modal" class="btn">Close</button>
</div>

<script>
    // JavaScript function to handle category update
    $(document).ready(function() {
        $('#update-category').on('click', function() {
            var category_id = $('#category_id').val();
            var name = $('#name').val();
            var price = $('#price').val();
            var status = $('input[name="status"]:checked').val();
            
            $.ajax({
                url: '{$_url}contacts/edit-category-post',
                type: 'POST',
                data: {
                    category_id: category_id,
                    name: name,
                    price: price,
                    status: status
                },
                success: function(response) {
                    // Handle success response, such as showing a success message or updating UI
                    alert('Category updated successfully!');
                    $('#ajax-modal').modal('hide'); // Hide the modal after successful update
                    // You can reload the category list or update the UI as needed
                    location.reload(); // Reload the page to reflect the changes
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    alert('An error occurred while updating the category.');
                }
            });
        });
    });
</script>
