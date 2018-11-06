/**
 * 
 */
$(function(){
	//deletes table rows
	$(".delData").on("click",function(e){
		
		var varFromPhp = $(this).attr("data");
		//var varFromPhp = this.href;
		console.log(varFromPhp);
		//ajax request....
		
		$.ajax(
		{
			url : "",
			type: "POST",
			data: {
				'del': varFromPhp,
			},
			success:function(data) 
			{
				//data: return data from server
				//$('#modal_backdrop').modal('toggle');
				
				/*if(data.product_array.length === 0){
					table = '<div class="col-md-12" ><div class="alert alert-warning no-border">ไม่พบข้อมูลสินค้า</div></div>';
				}else{
					table = '<table class="table" id="ResultData">';
					table += '<thead>';
					table += '<tr>';
						table += '<th style="width:10%">&nbsp;</th>';
						table += '<th>&nbsp;</th>';
						table += '<th style="width:20%; text-align:right; "><div class="col-md-12">จำนวน</div></th>';
						table += '<th style="width:5%">&nbsp;</th>';
					table += '</tr>';
					table += '</thead>';
					table += '<tbody>';
						$.each(data.product_array, function(index, value){
							console.log(value);
							table += '<tr>';
							
							table += '</tr>';
						});
					table += '</tbody>';
					table += '</table>';
				}*
				
				$("#ResultData").html(table);*/
				window.location.reload();
				//alert("Yes");
			},
			error: function() 
			{
				//if fails
				//alert('error');
			}
		});
		e.preventDefault();
	});
});