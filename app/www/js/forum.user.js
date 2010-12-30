$(function(){
    //for user query
    if($('#f_search').length > 0){
        $('#f_search').submit(function(){
            window.location.href = config.base + '/user/query/' + $('#s_name').val();
            return false;
        });
    }

    //for passwd
    if($('#p_submit').length > 0){
        $('#p_submit').submit(function(){
            if($('#p_new1').val() != $('#p_new2').val()){
                alert('两次输入的密码不一样!');
                $('#p_new1').select();
                return false;
            }
        });    
    }
});
