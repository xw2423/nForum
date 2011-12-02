$(function(){
    var isPost = false
        ,tmpl_preview = _.template($('#tmpl_preview').html() || '');
    $('#f_tmpl').submit(function(){
        if(isPost){
            DIALOG.alertDialog(SYS.code.MSG_REPEAT);
            return false;
        }
        $.post($(this).attr('action'), $(this).getPostData(), function(repo){
            isPost = (repo.ajax_st == 1);
            DIALOG.ajaxDialog(repo);
        });
        return false;
    });
    $('#body').on('click', '#que_preview', function(){
        var data = $('#f_tmpl').getPostData();
        data.pre = 1;
        $.post($('#f_tmpl').attr('action'), data, function(repo){
            if(repo.ajax_st == 0)
                DIALOG.ajaxDialog(repo);
            else{
                DIALOG.formDialog(tmpl_preview(repo),
                    {title:SYS.code.COM_PREVIEW, width:600,
                     buttons:[
                        {text:SYS.code.COM_SUBMIT,click:function(){
                            $.post($('#f_tmpl').attr('action'), $('#f_tmpl').getPostData(), function(repo){
                                isPost = (repo.ajax_st == 1);
                                DIALOG.ajaxDialog(repo);
                            });
                            $(this).dialog('close');
                        }},
                        {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
                     ]
                }) ;
            }
        });
    });
});
