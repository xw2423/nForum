$(function(){
    var tmpl_manage = _.template($('#tmpl_manage').html() || '')
        ,tmpl_deny = _.template($('#tmpl_deny').html() || '');
    $('#body').on('click','.a-func-manage',function(){
        var d = DIALOG.formDialog(tmpl_manage({action:$(this).attr('href'), gid:$(this).attr('_gid')}), {
                 buttons:[
                    {text:SYS.code.COM_SUBMIT,click:function(){
                        var op = $('#manage_op').getPostData()
                            ,tp = $('#manage_top').getPostData()
                            ,data = {
                                gid:$('#a_manage_gid').val()
                                ,op:_.values(op).join('|')
                                ,'top':_.values(tp).join('|')
                            };
                        if (data['op'] == '') delete data['op'];
                        if (data['top'] == '') delete data['top'];
                        $.post($('#a_manage').attr('action'), data, function(repo){
                            DIALOG.ajaxDialog(repo);
                        });
                        $(this).dialog('close');
                    }},
                    {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
                 ]
                 ,width:520
        });
        return false;
    }).on('click','.a-func-deny',function(){
        var b = $(this).attr('_b'), reason = SYS.cache(b + '_denyreasons'), d = DIALOG.formDialog(_.template($('#tmpl_deny').html())({action:$(this).attr('href'),maxday:(SESSION.get('is_admin')?70:14),board:b,userid:$(this).attr('_u'),reason:reason || []}),{
            width: 450,
            buttons:[
                {text:SYS.code.COM_SUBMIT,click:function(){
                    var f = $('#a_deny');
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
        if(reason) return false;
        $.get(SYS.base + '/board/' + b + '/ajax_denyreasons.json',function(res){
            d.find('#a_deny_reasons').append(
                _.reduce(res,function(ret,item){
                    ret += ('<option value="' + item.desc + '">' + item.desc + '</option>');
                    return ret;
                },'')
            );
            SYS.cache(b + '_denyreasons', res);
        });
        return false;
    })
});
