/**
 * 
 */
$(function(){
	
	$(document).on('keyup','.totalLineQty',function(){
        
        //id_arr = $(this).attr('id');
		//id = id_arr.split("_");
		//element_id = id[id.length-1];
        
		//var qty = $('#quantity_'+element_id).val();
        
        function calculateTotal()
		{
			var total_qty = 0;
			$(".totalLineQty").each(function(){
				total_qty += $(this).val()*1;
			});
			$("#sum_qty").text(total_qty);
			$("#TotalQty").val(total_qty);
		}
        calculateTotal();
    });
	
	
});