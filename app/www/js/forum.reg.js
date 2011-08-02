function validid(){
    $('#t_id').blur(function(){
        var id = $.trim($(this).val());
        if(id == "")
            return;
        var url = config.base + config.wsapi.u_checkid + "?id=" + id;
        var _id = $(this);
        $.getJSON(url, function(json){
            if(json.st == "success" ){
                var res = "";
                _id.nextAll().remove();
                switch(json.v){
                    case 0:
                        res = '<font color="green">该ID可以注册</font>';
                        break;
                    case 4:
                        res = '<font color="red">该ID已被使用</font>';
                        _id.select();
                        break;
                    default:
                        res = '<font color="red">该ID不符合规范</font>';
                        _id.select();
                }
                _id.after(res);
            }
        });
    });
}

function validpwd(){
    $('#t_pwd1').blur(function(){
        var pwd = $(this).val();
        $(this).nextAll().remove();
        if(pwd == "")
            return;
        if(pwd.length < 4 || pwd.length > 39){
            $(this).after('<font color="red">密码不符合规范</font>');
        }else{
            $(this).after('<font color="green">密码有效</font>');
        }
    });
    $('#t_pwd2').blur(function(){
        var pwd = $(this).val();
        $(this).nextAll().remove();
        if(pwd!= $('#t_pwd1').val()){
            $(this).after('<font color="red">两次密码不一样</font>');
        }else{
            $(this).after('<font color="green">两次密码一样</font>');
        }
    });
}
$(function(){
    validid();
    validpwd();
    $('#authimg').click(function(){this.src = config.base + "/authimg?_id=" + Math.round(Math.random() * 10000);});
    $('#f_reg').submit(function(){
        if($('#t_id').val() == ""){
            alert('ID不能为空');
            $('#t_id').focus();
            return false;
        }else if($('#t_pwd1').val() == ""){
            alert('密码不能为空');
            $('#t_pwd1').focus();
            return false;
        }else if($('#t_pwd1').val() != $('#t_pwd2').val()){
            alert('密码不一致');
            $('#t_pwd2').focus();
            return false;
        }else if($('#t_name').val() == ""){
            alert('昵称不能为空');
            $('#t_name').focus();
            return false;
        }else if($('#t_auth').val() == ""){
            alert('验证码不能为空');
            $('#t_auth').focus();
            return false;
        }else if($('#t_stuno').val() == ""){
            alert('学号不能为空');
            $('#t_stuno').focus();
            return false;
        }else if($('#t_dept').val() == ""){
            alert('学校系级或工作单位不能为空');
            $('#t_dept').focus();
            return false;
        }else if($('#t_tname').val() == ""){
            alert('真实姓名不能为空');
            $('#t_tname').focus();
            return false;
        }else if($('#t_phone').val() == ""){
            alert('联系电话不能为空');
            $('#t_phone').focus();
            return false;
        }else if($('#t_address').val() == ""){
            alert('详细住址不能为空');
            $('#t_address').focus();
            return false;
        }else if(!$('#t_year').val().match(/^[0-9]+$/)){
            alert('出生年份不正确');
            $('#t_year').focus();
            return false;
        }else if(!$('#t_month').val().match(/^[0-9]+$/)){
            alert('出生月份不正确');
            $('#t_month').focus();
            return false;
        }else if(!$('#t_day').val().match(/^[0-9]+$/)){
            alert('出生日不正确');
            $('#t_day').focus();
            return false;
        }else if(!$('#t_email').val().match(/^\w+([-+.]\w+)*@\w+([-.]\w)*\.\w+([-.]\w+)*$/)){
            alert('电子邮件不正确');
            $('#t_email').focus();
            return false;
        }
    });
});
