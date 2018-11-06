$(function(){
	
	$("#check1").on("click",function(){
		if($(this).is(":checked")){
			$("input:checkbox").prop('checked', $(this).prop("checked"));
		}else{
			$("input:checkbox").prop('checked', $(this).prop("checked"));
		}
	});
	
	$(".chklist").on("click",function(){
		var numberChecked = $('.chklist:checked').length;
		//console.log(numberChecked);
		if(numberChecked == 0){
			$("#check1").prop('checked', false);
		}else if(numberChecked == 10){
			$("#check1").prop('checked', true);
		}else{
			$("#check1").prop('checked', false);
		}
	});
	
});