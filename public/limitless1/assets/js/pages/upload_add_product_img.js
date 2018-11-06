$(function(){
    
    var $input = $("#input-705");
    var actions_ = '<div class="file-actions">\n' +
        '    <div class="file-footer-buttons">\n' +
        '        {upload} {delete}' +
        '    </div>\n' +
        '    <div class="file-upload-indicator" title="{indicatorTitle}">{indicator}</div>\n' +
        '    <div class="clearfix"></div>\n' +
        '</div>';
    var footerTemplate = '<div class="file-thumbnail-footer">\n' +
        '    <div class="file-caption-name" style="width:{width}">{caption}</div>\n' +
        '    {progress} {actions}\n' +
        '</div>';
   
    $input.fileinput({
        uploadUrl: "#", // server upload action
        uploadAsync: true,
        showUpload: false, // hide upload button
        showRemove: false, // hide remove button
        minFileCount: 1,
        maxFileCount: 12,
        layoutTemplates: {progress:""}
        //uploadExtraData: function(previewId, index) {
            //return {key: index};
        //},
        //initialPreviewAsData: true, // identify if you are sending preview data only and not the markup
        
    }).on("filebatchselected", function(event, files){
        // trigger upload method immediately after files are selected
        $input.fileinput("upload");
    });
    
});