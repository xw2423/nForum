$(function(){
    $('.f-user-add').submit(function(){
        $.post($(this).attr('action'), $(this).getPostData(),
            function(json){
                SYS.clear('friends');
                DIALOG.ajaxDialog(json);
            },'json');
        return false;
    });
    $('.f-user-delete').submit(function(){
        $.post($(this).attr('action'), $(this).getPostData(),
            function(json){
                SYS.clear('friends');
                DIALOG.ajaxDialog(json);
            }, 'json');
        return false;
    });
    $('#body').on('click', '.user-select', function(){
        var val = ($(this).attr("checked") == 'checked');
        $(".user-select").attr("checked", val);
        $(".user-item").attr("checked", val);
    }).on('click', '.user-del', function(){
        if($(".user-item:checked").length <= 0){
            DIALOG.alertDialog("请先选择要删除的用户!");
            return false;
        }
        DIALOG.confirmDialog("确认要删除这些用户?",function(){
            $('.f-user-delete').submit();
        });
    });
});

