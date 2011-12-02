var login = ["user/ajax_login"],
home = ['/#!' + SYS.home.substr(1)];
$(function(){
    $('#id').focus();
    $('#f_login').submit(function(){
        var v = $('#s-mode').val();
        if(v == '0'){
            $.post(SYS.base + '/' + SYS.ajax.login, $(this).getPostData(), function(json){
                if(json.ajax_st == 1)
                    window.location.href = SYS.base + '/';
                else
                    DIALOG.ajaxDialog(json);

            }, 'json');
            return false;
        }
        $(this).attr('action', SYS.base + login[v]);
    });
    $('#b_guest').click(function(){
        window.location.href = SYS.base + home[$('#s-mode').val()]
    });
    $('#b_reg').click(function(){
        window.location.href = SYS.base + "/#!reg";
    });
});
