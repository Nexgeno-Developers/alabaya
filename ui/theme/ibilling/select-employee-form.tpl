<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Select Employee</h3>
</div>
<div class="modal-body">

{if count($invoice_alocation) > 0}


    <form id="addCategoryForm">
        <input type="hidden" id="invoiceId" name="invoiceId" value="{$invoiceId}">
        <input type="hidden" id="queryupdate" name="queryupdate" value="1">
            
        {foreach $invoice_alocation as $i => $allocation}
            <div class="row category-row row-{$i}" style="display: flex; align-items: center;">
          <input type="hidden" id="id" name="id[]" value="{$allocation['id']}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="categoryName">Category Name</label>
                        <select class="form-control categorySelect" name="categoryId[]" {if $allocation['status'] == 1} readonly disabled{/if} required>
                            <option value="">Select Category</option>
                            {foreach $categoryData as $category}
                                <option value="{$category['id']}" data-price="{$category['price']}" {if $category['id'] == $allocation['category_id']} selected{/if}>{$category['name']}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control quantity" name="quantity[]" {if $allocation['status'] == 1} readonly {/if} value="{$allocation['qty']}" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control price" name="price[]" {if $allocation['status'] == 1} readonly {/if} value="{$allocation['price']}" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="employee">Employee</label>
                        <select class="form-control employeeSelect" name="employeeId[]" {if $allocation['status'] == 1} readonly disabled {/if} required>
                                <option value="{$allocation['employee_id']}">{$allocation['employee_name']}</option>
                        </select>
                    </div>
                </div>

                <!-- Show a checkmark for each row if status in invoice_alocation becomes 1 -->
                {if $allocation['status'] == 1}
                                <span class="checkmark">&#10004; Salary Credited</span>
                {/if}
                
                {if $allocation['status'] != 1}
                <div class="col-md-2 plus-minus-btn">
                    <button type="button" class="btn btn-success btn-add-row">+</button>
                    <button type="button" class="btn btn-danger btn-remove-row">-</button>
                </div>
                {/if}
            </div>
        {/foreach}
    </form>
    
    
{else}


    {if $deliveryStatus == 'completed' || $deliveryStatus == 'delivered'}
    
    <div id="no-inserted-data-form" >Change Delivery Status To [Pending or Processing]</div>
    
    {else}
    
        <form id="addCategoryForm">
            <input type="hidden" id="invoiceId" name="invoiceId" value="{$invoiceId}">
            <input type="hidden" id="queryupdate" name="queryupdate" value="0">
            <div class="row category-row" style="display: flex; align-items: center;">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="categoryName">Category Name</label>
                        <select class="form-control categorySelect" name="categoryId[]" required>
                            <option value="">Select Category</option>
                            {foreach $categoryData as $category}
                                <option value="{$category['id']}" data-price="{$category['price']}">{$category.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control quantity" name="quantity[]" value="1" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control price" name="price[]" value="" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="employee">Employee</label>
                        <select class="form-control employeeSelect" name="employeeId[]" required>
                            <option value="">Select Employee</option>
                            {foreach $employees as $employee}
                                <option value="{$employee['id']}">{$employee.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="col-md-2 plus-minus-btn">
                    <button type="button" class="btn btn-success btn-add-row">+</button>
                    <button type="button" class="btn btn-danger btn-remove-row">-</button>
                </div>
            </div>
        </form>
        
    {/if}
    
    
{/if}

</div>
<div class="modal-footer">
    {if empty($invoice_alocation)}
        {if $deliveryStatus == 'completed' || $deliveryStatus == 'delivered'}
        
            <button id="proceed_employee_salary" type="button" class="btn btn-primary">Proceed Employee Salary</button>
        {else}
        
            <button id="update_selected_employee_status" type="submit" class="btn btn-primary">Save</button>
        {/if}
    {elseif !empty($invoice_alocation)}
        {foreach $invoice_alocation as $allocation}
            {if $allocation['status'] == 0}
                {if $deliveryStatus == 'completed' || $deliveryStatus == 'delivered'}
                
                    <button id="proceed_employee_salary" type="button" class="btn btn-primary">Proceed Employee Salary</button>
                {else}
                
                    <button id="update_selected_employee_status" type="submit" class="btn btn-primary">Save</button>
                {/if}
                {break} <!-- Stop the loop after the first allocation with status 0 is found -->
            {/if}
        {/foreach}
    {/if}
    <button type="button" data-dismiss="modal" class="btn">Close</button>
</div>


<script>
var URL = $("#_url").val();
//console.log(_url);
//var baseUrl = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
//let URL = baseUrl+'/?ng=';
$(document).ready(function() {
    
    
        if ($('#no-inserted-data-form').length > 0) {
            // If the element exists, remove the buttons from the modal footer
            $('.modal-footer').find('button').not('[data-dismiss="modal"]').remove();
        }
   
    
        var deliveryStatus = '{$deliveryStatus}';
        if (deliveryStatus === 'completed' || deliveryStatus === 'delivered') {
            $('#update_selected_employee_status').hide();
            $('input').prop('readonly', true);
            $('select').prop('disabled', true);
            $('.plus-minus-btn').remove();
        }
   

    // Append click event for the new button
    $('#proceed_employee_salary').click(function() {
        // Implement the logic to proceed with employee salary
        alert('Proceeding with employee salary...');
    });


    
    // Change event for category dropdowns
    $('#addCategoryForm').on('change', '.categorySelect', function() {
        var categoryId = $(this).val();
        var categoryPrice = $(this).find('option:selected').data('price');
        // Update UI to display category price
        $(this).closest('.category-row').find('.price').val(categoryPrice);

        var $currentRow = $(this).closest('.category-row'); // Cache the current category row
        // Check if category ID is not empty before making AJAX call
        if (categoryId !== '') {
        // AJAX request to fetch employee options
            $.ajax({
            url: URL + 'invoices/fetch-employee-invoice',
            method: 'get',
            data: { categoryId: categoryId },
            success: function(response) {
                // Debugging: Log the response
                console.log('Response:', response); 
                
                // Set the HTML of .employeeSelect element with the response
                $currentRow.find('.employeeSelect').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching employees:', error);
            }
        });
        } else {
            // If category ID is empty, clear the employee dropdown
            $currentRow.find('.employeeSelect').html('<option value="">Select Employee</option>');
        }
    });
    
   // Add new row
    $('#addCategoryForm').on('click', '.btn-add-row', function() {
        var rowCount = $('.category-row').length; // Get the current number of rows
        var lastRowIndex = rowCount - 1; // Get the index of the last row
        var lastRow = $('.category-row:eq(' + lastRowIndex + ')'); // Get the last row
        
        var newRow = lastRow.clone(); // Clone the last row
        var newIndex = rowCount; // Calculate the index for the new row
        
        // Update the class name of the new row
        newRow.removeClass().addClass('row category-row row-' + newIndex);
        
        newRow.find('input[name="id[]"]').remove();
    
        newRow.find('input[name="quantity[]"]').val('1');
        newRow.find('input[name="price[]"]').val('');
        // Clear the select input and append a new option
    var selectInput = newRow.find('select[name="employeeId[]"]');
    selectInput.empty(); // Remove existing options
    selectInput.append('<option value="">Select Employee</option>');        
        newRow.find('select').prop('selectedIndex', 0);
        
        $('#addCategoryForm').append(newRow); // Append the new row to the form
    });

    // Remove row
    $('#addCategoryForm').on('click', '.btn-remove-row', function() {
        var removedRow = $(this).closest('.category-row'); // Get the removed row
        var removedIndex = removedRow.attr('class').match(/row-(\d+)/)[1]; // Extract the index of the removed row
    
        removedRow.remove(); // Remove the closest row
    
        // Update class names of subsequent rows
        $('.category-row').each(function() {
            var currentIndex = $(this).attr('class').match(/row-(\d+)/)[1]; // Extract the index of the current row
            if (currentIndex > removedIndex) {
                var newIndex = currentIndex - 1; // Decrement index for subsequent rows
                $(this).removeClass().addClass('row category-row row-' + newIndex); // Update class name
            }
        });
    });

});

$(document).ready(function() {
    // Handle form submission
    $('#update_selected_employee_status').click(function() {
        // Prevent form submission if any category row field is empty
        var formIsValid = true; // Flag to track form validation
        
        $('.category-row').each(function() {
            var category = $(this).find('.categorySelect').val();
            var employee = $(this).find('.employeeSelect').val();
            var price = $(this).find('.price').val();
            
            // Check if any field is empty in the current category row
            if (category === '' || employee === '' || price === '') {
                formIsValid = false;
                return false; // Exit the loop if any field is empty
            }
        });
        
        if (!formIsValid) {
            alert('Please fill in all fields in each category row');
            event.preventDefault(); // Prevent form submission
        } else {
            // If all fields are filled, proceed with form submission
            var formData = $('#addCategoryForm').serialize(); // Serialize form data
            
            $.ajax({
                url: URL + 'invoices/employee-invoice-form', // Update the URL as per your application
                method: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Form data sent successfully:', response);
                    $('#ajax-modal').modal('hide');
                    // Reload the page
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error sending form data:', error);
                    // Handle error response, if needed
                }
            });
        }
    });
});

$(document).ready(function() {
    // Handle form submission
    $('#proceed_employee_salary').click(function() {
        // Prevent form submission if any category row field is empty
        var formIsValid = true; // Flag to track form validation
        
        $('.category-row').each(function() {
            var category = $(this).find('.categorySelect').val();
            var employee = $(this).find('.employeeSelect').val();
            var price = $(this).find('.price').val();
            
            // Check if any field is empty in the current category row
            if (category === '' || employee === '' || price === '') {
                formIsValid = false;
                return false; // Exit the loop if any field is empty
            }
        });
        
        if (!formIsValid) {
            alert('Please fill in all fields in each category row');
            event.preventDefault(); // Prevent form submission
        } else {
            // If all fields are filled, proceed with form submission
            var formData = $('#addCategoryForm').serialize(); // Serialize form data
            
            $.ajax({
                url: URL + 'invoices/employee-invoice-form-proceed', // Update the URL as per your application
                method: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Form data sent successfully:', response);
                    $('#ajax-modal').modal('hide');
                    // Reload the page
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error sending form data:', error);
                    // Handle error response, if needed
                }
            });
        }
    });
});




</script>