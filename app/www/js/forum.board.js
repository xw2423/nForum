$(function(){
    $('.board-list tbody tr:odd td').addClass('bg-odd');
    $('#board_search').submit(function(){
        BODY.open($(this).attr('action') + '?' + _.map($(this).getPostData(),function(v,k){
            return k + '=' + encodeURIComponent(encodeURIComponent(v));
        }).join('&'));  
        return false;
    });

    var validPost = function(){
        if(!user_post){
            DIALOG.alertDialog(SESSION.get('is_login')?SYS.code.MSG_NOPERM:SYS.code.MSG_LOGIN);
            return false;
        }
    };
    $('#body').on('click','.b-post',validPost)
        .on('click', '#b_fav', function(){
            var url = SYS.base + "/fav/op/0.json"
            ,data = {ac:'ab', v:$(this).attr('_b')};
            $.post(url, data, function(json){
                if(json.ajax_st == 1)
                    APP.renderTree();
                DIALOG.ajaxDialog(json);
            }, "json");
        }).on('mouseover', '#board_search .input-text', function(){
            $(this).select();
        });
});
