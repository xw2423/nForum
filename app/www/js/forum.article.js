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
    }).ubb({enable:false, ubb_img_path:config.base + "/img/ubb/", ubb_em:$('#em_img')});
        
    var validPost = function(){
        if(!user_post){
            alert(user_login?'您没有发文权限!':'请先登录!');
            return false;
        }
    };
    $('#f_post').submit(function(){
        if($.trim($('#text_a').val()) == ''){
            alert('不要回复空内容嘛!');
            return false;
        }
        return validPost();
    });
    $('#b_post').click(validPost);
    $('#b_tmpl').click(validPost);
    $('.a-post').click(validPost);
    
    BShare.init($('#a_share').parent(), $('#a_share').attr('_u'), $('#a_share').attr('_c'));

    if($('.map-map').length > 0)
        nForumMap.loadJs('nForumMap.parseMap', null);
});
