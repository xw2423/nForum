$(function(){
    $('#addform').dialog({
        modal:true,
        resizable:false,
        autoOpen:false,
        title:"添加",
        width:350,
        zIndex:2,
        bgiframe:true
    }).find('input[name="sTime"],input[name="eTime"]').datepicker({dateFormat:"yy-mm-dd"});
    $('#modifyform').dialog({
        modal:true,
        resizable:false,
        autoOpen:false,
        title:"修改",
        width:350,
        bgiframe:true
    }).find('input[name="sTime"],input[name="eTime"]').datepicker({dateFormat:"yy-mm-dd"});
;
    $('#preview').dialog({
        modal:true,
        resizable:false,
        autoOpen:false,
        width:660,
        title:"预览",
        bgiframe:true
    });
    $(document).mousedown(function(e){
        e.stopPropagation();    
        if($('#preview').dialog('isOpen')) 
            $('#preview').dialog('close');
    });

    $('.b_mod').click((function(type){
        if(type){
            return function(){
                var form = $('#modifyform'),
                item = $(this).parents('tr');
                form.find('input[name="aid"]').val(item.find('.c_1').attr("id"));
                form.find('input[name="url"]').val($.trim(item.find('.c_3').text()));
                form.find('input[name="sTime"]').val(item.find('.c_4').text());
                form.find('input[name="eTime"]').val(item.find('.c_5').text());
                form.find('input[name="privilege"]').attr("checked", (item.find('.c_6').text() == "是"));
                form.find('input[name="home"]').attr("checked", (item.find('.c_9').text() == "是"));
                form.find('input[name="remark"]').val(item.find('.c_8').text());
                $('#modifyform').dialog('open');
            }
        }else{
            return function(){
                var form = $('#modifyform'),
                item = $(this).parents('tr');
                form.find('input[name="aid"]').val(item.find('.c_1').attr("id"));
                form.find('input[name="url"]').val($.trim(item.find('.c_3').text()));
                form.find('input[name="switch"]').attr("checked", (item.find('.c_4').text() == "是"));
                form.find('input[name="weight"]').val(item.find('.c_5').text());
                form.find('input[name="home"]').attr("checked", (item.find('.c_9').text() == "是"));
                form.find('input[name="remark"]').val(item.find('.c_8').text());
                $('#modifyform').dialog('open');
            }
        }
    })($('.b_mod').attr('_type')));

    $('.b_del').click(function(){
        if(confirm("确认删除?")){
            $('#delform').find('input[name="aid"]').val($(this).parents('tr').find('.c_1').attr('id'));
            $('#delform').submit();
        }
    });

    $('#b_add').click(function(){
        $('#addform').dialog('open');
    });
});
