$(function(){
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
    }) .attachSingleArticle('.m-single', function(){
        var tr = $(this).parent().parent();
        if(tr.hasClass('no-read'))
            $.post($('#refer_read').attr('action'), {'index':$(this).attr('_index')}, function(){
                tr.removeClass('no-read');
                SESSION.update(true);
            });
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
