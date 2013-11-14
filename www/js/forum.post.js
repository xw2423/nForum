$(function(){
    var ubb = false, isPost = false, sub = null
        ,tmpl_preview = _.template($('#tmpl_preview').html() || '');
    $('#body').on('focus', '#post_content', function(){
        if(ubb) return true;
        $(this).ubb({
            ubb_img_path:SYS.base + "/img/ubb/",
            ubb_em:$('#em_img'),
            ubb_syntax_enable:(typeof sh_init !== 'undefined')
        });
        ubb = true;
        $(this).focus();
        window.ROUTER.preventJump(true);
    }).on('click', '.tab-normal', function(){
        var self = $(this);
        if(self.hasClass('tab-down')){
            self.removeClass('tab-down');
            $('#post_subject').val($('#post_subject').val().replace('[' + self.html() + ']', ''));
        }else{
            self.addClass('tab-down');
            $('#post_subject').val('[' + self.html() + ']' + $('#post_subject').val());
        }
    }).on('keydown', '#post_content', function(event){
        if(event.ctrlKey && event.keyCode == 13){
            $('#post_form').submit();
            return false;
        }
    }).on('click', '#post_preview', function(){
        if($('#post_subject').val() == ""){
            $('#post_subject').alertDialog(SYS.code.MSG_SUBJECT);
            return false;
        }
        $.post($('#f_preview').attr('action'), $('#post_content,#post_subject').getPostData(), function(repo){
            if(repo.ajax_st == 0)
                DIALOG.ajaxDialog(repo);
            else{
                DIALOG.formDialog(tmpl_preview(repo), {title:SYS.code.COM_PREVIEW, width:600
                 ,buttons:[
                    {text:SYS.code.COM_SUBMIT,click:function(){
                        $.post($('#post_form').attr('action'), $('#post_form').getPostData(), function(repo){
                            isPost = (repo.ajax_st == 1);
                            DIALOG.ajaxDialog(repo);
                        });
                        $(this).dialog('close');
                    }},
                    {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
                 ]
            });
                if(typeof sh_init !== 'undefined') sh_init();
            }
        });
    });
    $('#post_form').submit(function(){
        if($('#post_id').length > 0 && $('#post_id').val() == ""){
            $('#post_id').alertDialog(SYS.code.MSG_USER);
            return false;
        }
        if($('#post_subject').val() == ""){
            $('#post_subject').alertDialog(SYS.code.MSG_SUBJECT);
            return false;
        }
        if($('#post_subject').val() == sub && isPost){
            DIALOG.alertDialog(SYS.code.MSG_REPEAT);
            return false;
        }
        var btn = $(this).find('input[type="submit"]').loading(true);
        $.post($(this).attr('action'), $(this).getPostData(), function(repo){
            btn.loading(false);
            sub = $('#post_subject').val();
            if(isPost = (repo.ajax_st == 1))
                window.ROUTER.preventJump(false);
            DIALOG.ajaxDialog(repo);
        });
        return false;
    });
});
