
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
        buttons: ["csv", "pageLength"],
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
        table.ajax.reload();
    });

    // Reset filters
    $("#resetFilters").on("click", function () {
        $("#filterForm")[0].reset();
        table.ajax.reload();
    });

    // Update totals after draw
    table.on("xhr.dt", function (e, settings, json) {
        if (json && json.totals) {
            $("#totals").html(
                "Income: " + json.totals.income +
                " | Expense: " + json.totals.expense +
                " | Balance: " + json.totals.balance
            );
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
