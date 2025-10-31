var _url = $("#_url").val();

$(function () {
    var table = $("#employeeInvoicesTable").DataTable({
        searching: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: _url + "client/employee_invoices-dt",
            type: "POST",
            data: function (d) {
                d.search_term    = $("#ei_search").val() || "";
                d.payment_status = $("#ei_payment_status").val() || "";
                d.order_status   = $("#ei_order_status").val() || "";
                d.date_type      = $("#ei_date_type").val() || "";
                d.date_from      = $("#ei_date_from").val() || "";
                d.date_to        = $("#ei_date_to").val() || "";
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

    // Apply button
    $("#ei_apply_filters").on("click", function () {
        table.ajax.reload();
    });

    // Enter on search
    $("#ei_search").on("keyup", function (e) {
        if (e.keyCode === 13) {
            table.ajax.reload();
        }
    });

    // Reset
    $("#ei_reset_filters").on("click", function () {
        $("#ei_search").val("");
        $("#ei_payment_status").val("");
        $("#ei_order_status").val("");
        $("#ei_date_type").val("");
        $("#ei_date_from").val("");
        $("#ei_date_to").val("");
        table.ajax.reload();
    });

    // when data comes back, update total + footer
    table.on("xhr.dt", function (e, settings, json) {
        if (json && json.totals) {
            $("#ei_footer").html("Total Earn Amount: " + json.totals.total_earn);
        } else {
            $("#ei_footer").html("");
        }

        if (json && typeof json.total_count !== "undefined") {
            $("#ei_total").text("Total : " + json.total_count);
        }
    });
});
