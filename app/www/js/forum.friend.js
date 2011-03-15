$(function(){
    $('.b-select').click(function(){
        var val = $(this).attr("checked");
        $(".b-select").attr("checked", val);
        $(".b-friend").attr("checked", val);
    });
    $('.b-del').click(function(){
        var enable = false;
        $(".b-friend").each(function(){
            if($(this).attr("checked")){
                enable = true;
                return false;
            }
        });
        if(!enable){
            alert("请先选择要删除的好友!");    
            return false;
        }
        if(confirm("确认要删除这些好友?")){
            $('#friend_form').submit();
        }
    });
});

