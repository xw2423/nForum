$(function(){
    var tmpl_manage = _.template($('#tmpl_manage').html() || '');
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
    })
});
