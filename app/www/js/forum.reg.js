$(function(){
    var validid = function(){
        $('#t_id').blur(function(){
            var self = $(this), url, id = $.trim(self.val());
            self.nextAll().remove();
            if(id == "")
                return;
            url = SYS.base + '/' + SYS.ajax.valid_id + "?id=" + id;
            $.getJSON(url, function(json){
                if(json.ajax_st == 1 ){
                    var res = "";
                    switch(json.status){
                        case 0:
                            res = '<font color="green">该ID可以注册</font>';
                            break;
                        case 4:
                            res = '<font color="red">该ID已被使用</font>';
                            self.select();
                            break;
                        default:
                            res = '<font color="red">该ID不符合规范</font>';
                            self.select();
                    }
                    self.after(res);
                }
            });
        });
    };
    var validpwd = function(){
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
    };
    validid();
    validpwd();
    $('#authimg').click(function(){this.src = $(this).attr('_src') + "?_t=" + $.random()}).click();
    $('#f_reg').submit(function(){
        if($('#t_id').val() == ""){
            $('#t_id').alertDialog('ID不能为空');
            return false;
        }else if($('#t_pwd1').val() == ""){
            $('#t_pwd1').alertDialog('密码不能为空');
            return false;
        }else if($('#t_pwd1').val() != $('#t_pwd2').val()){
            $('#t_pwd2').alertDialog('密码不一致');
            return false;
        }else if($('#t_name').val() == ""){
            $('#t_name').alertDialog('昵称不能为空');
            return false;
        }else if($('#t_auth').val() == ""){
            $('#t_auth').alertDialog('验证码不能为空');
            return false;
        }else if($('#t_stuno').val() == ""){
            $('#t_stuno').alertDialog('学号不能为空');
            return false;
        }else if($('#t_dept').val() == ""){
            $('#t_dept').alertDialog('学校系级或工作单位不能为空');
            return false;
        }else if($('#t_tname').val() == ""){
            $('#t_tname').alertDialog('真实姓名不能为空');
            return false;
        }else if($('#t_phone').val() == ""){
            $('#t_phone').alertDialog('联系电话不能为空');
            return false;
        }else if($('#t_address').val() == ""){
            $('#t_address').alertDialog('详细住址不能为空');
            return false;
        }else if(!$('#t_year').val().match(/^[0-9]+$/)){
            $('#t_year').alertDialog('出生年份不正确');
            return false;
        }else if(!$('#t_month').val().match(/^[0-9]+$/)){
            $('#t_month').alertDialog('出生月份不正确');
            return false;
        }else if(!$('#t_day').val().match(/^[0-9]+$/)){
            $('#t_day').alertDialog('出生日不正确');
            return false;
        }else if(!$('#t_email').val().match(/^\w+([-+.]\w+)*@\w+([-.]\w)*\.\w+([-.]\w+)*$/)){
            $('#t_email').alertDialog('电子邮件不正确');
            return false;
        }
        $.post($(this).attr('action'), $(this).getPostData(), function(json){
            if(json.ajax_st == 0){
                $('#t_auth').val('');
                $('#authimg').click();
            }
            DIALOG.ajaxDialog(json)
        }, 'json');
        return false;
    });
});
