//$(document).ready(function () {
var _url = $("#_url").val();

		$(function() {
		 
			function cb(start, end) {
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			}
			//cb(moment(), moment());
			cb(moment().startOf('month'), moment().endOf('month'));
		
			$('#reportrange').daterangepicker({
				ranges: {
				   'Today': [moment(), moment().subtract(-1, 'days')],
				   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(0, 'days')],
				   'Last 7 Days': [moment().subtract(6, 'days'), moment().subtract(-1, 'days')],
				   'Last 30 Days': [moment().subtract(29, 'days'), moment().subtract(-1, 'days')],
				   'This Month': [moment().startOf('month'), moment().endOf('month')],
				   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				},
			},
			function(start, end, label) {
				$('.loader').addClass('active');
				

				
				$.ajax
				({
					type: "POST",
					url: _url + 'reports/gst-reports-ajax', 
					data: {company:$('[name="filter"]').val(), start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD')},
					cache: false,
					success: function(html){ $(".ret_bal").html(html);
					  
					 $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
					$('.loader').removeClass('active'); } 
					
				});		
			},
			 cb);
		
		});
		  
		$("#csv").on('click', function (event) {
			exportThisWithParameter('projectSpreadsheet', 'gst-report');
    });	
	
		var exportThisWithParameter = (function () {
				var uri = 'data:application/vnd.ms-excel;base64,',
						template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"  xmlns="http://www.w3.org/TR/REC-html40"><head> <xml><x:ExcelWorkbook><x:ExcelWorksheets> <x:ExcelWorksheet><x:Name>{worksheet}</x:Name> <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions> </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook> </xml></head><body> <table>{table}</table></body></html>',
						base64 = function (s) {
								return window.btoa(unescape(encodeURIComponent(s)))
						},
						format = function (s, c) {
								return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
						}
				return function (tableID, excelName) {
						tableID = document.getElementById(tableID)
						var ctx = { worksheet: excelName || 'Worksheet', table: tableID.innerHTML }
						window.location.href = uri + base64(format(template, ctx))
				}
		});
			$("#send_pdf").on('click', function() {
				var pdf = $( ".check_invoice:checked" ).each(function() {
											return $(this).val();
										}); 
				console.log(pdf);						
				$.ajax({
					type: "POST",
					url: _url + 'reports/send_pdf',
					data: pdf,
					cache: false,
					success: function(html){  } 
					
				});		
    });	
		
		 $("#checkAll").click(function () {
     $('input:checkbox').not(this).prop('checked', this.checked);
 });

$(document).ready(function () { 

	setTimeout(function(){  
		$('#reportrange').click();
		$('.ranges ul li').removeAttr('class'); 
	}, 100);

	setTimeout(function(){  
		$('.ranges ul li:nth-child(5)').click();
	}, 150);

	$("#filter").select2({
		theme: "bootstrap",
		language: {
			noResults: function () {
				return $("#_lan_no_results_found").val();
			}
		}
	});	

	$('#filter').on('change', function(){
		$('#reportrange').click();
	});
		
});

