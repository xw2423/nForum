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
    var validLogin = function(){
        if(!user_post && !user_login){
            alert('发帖请先登录!');
            return false;
        }
    };
    $('#b_post').click(validLogin);
    $('#b_tmpl').click(validLogin);
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
