/**
 * 
 */
//autocomplete script
$(function(){
			$(document).on('focus','.autocomplete_txt',function(){
				type = $(this).data('type');
				
				if($(this).val().length == 0){
					$("#btn_submit").attr('disabled', true);
				}
				
				$(this).keyup(function(){
					var val = this.value;
					if(val <= 0){
						$("#btn_submit").attr('disabled', true);
					}
				});
				
				if(type =='productCode' )autoTypeNo=0;
				if(type =='productName' )autoTypeNo=1;
				$(this).autocomplete({
					source: function( request, response ){
						$.ajax({
							url : "",
							dataType: "json",
							method: 'post',
							data:{
							   name_startsWith: request.term,
							   type: type
							},
							success: function( data ){
								response( $.map( data, function( item ){
									var code = item.split("|");
									return {
										label: code[autoTypeNo],
										value: code[autoTypeNo],
										data : item
									};
								}));
							}
						});
					},
					autoFocus: true,
					minLength: 0,
					response: function(event, ui) {
						if (!ui.content.length) {
							$("#message").html("<div class='alert alert-danger no-border'><button type='button' class='close' data-dismiss='alert'><span>×</span><span class='sr-only'>Close</span></button><span class='text-semibold'>ไม่พบข้อมูลสินค้า</span></div>");
							$('#btn_submit').attr('disabled',true);
						} else {
							$("#message").empty();
						}
					},
					select: function( event, ui ){
						var names = ui.item.data.split("|");
						id_arr = $(this).attr('id');
						id = id_arr.split("_");
						element_id = id[id.length-1];
						$('#itemNo_'+element_id).val(names[0]);
						$('#itemName_'+element_id).val(names[1]);
						$('#quantity_'+element_id).val(names[2]);
						$('#proId_'+element_id).val(names[3]);
						$('#itemId_'+element_id).val(names[4]);
						var img = $("<img />",{"src":names[5]}).addClass("img_size");
						$('#img_'+element_id).html(img);
						$('#proSKU_'+element_id).val(names[6]);
						calculateTotal();
						
						$("#btn_submit").removeAttr('disabled');
						$("#quantity_"+element_id).css("border","").removeAttr('disabled');
						$("#itemNo_"+element_id).css("border","");
						$("#itemName_"+element_id).css("border","");
					}		      	
				});
				
				function calculateTotal()
				{
					var total_qty = 0;
					$(".totalLineQty").each(function(){
						total_qty += $(this).val()*1;
					});
					$("#sum_qty").text(total_qty);
					//$("#TotalQty").val(total_qty);
				}
			});
});