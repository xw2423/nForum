$(function(){
    $('.board-list tbody tr:odd td').addClass('bg-odd');
    $('#board_search').submit(function(){
        BODY.open($(this).attr('action') + '?' + _.map($(this).getPostData(),function(v,k){
            v = encodeURIComponent(encodeURIComponent(v));
            return k + '=' + v;
        }).join('&'));
        return false;
    });

    var validPost = function(){
        if(!user_post){
            if(SESSION.get('is_login'))
                DIALOG.alertDialog(SYS.code.MSG_NOPERM);
            else
                $('#u_login_id').alertDialog(SYS.code.MSG_LOGIN);
            return false;
        }
    };
    $('#board_search input[placeholder]').placeholder();
    $('#body').on('click','.b-post',validPost)
        .on('click', '#b_fav', function(){
            var self = this;
            DIALOG.confirmDialog('确认收藏此版面?', function(){
                var url = SYS.base + "/fav/op/0.json"
                ,data = {ac:'ab', v:$(self).attr('_b')};
                $.post(url, data, function(json){
                    if(json.ajax_st == 1)
                        APP.renderTree();
                    DIALOG.ajaxDialog(json);
                }, "json");
            });
       });

    if ($('#deny_list').length > 0) {
        var b = $('#add_deny').attr('_b') ,reason = SYS.cache(b + '_denyreasons');

        if(reason){
            $('#deny_list select').append(
                _.reduce(reason,function(ret,item){
                    ret += ('<option value="' + item.desc + '">' + item.desc + '</option>');
                    return ret;
                },'')
            );
        }else{
            $.get(SYS.base + '/board/' + b + '/ajax_denyreasons.json', function(res){
                $('#deny_list select').append(
                    _.reduce(res,function(ret,item){
                        ret += ('<option value="' + item.desc + '">' + item.desc + '</option>');
                        return ret;
                    },'')
                );
                SYS.cache(b + '_denyreasons', res);
            });
        }

        $('#deny_list select').on('change', function(){
            $(this).prev().val($(this).val()).change();
        });

        $('#add_deny').click(function(){
            var tr = $(this).parents('tr:first');
            var data = {
                id: tr.find('.c_userid input').val()
                ,reason: tr.find('.c_reason input').val()
                ,day: tr.find('.c_desc input').val()
            };
            if (data.id == '' || data.reason == '' || data.day == '') {
                DIALOG.alertDialog('请输入封禁信息!');
                return false;
            }
            DIALOG.confirmDialog('确认封禁 ' + data.id + ' ' + data.day + ' 天?', function(){
                $.post(SYS.base + '/board/' + b + '/ajax_adddeny.json', data, function(res){
                    DIALOG.ajaxDialog(res);
                }, 'json');
            });
            return false;
        });
        $('.mod_deny').click(function(){
            var tr = $(this).parents('tr:first');
            var t = new Date();
            var now = parseInt(t.getTime() / 1000);
            var offset = t.getTimezoneOffset() * 60;
            var free = tr.find('.c_desc span').text();
            t.setTime(free * 1000);
            var data = {
                action: SYS.base + '/board/' + b + '/ajax_moddeny.json'
                ,id: tr.find('.c_userid').text()
                ,reason: $.trim(tr.find('.c_reason').text())
                ,freetime: t.getFullYear() + '年' + (t.getMonth() + 1) + '月' + t.getDate() + '日'
                ,day: Math.floor((free - offset) / 86400) - Math.floor((now - offset) / 86400)
                ,maxday: SESSION.get('is_admin')?70:14
            };
            var d = DIALOG.formDialog(_.template($('#tmpl_denymod').html())(data),{
                width: 450,
                buttons:[
                    {text:SYS.code.COM_SUBMIT,click:function(){
                        var f = $(this).find('#m_deny');
                        $.post(f.attr('action'), f.getPostData(), function(repo){
                            DIALOG.ajaxDialog(repo);
                        });
                        $(this).dialog('close');
                    }},
                    {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
                ]
            }).on('change', 'select', function(){
                $(this).prev().val($(this).val()).change();
            });
            d.find('select').html($('#deny_list select').html());
            return false;
        });
        $('.del_deny').click(function(){
            var tr= $(this).parents('tr:first');
            var id = tr.find('.c_userid').text();
            DIALOG.confirmDialog('确认解封 ' + id + ' 吗?', function(){
                $.post(SYS.base + '/board/' + b + '/ajax_deldeny.json', {id:id}, function(res){
                    DIALOG.ajaxDialog(res);
                }, 'json');
            });
            return false;
        });
        $('#deny_list input[type="button"]').prop('disabled', false);
    }
    if($('.a-single').length > 0){
        $('#body').attachSingleArticle('.a-single');
    }
});
