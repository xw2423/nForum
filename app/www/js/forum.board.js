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
            DIALOG.alertDialog(SESSION.get('is_login')?SYS.code.MSG_NOPERM:SYS.code.MSG_LOGIN);
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
});
