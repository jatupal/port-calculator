/**
 * 
 */
//autocomplete script
$(function(){
	
	var searchRequest = null;
	var minlength = 0;
	var table;
    $("#sample_search").keyup(function (){
        var that = this,
        value = $(this).val();

        if (value.length >= minlength ){
            if (searchRequest != null) 
                searchRequest.abort();
			searchRequest = $.ajax({
				url: "",
				dataType: "json",
                method: "post",
                data: {
                    'search_keyword' : value
                },
                success: function(data){
					//console.log(data.product_array);
					if (data.product_array.length === 0) {
						
						table = '<div class="col-md-12" ><div class="alert alert-warning no-border">ไม่พบข้อมูลสินค้า</div></div>';
					}else{
						table = "<div class='col-md-12' ><div class='panel panel-flat'><table class='table'>";
						$.each(data.product_array, function(index, value){
							//console.log(value);
							if(value.chkdup =="<span class='label label-warning'>Add already</span>"){
								table += "<tr><td><input class='chkProduct' type='checkbox' name='list[]' value='"+value.product_detail_id+"' disabled='disabled' /></td>";
							}else{
								table += "<tr><td><input class='chkProduct' type='checkbox' name='list[]' value='"+value.product_detail_id+"' /></td>";
							}
							$.each(value, function(i, vale){
								//console.log(i);
								if(i != "product_id" && i != "product_detail_id")
									table += "<td>" + vale + "</td>";
							});
							table += "</tr>";
						});
						table += "</table></div></div>";
					}
					$("#ResultSuppProducts").html(table);
                }
            });
        }
    });
	
	//window.onbeforeunload = function(){
		//return 'Are you sure you want to leave?';
	//};
	
});