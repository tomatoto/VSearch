function cacheVideo(){
		$(this).text("caching... ");	
		$.ajax({
			type:"POST",
			url:"./cache.php",
			data:$(this).attr("value"),
			dataType:"html",
			success:
				function(response){
					//alert("response:");
					//alert("response:"+response);
					$(this).toggleClass("cached");
					$(this).removeClass("to-cached");
					$(this).text("cached");
				}

		});

		$(this).toggleClass("cached");
		$(this).removeClass("to-cached");
}
