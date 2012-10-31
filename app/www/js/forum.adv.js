$(function(){
    var tmpl_adv = _.template($('#tmpl_adv').html())
        ,tmpl_adv_preview = _.template($('#tmpl_adv_preview').html());
    $('.b_mod').click((function(type){
        if(type){
            return function(){
                var item = $(this).parents('tr')
                ,data = {
                    aid:item.find('.c_1').attr("id")
                    ,url:$.trim(item.find('.c_3').text())
                    ,stime:item.find('.c_4').text()
                    ,etime:item.find('.c_5').text()
                    ,priv:item.find('.c_6').text() == "是"
                    ,home:item.find('.c_9').text() == "是"
                    ,remark:item.find('.c_8').text()
                    ,ac:'set'
                };
                DIALOG.formDialog(tmpl_adv(data),
                    {title:SYS.code.COM_TITLE, width:350,
                     buttons:[
                        {text:SYS.code.COM_SUBMIT,click:function(){
                            $('#adv_form').submit();
                        }},
                        {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
                     ]
                }).find('input[name="sTime"],input[name="eTime"]').datepicker({dateFormat:"yy-mm-dd"});
            }
        }else{
            return function(){
                var item = $(this).parents('tr')
                ,data = {
                    aid:item.find('.c_1').attr("id")
                    ,url:$.trim(item.find('.c_3').text())
                    ,home:(item.find('.c_9').text() == "是")
                    ,remark:item.find('.c_8').text()
                    ,sw:(item.find('.c_4').text() == "是")
                    ,weight:item.find('.c_5').text()
                    ,ac:'set'
                };
                DIALOG.formDialog(tmpl_adv(data),
                    {title:SYS.code.COM_TITLE, width:350,
                     buttons:[
                        {text:SYS.code.COM_SUBMIT,click:function(){
                            $('#adv_form').submit();
                        }},
                        {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
                     ]
                }).find('input[name="sTime"],input[name="eTime"]').datepicker({dateFormat:"yy-mm-dd"});
            }
        }
    })($('.b_mod').attr('_type')));

    $('.b_del').click(function(){
        var self = this;
        DIALOG.confirmDialog('确认删除?', function(){
            $('#adv_form_del').find('input[name="aid"]').val($(self).parents('tr').find('.c_1').attr('id'));
            $('#adv_form_del').submit();
        });
    });

    $('.b_pre').click(function(){
        DIALOG.formDialog(tmpl_adv_preview({img:$(this).parent().parent().find('.c_2 a').attr('href')}),
            {title:SYS.code.COM_PREVIEW, width:660}
        );
    });

    $('#b_add').click(function(){
        DIALOG.formDialog(tmpl_adv({
                    ac:'add'
                    ,aid:''
                    ,url:''
                    ,home:''
                    ,remark:''
                    ,sw:false
                    ,weight:''
                    ,stime:''
                    ,etime:''
                    ,priv:false
                }),
            {title:SYS.code.COM_TITLE, width:350,
             buttons:[
                {text:SYS.code.COM_SUBMIT,click:function(){
                    $('#adv_form').submit();
                }},
                {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
             ]
        }).find('input[name="sTime"],input[name="eTime"]').datepicker({dateFormat:"yy-mm-dd"});
    });
    $('#adv_filter').find('input[name="sTime"],input[name="eTime"]').datepicker({dateFormat:"yy-mm-dd"});
});
