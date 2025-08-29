$("#rental_service").on("click",function (event) {
	event.preventDefault();
	$service = $(this).children("option:selected").text();
	$service_val = $(this).children("option:selected").val();
	if($service != "Select Services"){
		if($service == "Domain" || $service == "Hosting" || $service == "Domain And Hosting"){
			$("#ren_domain_plan").show();
			$("#ren_service_plan").hide();
		}else{
			$("#ren_domain_plan").hide();
			$("#ren_service_plan").show();
		}
	}
});