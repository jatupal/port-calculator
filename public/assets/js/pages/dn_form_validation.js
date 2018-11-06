$(function(){
	
	$('#MyForm').validate({
        rules: {
            'itemNo[]': {
                required: true
            },
			'itemName[]': {
                required: true
            },
			'quantity[]': {
                required: true
            },
        },
		messages:{
			'itemNo[]': "กรุณาระบุรหัสสินค้า",
			'itemName[]': "กรุณาระบุชื่อสินค้า",
		},
        submitHandler: function (form) { // for demo
            //alert('valid form'); // for demo
            //return false; // for demo
			return true;
        }
    });
	
});