$(function(){
    $('.b-select').click(function(){
        var val = $(this).attr("checked");
        $(".b-select").attr("checked", val);
        $(".b-mail").attr("checked", val);
    });
    $('.b-del').click(function(){
        var enable = false;
        $(".b-mail").each(function(){
            if($(this).attr("checked")){
                enable = true;
                return false;
            }
        });
        if(!enable){
            alert("请先选择要删除的邮件!");    
            return false;
        }
        if(confirm("确认要删除这些邮件?")){
            $('#mail_form').submit();
        }
    });
    $('.b-clear').click(function(){
        if(confirm("确认要删除全部邮件?")){
            $('#mail_clear').submit();
        }
    });
    $('#a_content').keydown(function(event){
        if(event.ctrlKey && event.keyCode == 13){
            $('#f_mail').submit();
            return false;
        }
    });
    $('#f_mail').submit(function(){
        if($.trim($('#id').val()) ==  ""){
            alert("请填写收件人");
            return false;
        }
    });
});
