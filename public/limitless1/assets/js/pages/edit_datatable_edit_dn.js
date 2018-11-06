/**
 * 
 */
$(function(){
	
	var i = $('table tr').length;
	
	function addNewRow()
	{
		html = '<tr>';
        html += '<td>&nbsp;</td>';
		html += '<td width="11%"><div id="img_'+i+'"></div></td>';
		html += '<td><input type="hidden" name="proId[]" id="proId_'+i+'" /><input type="hidden" name="proItemId[]" id="itemId_'+i+'" /><input type="text" data-type="productCode" name="itemNo[]" id="itemNo_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';
		html += '<td><input type="hidden" name="productSKU[]" id="proSKU_'+i+'" /><input type="text" data-type="productName" name="itemName[]" id="itemName_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';		
		html += '<td><input type="number" name="quantity[]" id="quantity_'+i+'" class="form-control changesNo totalLineQty" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" size="5" style="width: 50px; text-align: center;" disabled="disabled"/></td>';
		html += '<td><a href="javascript:void(0);" id="remCF"><i class="icon-bin"></i></a></td>';
		html += '</tr>';
		$('table').append(html);
		i++;
	}
	
	$(".addmore").on('click',function(){
		addNewRow();
	});

	//deletes the selected table rows
	$("#customFields").on('click', '#remCF', function(){
		$(this).parent().parent().remove();
		calculateTotal();
		$("#message").empty();
		i--;
		if(i == 1){$('#btn_submit').attr('disabled',true);}
	});
	
	$('#quantity_1').keyup(function(){
		
		var val = this.value;
		if(val <= 0){
			$("#quantity_1").css({"border":"2px solid #cc0000", "display":"inline"});
			
			//$('<label id="basic-error" class="validation-error-label" for="basic" style="width: 160px; display: none inline;" >จำนวนมากกว่า 0</label>').insertAfter($(".totalLineQty"));
		}else{
			$("#quantity_1").css({"border":"", "display":""});
		}
		calculateTotal();
		
		if($('#itemNo_1').val().length == 0){
			$("#itemNo_1").css("border","2px solid #cc0000");
			$("#itemName_1").css("border","2px solid #cc0000");
			$('#btn_submit').attr('disabled',true);
		}else{
			$("#itemNo_1").css("border","");
			$("#itemName_1").css("border","");
		}
		
    });
	
	$("#quantity_1").keydown(function(e){
		if(e.which == 13){
			addNewRow();
		}
	});
	
	function calculateTotal()
	{
		var total_qty = 0;
		$(".totalLineQty").each(function(){
			total_qty += $(this).val()*1;
		});
		$("#sum_qty").text(total_qty);
		
		if(total_qty <= 0){
			$('#btn_submit').attr('disabled',true);
		}else{
			$("#btn_submit").removeAttr('disabled');
		}
	}
    
	calculateTotal();
	
});