
/*$(document).ready(function () {
		$(".sys_table").DataTable({
				 dom: "Bfrtip",
				 lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ "10 rows", "25 rows", "50 rows", "Show all" ]
        ],
        buttons: [
            "print",
						"pageLength"
        ]
			});
				  $(".buttons-print").removeClass("btn btn-default");
				  $(".buttons-print").addClass("btn btn-primary");
				  $(".buttons-page-length").removeClass("btn btn-default");
				  $(".buttons-page-length").addClass("btn btn-primary");
					$(".dataTables_filter").addClass("pull-right");

   
});*/


$(function () {
    var table = $("#transactionTable").DataTable({
        searching: false, // we use custom filters instead
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "transactions/list-datatable",
            type: "POST",
            data: function (d) {
                return $.extend({}, d, $("#filterForm").serializeObject());
            }
        },
        dom: "Bfrtip",
        buttons: ["pageLength"],
        // buttons: ["csv", "pageLength"],
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        order: [[0, "desc"]],
        columnDefs: [
            { targets: [7], orderable: false }
        ]
    });

    // On filter submit, reload table
    $("#filterForm").on("submit", function (e) {
        e.preventDefault();
        // alert("Filter form submitted");
        validateDates();
        table.ajax.reload();
    });

    // Reset filters
    $("#resetFilters").on("click", function () {
        $("#filterForm")[0].reset();
        table.ajax.reload();
    });
/*
    // Update totals after draw
    table.on("xhr.dt", function (e, settings, json) {
        if (json && json.totals) {
            let res = json.totals; // ✅ assign here

            $('#totals').html(
                'Income: ' + res.filtered.income +
                ' | Expense: ' + res.filtered.expense +
                ' | Balance: ' + res.filtered.balance
                // 'Filtered → Income: ' + res.filtered.income +
                // ' | Expense: ' + res.filtered.expense +
                // ' | Balance: ' + res.filtered.balance +
                // '<br>' +
                // 'Page → Income: ' + res.page.income +
                // ' | Expense: ' + res.page.expense +
                // ' | Balance: ' + res.page.balance
            );
        }
    });
*/
    // Update totals after draw
    table.on("xhr.dt", function (e, settings, json) {
        if (json && json.totals && json.totals.filtered) {
            const res = json.totals.filtered;
            const by = res.by_method || { income:{}, expense:{} };

            const html = `
                <div class="line"><span class="label">Overall</span> → 
                    Income: <b>${res.income}</b> |
                    Expense: <b>${res.expense}</b> |
                    Balance: <b>${res.balance}</b>
                </div>

                <div class="group">
                    <div class="line"><span class="label">Income by Method</span> → 
                        Cash: <b>${by.income.cash || '0.00'}</b> |
                        QR: <b>${by.income.qr || '0.00'}</b> |
                        Other: <b>${by.income.other || '0.00'}</b>
                    </div>
                    <div class="line"><span class="label">Expense by Method</span> → 
                        Cash: <b>${by.expense.cash || '0.00'}</b> |
                        QR: <b>${by.expense.qr || '0.00'}</b> |
                        Other: <b>${by.expense.other || '0.00'}</b>
                    </div>
                </div>
            `;
            $('#totals').html(html);
        } else {
            $('#totals').html('');
        }
    });

});

// Serialize form to object
$.fn.serializeObject = function(){
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function validateDates() {
    // alert("validateDates called");
    const from = document.getElementById("date_from").value;
    const to = document.getElementById("date_to").value;

    // Create or reuse error div
    let errorDiv = document.getElementById("dateError");
    if (!errorDiv) {
        errorDiv = document.createElement("div");
        errorDiv.id = "dateError";
        errorDiv.style.color = "red";
        errorDiv.style.marginTop = "5px";
        document.querySelector("#filterForm").appendChild(errorDiv);
    }

    errorDiv.textContent = ""; // Clear previous error

    // ✅ Case 1: both empty → valid
    if (!from && !to) {
        return true;
    }

    // ✅ Case 2: one filled, one empty → invalid
    if ((from && !to) || (!from && to)) {
        errorDiv.textContent = "Please select both dates or leave both empty.";
        return false;
    }

    // ✅ Case 3: both filled but 'to' < 'from' → invalid
    if (new Date(to) < new Date(from)) {
        errorDiv.textContent = "‘Date To’ cannot be earlier than ‘Date From’.";
        return false;
    }

    // ✅ Case 4: valid
    return true;
}
