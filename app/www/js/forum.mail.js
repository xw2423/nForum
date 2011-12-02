$(function(){
    var tmpl_mail_detail = _.template($('#tmpl_mail_detail').html() || '')
    ,friends = SYS.cache('friends');
    $('#body').on('click', '.mail-select', function(){
        var val = ($(this).attr("checked") == 'checked');
        $(".mail-select").attr("checked", val);
        $(".mail-item").attr("checked", val);
    }).on('click', '.mail-del', function(){
        if($(".mail-item:checked").length <= 0){
            DIALOG.alertDialog("请先选择要删除的邮件!");    
            return false;
        }
        DIALOG.confirmDialog("确认要删除这些邮件?",function(){
            $('#mail_form').submit();
        });
    }).on('click', '.mail-clear', function(){
        DIALOG.confirmDialog("确认要删除全部邮件?",function(){
            $('#mail_clear').submit();
        });
    }).on('click', '.title_3', function(){
        $(this).find('>.mail-detail').click();
        return false;
    }).on('click', '.mail-detail', function(){
        var tr = $(this).parent().parent();
        APP.tips(true);
        $.getJSON($(this).attr('href'), function(json){
            APP.tips(false);
            if(json.ajax_st == 0)
                DIALOG.ajaxDialog(json);
            else{
                tr.removeClass('no-read');
                var d = DIALOG.formDialog(tmpl_mail_detail(json),
                    {title:SYS.code.COM_DETAIL, width:600
                    }
                );
                d.on('click.nforum', '.mail-reply', function(){
                    d.dialog('close');
                    BODY.open(this);
                    return false;
                }).on('click.nforum', '.mail-forward', function(){
                    var d = DIALOG.formDialog(_.template($('#tmpl_forward').html())({action:$(this).attr('href'), friends:friends || []}), {
                             buttons:[
                                {text:SYS.code.COM_SUBMIT,click:function(){
                                    var f = $(this).find('#a_forward');
                                    $.post(f.attr('action'), f.getPostData(), function(repo){
                                        DIALOG.ajaxDialog(repo);
                                    });
                                    $(this).dialog('close');
                                }},
                                {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
                             ]
                    }).on('change', 'select', function(){
                        $(this).prev().val($(this).val());
                    });
                    if(friends) return false;
                    $.getJSON(SYS.ajax.friend_list, function(json){
                        if(!_.isArray(json)) return;
                        d.find('#a_forward_list').append(
                            _.reduce(json,function(ret,item){
                                ret += ('<option value="' + item + '">' + item + '</option>');
                                return ret;
                            },'')
                        );
                        SYS.cache('friends', json);
                    });
                    return false;
                }).on('click.nforum', '.mail-delete', function(){
                    var url = $(this).attr('href');
                    DIALOG.confirmDialog("确认要删除此邮件?",function(){
                        $.post(url, function(json){
                            d.dialog('close');
                            DIALOG.ajaxDialog(json);
                        }, 'json');
                    });
                    return false;
                }).on('click.nforum', '.mail-new', function(){
                    d.dialog('close');
                    BODY.open(this);
                    return false;
                });
            }
        });
        return false;
    });
    $('#mail_form').submit(function(){
        var btn = $(this).find('input[type="submit"]').loading(true);
        $.post($(this).attr('action'), $(this).getPostData(),
            function(json){
                btn.loading(false);
                DIALOG.ajaxDialog(json);
            }, 'json');
        return false;
    });
    $('#mail_clear').submit(function(){
        $.post($(this).attr('action'), $(this).getPostData(),
            function(json){
                DIALOG.ajaxDialog(json);
            }, 'json');
        return false;
    });
});
