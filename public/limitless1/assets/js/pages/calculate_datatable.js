$(function(){
	
	$(document).on('keyup','.LineQty',function(){
        calculateTotal();
		calculate_dif();
    });
	
	function calculate_dif()
	{
		var total_amounts = 0; 
		var total_qty = 0;
		$(".LineQty1").each(function(){
			total_amounts += $(this).val()*1;
		});
		
		$(".LineQty").each(function(){
			total_qty += $(this).val()*1;
		});
		
		if(total_amounts != total_qty){
			$("#btn_submit").attr('disabled',true);
		}else{
			$("#btn_submit").removeAttr('disabled');
		}
	}
	
	function calculateTotal()
	{
		var total_qty = 0;
		$(".LineQty").each(function(){
			total_qty += $(this).val()*1;
		});
		$("#total_receive").text(total_qty);
		$("#TotalQty").val(total_qty);
	}
	
	calculate_dif();
	calculateTotal();
});