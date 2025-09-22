{include file="sections/header.tpl"}

<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Category List</h5>
                </div>
                <div class="ibox-content table-responsive">
                    {if $categories}
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Edit</th>
                                    {*<th>Delete</th>*}
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $categories as $category}
                                    <tr>
                                        <td>{$category.name}</td>
                                        <td>{$category.price}</td>
                                        <td>{if $category.status == 1}Active{else}Inactive{/if}</td>
                                        <td>{$category.edit_button}</td>
                                        {*<td>{$category.delete_button}</td>*}
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    {else}
                        <p>No categories found.</p>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function edit_category_modal(id) {
		var _url = $("#_url").val();
		var $modal = $('#ajax-modal');
		$('body').modalmanager('loading');
		$modal.load(_url + 'contacts/edit-category-modal/' + id, '', function() {
			$modal.modal();
		});
    } 
    
      function delete_category(id) {
        var _url = $("#_url").val();
        if (confirm('Are you sure you want to delete this category?')) {
            $.ajax({
                url: _url + 'contacts/delete-category/' + id,
                type: 'POST',
                success: function(response) {
                    // Handle success response, such as refreshing the page or updating UI
                    alert('Category deleted successfully!');
                    // For example, you can reload the page after deletion
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    alert('An error occurred while deleting the category.');
                }
            });
        }
    } 
</script>

{include file="sections/footer.tpl"}
