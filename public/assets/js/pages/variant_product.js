$(function() {
	
	$("#product-multiple-options").click(function(){
		$("#product-multiple-options").addClass("hide");
		$("#product-multiple-options-cancel").removeClass("hide");
		$("#product-multiple-options-variant").removeClass("hide");
	});
	
	$("#product-multiple-options-cancel").click(function(){
		$("#product-multiple-options").removeClass("hide");
		$("#product-multiple-options-cancel").addClass("hide");
		$("#product-multiple-options-variant").addClass("hide");
	});
    
    function aaa()
    {
        //var variant_option = [];
        
        var aaa = $('input[name*="option_name"]').length;
        //console.log(aaa);
        
        var bbb = $('select[name*="option_value"]').length;
        //console.log(bbb);
        
        var option_name = [];
        //for(var i=1; i<= aaa; i++){
            //console.log(i);
            $('input[name*="option_name"]').on('change', function(){
                $('input[name*="option_name"]').each(function(i, val){
                    //option_name.push($(this).val());
                    //option_name.push([this.id, $(this).val()]);
                    option_name[i] = $(this).val();
                });
                
                //console.log(option_name);
                
            });
            
            
        //}
        
        var option_value = []; 
        $('select[name*="option_value"]').on('change', function(){
            
            $.each(option_name, function(i,val){ //loop foreach
                console.log(i);
                $('[name="option_value['+i+']"] :selected').each(function(j, selected){ 
                    //option_value[j] = $(selected).text();
                    //variant_option = {val : $(selected).text() };
                    //option_value.push($(selected).text());
                    //option_value.push([j, $(selected).text()]);
                    //option_value[i] = option_value[j];
                    console.log($(selected).text());
                });
                //console.log(option_value);
                
                //variant_option = option_value;
                
                //console.log(option_name);
            });
            //console.log(variant_option);
            
        });
    }
	
	function addMultiSelect(){
		$(".select-multiple-tokenization").select2({
			tags: true,
			tokenSeparators: [","]
		});
    }
	
	var i = $('table tr').length;
    
	function addNewRow()
	{
		html = '<tr>';
		var a = i - 1;
		html += '<td><input type="text" name="option_name['+a+']" class="form-control autoc" id="option_name_'+i+'" placeholder="Color or Size etc."></td>';
		html += '<td><select class="select-multiple-tokenization" multiple="multiple" id="option_value_'+i+'" name="option_value['+a+']" data-placeholder="Black or S,M etc."></select></td>';
		html += '<td><a href="javascript:void(0);" id="remCF"><i class="icon-bin"></i></a></td>';
		
		html += '</tr>';
		$('table').append(html);
		i++;
        if(i >= 3){$("#add-new-option").addClass("hide");}else{$("#add-new-option").removeClass("hide");}
	}
	
	$("#add-new-option").on('click',function(){
		addNewRow();
		addMultiSelect();
        aaa();
        //console.log(i);
        if(i > 2){$("#remCF").removeClass("hide");}
	});

	//deletes the selected table rows
	$("#customFields").on('click', '#remCF', function(){
		$(this).parent().parent().remove();
        i--;
        //console.log(i);
        if(i <= 2){$("#remCF").addClass("hide");}
        if(i >= 3){$("#add-new-option").addClass("hide");}else{$("#add-new-option").removeClass("hide");}
	});
	
	//console.log(i);
    if(i <= 2){$("#remCF").addClass("hide");}
    
    
    aaa();
    
    
});

















