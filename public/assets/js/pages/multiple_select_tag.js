/* ------------------------------------------------------------------------------
*
*  # Select2 selects
*
*  Specific JS code additions for form_select2.html page
*
*  Version: 1.1
*  Latest update: Nov 20, 2015
*
* ---------------------------------------------------------------------------- */

$(function() {

    // Tokenization
    $(".select-multiple-tokenization").select2({
        tags: true,
        tokenSeparators: [","]
    });
	
	// Select with search
    $('.select-search').select2();

});
