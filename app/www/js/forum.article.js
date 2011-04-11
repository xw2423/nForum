$(function(){
    $('.a-wrap').makeRC({
        in_head_border:"#D4E6FC",
        in_bottom_border:'#D4E6FC',
        in_head_bg:"#F3F5FC",
        in_bottom_bg:"#F3F5FC"
    });
    $('#goToReply').click(function(){
        $('#text_a').focus();
    });
    $('#t_search').mouseover(function(){
        $(this).select();
    });

    $('#text_a').mouseover(function(){
        $(this).focus();
    }).keydown(function(event){
        if(event.ctrlKey && event.keyCode == 13){
            $('#f_post').submit();
            return false;
        }
    }).ubb({enable:false, ubb_img_path:"/img/ubb/", ubb_em:$('#em_img')});
        
    var validLogin = function(){
        if(!user_post && !user_login){
            alert('请先登录!');
            return false;
        }
    };
    $('#f_post').submit(validLogin);
    $('#b_post').click(validLogin);
    $('.a-post').click(validLogin);
    
    $('.b-head div').click(function(){
        if(window.clipboardData){
            window.clipboardData.setData("Text",window.location.href);
            alert("本页链接已经复制到剪切板");
        }else{
            alert("您的浏览器不支持此功能，请手动复制");
        }
    });
    
    BShare.init($('#a_share').attr('_u'), $('#a_share').attr('_c'))
});
