var _url = $("#_url").val();

$(function () {
    var currentPaymentStatus = ""; // "", "1", "0"

    var table = $("#employeeInvoicesTable").DataTable({
        searching: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: _url + "client/employee_invoices-dt",
            type: "POST",
            data: function (d) {
                d.payment_status = currentPaymentStatus;
                d.search_term = $("#ei_search").val() || "";
                return d;
            }
        },
        dom: "Bfrtip",
        buttons: ["pageLength"],
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        order: [[7, "desc"]], // assign date
        columnDefs: [
            { targets: [0], orderable: false },
            { targets: [2,3,4], className: "text-right" }
        ]
    });

    // radio change
    $("input[name='payment_status']").on("change", function () {
        currentPaymentStatus = $(this).data("val");
        table.ajax.reload();
    });

    // 🔎 search box keyup
    $("#ei_search").on("keyup change", function () {
        table.ajax.reload();
    });

    table.on("xhr.dt", function (e, settings, json) {
        if (json && json.totals) {
            // $("#ei_totals").html("Total Earn Amount: " + json.totals.total_earn);
            $("#ei_footer").html("Total Earn Amount: " + json.totals.total_earn);
        } else {
            // $("#ei_totals").html("");
            $("#ei_footer").html("");
        }
        // ✅ update total count like old view
        if (json && typeof json.total_count !== "undefined") {
            $("#ei_total").text("Total : " + json.total_count);
        }
    });
});
