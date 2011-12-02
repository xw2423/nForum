$(function(){
    var tmpl_preview = _.template($('#tmpl_preview').html());
    $('#body').on('click', '.elite-preview', function(){
        $.getJSON($(this).attr('href'), function(repo){
            if(repo.ajax_st == 0)
                DIALOG.ajaxDialog(repo);
            else{
                DIALOG.formDialog(tmpl_preview(repo),
                    {title:SYS.code.COM_DETAIL, width:600
                });
            }
        });
        return false;
    });
});
