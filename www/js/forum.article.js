$(function(){
    $('#quick_post textarea').ubb({enable:false, ubb_img_path:SYS.base + "/img/ubb/", ubb_em:$('#em_img')});

    var validPost = function(){
        if(!user_post){
            if(SESSION.get('is_login'))
                DIALOG.alertDialog(SYS.code.MSG_NOPERM);
            else
                $('#u_login_id').alertDialog(SYS.code.MSG_LOGIN);
            return false;
        }
        return true;
    };
    $('#quick_post').submit(function(){
        if($.trim($('#quick_post textarea').val()) == ''){
            $('#quick_post textarea').alertDialog(SYS.code.MSG_NULL);
            return false;
        }
        if(validPost()){
            $.post($('#quick_post').attr('action'), $('#quick_post').getPostData(), function(json){
                DIALOG.ajaxDialog(json);
            }, 'json');
        }
        return false;
    });
    $('#quick_post textarea').placeholder();
    $('#f_search').submit(function(){
        BODY.open($(this).attr('action') + '?' + _.map($(this).getPostData(),function(v,k){
            v = encodeURIComponent(encodeURIComponent(v));
            return k + '=' + v;
        }).join('&'));
        return false;
    });
    $('#f_search input[placeholder]').placeholder();
    $('#body').on('click','.a-post',validPost)
        .on('click','#a_reply',function(){
            $('#quick_post textarea').focus();
        }).on('click','.a-func-friend',function(){
            $.post($(this).attr('href'), function(json){
                delete json['default'];
                delete json['list'];
                DIALOG.ajaxDialog(json);
            }, 'json');
            return false;
        }).on('keydown','#quick_post textarea',function(event){
            if(event.ctrlKey && event.keyCode == 13){
                $('#quick_post').submit();
                return false;
            }
        }).on('click','.a-func-del',function(){
            var self = this;
            DIALOG.confirmDialog('确认删除此文章?', function(){
                $.post($(self).attr('href'), function(json){
                    DIALOG.ajaxDialog(json);
                }, 'json');
            });
            return false;
        }).on('click','.a-back',function(){
            $('html,body').animate({scrollTop:0}, 500)
        });

    BShare.init($('#a_share').parent(), $('#a_share').attr('_u'), $('#a_share').attr('_c'));

    if($('.map-map').length > 0)
        nForumMap.loadJs('nForumMap.parseMap', null);

    //forward
    $('#body').on('click','.a-func-forward',function(){
        if(!SESSION.get('is_login')){
            $('#u_login_id').alertDialog(SYS.code.MSG_LOGIN);
            return false;
        }
        var d = DIALOG.formDialog(_.template($('#tmpl_forward').html())({action:$(this).attr('href'), friends:[]}), {
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
        SYS.cacheFriends(function(json){
            if(!_.isArray(json)) return;
            d.find('#a_forward_list').append(
                _.reduce(json,function(ret,item){
                    ret += ('<option value="' + item + '">' + item + '</option>');
                    return ret;
                },'')
            );
        });
        return false;
    });
    if(typeof sh_init !== 'undefined') sh_init();
});
