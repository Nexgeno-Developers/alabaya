
$(document).ready(function () {
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

   
});