var login = ["/login"],
home = ["/default"];
$(function(){
    $('#id').focus();
    $('#f_login').submit(function(){
        $(this).attr('action', config.base + login[$('#s-mode').val()]);
    });
    $('#b_guest').click(function(){
        window.location.href = config.base + home[$('#s-mode').val()]
    });
    $('#b_reg').click(function(){
        window.location.href = config.base + "/reg";
    });
});
