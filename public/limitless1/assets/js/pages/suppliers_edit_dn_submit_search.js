$(function(){
	//callback handler for form submit
	$("#FormSearch").submit(function(e)
	{
		var table;
		var DataPost = $('.chkProduct:checked').map(function(){ 
            return this.value; 
        }).get();
		
		//console.log(DataPost);
		$.ajax(
		{
			url : "",
			type: "POST",
			data: {
				'list[]': DataPost,
			},
			success:function(data) 
			{
				//data: return data from server
				$('#modal_backdrop').modal('toggle');
				
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
		e.preventDefault(); //STOP default action
		//e.unbind(); //unbind. to stop multiple form submit.
		
	});
	
	//$("#FormSearch").submit(); //Submit  the FORM
});