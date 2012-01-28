$(function(){
    var tmpl_article_single = _.template($('#tmpl_article_single').html() || '')
    ,friends = SYS.cache('friends');
    $('#body').on('click', '.mail-select', function(){
        var val = ($(this).attr("checked") == 'checked');
        $(".mail-select").attr("checked", val);
        $(".mail-item").attr("checked", val);
    }).on('click', '.refer-del', function(){
        if($(".mail-item:checked").length <= 0){
            DIALOG.alertDialog("请先选择要删除的提醒!");    
            return false;
        }
        DIALOG.confirmDialog("确认要删除这些提醒?",function(){
            $('#refer_form').submit();
        });
    }).on('click', '.refer-clear', function(){
        DIALOG.confirmDialog("确认要删除全部提醒?",function(){
            $('#refer_clear').submit();
        });
    }).on('click', '.refer-read', function(){
        DIALOG.confirmDialog("确认要已读全部提醒?",function(){
            $('#refer_read').submit();
        });
    }).on('click', '.title_3', function(){
        $(this).find('>.m-single').click();
        return false;
    }).on('click', '.m-single', function(){
        var tr = $(this).parent().parent();
        APP.tips(true);
        if(tr.hasClass('no-read'))
            $.post($('#refer_read').attr('action'), {'index':$(this).attr('_index')}, function(){
                tr.removeClass('no-read');
                SESSION.update(true);
            });
        $.getJSON($(this).attr('href'), function(json){
            APP.tips(false);
            if(json.ajax_st == 0)
                DIALOG.ajaxDialog(json);
            else{
                var d = DIALOG.formDialog(tmpl_article_single(json),
                    {title:SYS.code.COM_DETAIL, width:600
                    }
                ),
                validPost = function(){
                    if(!json.allow_post){
                        DIALOG.alertDialog(SESSION.get('is_login')?SYS.code.MSG_NOPERM:SYS.code.MSG_LOGIN);
                        return false;
                    }
                    return true;
                };
                d.on('click.nforum', '.a-post', function(){
                    if(!validPost())
                        return false;
                    d.dialog('close');
                    BODY.open(this);
                    return false;
                }).on('click.nforum', '.a-close', function(){
                    d.dialog('close');
                    BODY.open(this);
                    return false;
                }).on('click.nforum', '.a-single', function(){
                    APP.tips(true);
                    $.getJSON($(this).attr('href'), function(json){
                        APP.tips(false);
                        if(json.ajax_st == 0)
                            DIALOG.ajaxDialog(json);
                        else{
                            DIALOG.updateTop(tmpl_article_single(json));
                        }
                    });
                    return false;
                }).on('click.nforum', '.a-func-forward', function(){
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
                }).on('click.nforum', '.a-func-del', function(){
                    var url = $(this).attr('href');
                    DIALOG.confirmDialog("确认要删除此文章?",function(){
                        $.post(url, function(json){
                            d.dialog('close');
                            DIALOG.ajaxDialog(json);
                        }, 'json');
                    });
                    return false;
                });
            }
        });
        return false;
    });
    $('#refer_form').submit(function(){
        var btn = $(this).find('input[type="submit"]').loading(true);
        $.post($(this).attr('action'), $(this).getPostData(),
            function(json){
                btn.loading(false);
                DIALOG.ajaxDialog(json);
                SESSION.update(true);
            }, 'json');
        return false;
    });
    $('#refer_clear').submit(function(){
        $.post($(this).attr('action'), $(this).getPostData(),
            function(json){
                DIALOG.ajaxDialog(json);
                SESSION.update(true);
            }, 'json');
        return false;
    });
    $('#refer_read').submit(function(){
        $.post($(this).attr('action'), $(this).getPostData(), function(json){
            if(json.ajax_st == 1)
                BODY.refresh();
                SESSION.update(true);
        }, 'json');
        return false;
    });
});
