var login = [SYS.base + '/' + SYS.ajax.login],
home = [SYS.base + '/#!' + SYS.home.substr(1)];
$(function(){
    $('#f_login').submit(function(){
        var v = $('#s-mode').val();
        if(v == '0'){
            $.post(login[v], $(this).getPostData(), function(json){
                if(json.ajax_st == 1)
                    window.location.href = SYS.base + '/';
                else
                    DIALOG.ajaxDialog(json);

            }, 'json');
            return false;
        }
        $(this).attr('action', login[v]);
    });
    $('#b_guest').click(function(){
        window.location.href = home[$('#s-mode').val()]
    });
    $('#b_reg').click(function(){
        window.location.href = SYS.base + "/#!reg";
    });
    $('#id, #pwd').placeholder();
});
