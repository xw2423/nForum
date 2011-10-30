$(init);
function init(){
    $('.board-list tr').mouseover(function(){
        $(this).addClass("mouseover");
    }).mouseout(function(){
        $(this).removeClass("mouseover");
    });
    $('.board-list tr:odd').addClass('bg-odd');
    $('#t_search').mouseover(function(){
        $(this).select();
    });
    var validPost = function(){
        if(!user_post){
            alert(user_login?'您没有发文权限!':'请先登录!');
            return false;
        }
    };
    $('#b_post').click(validPost);
    $('#b_tmpl').click(validPost);
}
function favadd(name){
    var url = config.base + "/fav/op/0";
    var data = {ac:'ab', v:name};
    $.post(url, data, function(json){
        if(json.st == "success"){
            alert("添加成功");
        }else if(json.st == "error"){
            alert('发生错误\ncode:' + json.code + '\n' + json.msg);
        }
    }, "json");
}
