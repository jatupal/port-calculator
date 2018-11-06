$(function(){
	
	$("#process1").on("change",function(){
                var selectVal = $(this).find("option:selected").val();
                var chk = $("input[name='check1']");
                if(selectVal == "all"){
                    $("input:checkbox").prop('checked', true);
                    $("#process1").html("<option value=''>--Select--</option><option value='all' selected='selected'>Select all</option><option value='all_on_page'>Select all on this page</option><option value='deall'>Deselect all</option>");
                    
                }else if(selectVal == "all_on_page"){
                    $("#check1").prop('checked', false);
                    $(".chklist").prop('checked', true);
                    $("#process1").html("<option value=''>--Select--</option><option value='all'>Select all</option><option value='all_on_page' selected='selected'>Select all on this page</option><option value='deall_on_page'>Deselect all on this page</option>");
                    
                }else if(selectVal == "deall_on_page"){
                    $(".chklist").prop('checked', false);
                    $("#process1").html("<option value=''>--Select--</option><option value='all'>Select all</option><option value='all_on_page'>Select all on this page</option>");
                    
                }else if(selectVal == "deall"){
                    $("input:checkbox").prop('checked', false); 
                    $("#process1").html("<option value=''>--Select--</option><option value='all'>Select all</option><option value='all_on_page'>Select all on this page</option>");
                    
                }else{
                    $("input:checkbox").prop('checked', false);
                    $("#process1").html("<option value=''>--Select--</option><option value='all'>Select all</option><option value='all_on_page'>Select all on this page</option>");
                    
                }
            });
            
            $("#check1").on("click",function(){
                if($(this).is(":checked")){
                    $("input:checkbox").prop('checked', $(this).prop("checked"));
                    $("#process1").html("<option value=''>--Select--</option><option value='all' selected='selected'>Select all</option><option value='all_on_page'>Select all on this page</option><option value='deall'>Deselect all</option>");
                }else{
                    $("input:checkbox").prop('checked', $(this).prop("checked"));
                    $("#process1").html("<option value=''>--Select--</option><option value='all' >Select all</option><option value='all_on_page'>Select all on this page</option>");
                }
            });
            
            $(".chklist").on("click",function(){
                var numberChecked = $('.chklist:checked').length;
				console.log(numberChecked);
                if(numberChecked == 0){
                    $("#check1").prop('checked', false);
                    $("#process1").html("<option value=''>--Select--</option><option value='all'>Select all</option><option value='all_on_page'>Select all on this page</option>");
                }else if(numberChecked == 10){
                    $("#check1").prop('checked', false);
                    $("#process1").html("<option value=''>--Select--</option><option value='all'>Select all</option><option value='all_on_page' selected='selected'>Select all on this page</option><option value='deall_on_page'>Deselect all on this page</option>");
                }else{
                    $("#check1").prop('checked', false);
                    $("#process1").html("<option value=''>--Select--</option><option value='all'>Select all</option><option value='all_on_page'>Select all on this page</option>");
                }
            });
	
});