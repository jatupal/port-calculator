$(function(){
	//callback handler for form submit
	$("#FormSearch").submit(function(e)
	{
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