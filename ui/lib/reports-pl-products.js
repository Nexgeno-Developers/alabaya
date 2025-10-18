var _url = $("#_url").val();
$(function () {
    console.log('reports-pl-products.js loaded');
  var table = $("#plpTable").DataTable({
    searching: false,
    processing: true,
    serverSide: true,
    ajax: {
      url: _url + "reports/pl-products-dt",
      type: "POST",
      data: function (d) {
        return $.extend({}, d, $("#plpFilterForm").serializeObject());
      }
    },
    dom: "Bfrtip",
    buttons: ["pageLength"],
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"]
    ],
    order: [[1, "asc"]], // Branch
    columnDefs: [
      { targets: [3,4,5,6,7], className: "text-right" },
      { targets: [0], orderable: false }
    ]

  });

  $("#plpFilterForm").on("submit", function (e) {
    e.preventDefault();
    if (!validatePLPDates()) return;
    table.ajax.reload();
  });

  $("#plp_reset").on("click", function () {
    $("#plpFilterForm")[0].reset();
    $("#plp_date_error").text('');
    table.ajax.reload();
  });

  table.on("xhr.dt", function (e, settings, json) {
    if (json && json.totals && json.totals.filtered) {
      const t = json.totals.filtered;
      const html = `
        <div class="line"><span class="label">TOTALS</span> →
          Revenue: <b>${t.revenue}</b> |
          COGS: <b>${t.cogs}</b> |
          Profit: <b>${t.profit}</b>
        </div>`;
      $("#totals").html(html);
    } else { $("#totals").html(''); }
  });
  
});

// Helpers
$.fn.serializeObject = function(){
  var o = {};
  var a = this.serializeArray();
  $.each(a, function() {
    if (o[this.name] !== undefined) {
      if (!o[this.name].push) { o[this.name] = [o[this.name]]; }
      o[this.name].push(this.value || '');
    } else {
      o[this.name] = this.value || '';
    }
  });
  return o;
};

function validatePLPDates() {
  const from = document.getElementById("plp_date_from").value;
  const to   = document.getElementById("plp_date_to").value;
  const err  = document.getElementById("plp_date_error");
  err.textContent = "";

  if (!from && !to) return true;
  if ((from && !to) || (!from && to)) { err.textContent = "Please select both dates or leave both empty."; return false; }
  if (new Date(to) < new Date(from))  { err.textContent = "‘To’ cannot be earlier than ‘From’."; return false; }
  return true;
}
