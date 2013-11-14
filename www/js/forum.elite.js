$(function(){
    var tmpl_preview = _.template($('#tmpl_preview').html()),f;
    $('#body').on('click', '.elite-preview', function(){
        $.getJSON($(this).attr('href').replace("/elite/file", "/elite/ajax_file.json"), function(repo){
            if(repo.ajax_st == 0)
                DIALOG.ajaxDialog(repo);
            else{
                DIALOG.formDialog(tmpl_preview(repo),
                    {title:SYS.code.COM_DETAIL, width:600
                });
                if(typeof sh_init !== 'undefined') sh_init();
            }
        });
        return false;
    });
    if(f = window.BODY.get('path').match(/&f=([^&]*)/)){
        f = encodeURIComponent(f[1]);
        $('#body a[href$="' + f + '"]').click();
        window.ROUTER.navigate(window.BODY.get('path').replace(/&f=([^&]*)/, ''));
    }
});
