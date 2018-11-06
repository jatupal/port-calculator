$(function(){

	function calculateTotal()
	{
		var total_qty = 0;
		$(".LineQty").each(function(){
			total_qty += $(this).val()*1;
		});
		//$("#sum_qty").text(total_qty);
		$("#sum_qty").val(total_qty);
		$("#sum_qty").text(total_qty);
	}
	
	
	$(document).on('keyup','.LineQty',function(){
		calculateTotal();
	});
	
	/*var del;
	$("#ResultData").on('click', '#remCF', function(){
		$(this).parent().parent().remove();
		//del = $(this).val();
		//console.log(del);
		calculateTotal();
	});*/
	
	calculateTotal();
})