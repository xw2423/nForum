var Fav = {
    areaSelector:"#ajaxArea",
    level: 0,
    pLevel: new Array(),
    template: "",

    init: function(){
        Fav._init();
        $('#update').bind('click', Fav.update);
        $('#dir_btn').bind('click', {type:"ad"}, Fav.add);
        $('#board_btn').bind('click', {type:"ab"}, Fav.add);
        $('#pLevel').bind('click', Fav.goParent).hide();
        Fav.update();
    },

    _init: function(){
        Fav.template = $(Fav.areaSelector).html();    
        $(Fav.areaSelector).empty().show();
    },

    add: function(event){
        var type = event.data.type;
        var url = config.base + "/fav/op/" + Fav.level;
        var data = {ac:type, v:$('#' + type + '_txt').val()};
        $.post(url, data, Fav.onAdd, "json");
    },
    
    onAdd: function(json){
        if(json.st == "success"){
            Fav.update();
        }else if(json.st == "error"){
            alert('发生错误\ncode:' + json.code + '\n' + json.msg);
        }
    },

    update: function(num, p){
        if(!isNaN(num)){
            if(p != true){
                Fav.pLevel.push(Fav.level);
                $('#pLevel').show();
            }
            Fav.level = num;
        }
        $(Fav.areaSelector).empty();
        var url = config.base + "/fav/" + Fav.level + "?_v=" + Math.round(Math.random()* 10000);
        $.getJSON(url, Fav.onUpdate);
    },

    onUpdate: function(json){
        var ret = "",
        json = json.v;
        if(json.length > 0){
            for(i in json){
                var link = "";
                var content = Fav.template;
                if(json[i]['name'] == ""){
                    link = 'javascript:Fav.update(' + json[i]['bid'] + ');';
                content = content.replace(/<td class="title_3[\s\S]*?<\/td>/i, '<td class="title_3">&nbsp;</td>')
                    .replace(/%7%/, "&nbsp;")
                    .replace(/%8%/, "&nbsp;")
                    .replace(/%9%/, "&nbsp;")
                    .replace(/%10%/, "&nbsp;")
                    .replace(/%2%/, '[自定义目录]')
                    .replace(/"[^"]*?%0%"/, link);
                }else{
                    content = content.replace(/%2%/, json[i]['bm'])
                        .replace(/%3%/, json[i]['last']['id'])
                        .replace(/%4%/, json[i]['last']['title'])
                        .replace(/%5%/, json[i]['last']['owner'])
                        .replace(/%6%/, json[i]['last']['date'])
                        .replace(/%7%/, json[i]['pnum'])
                        .replace(/%8%/, json[i]['tnum'])
                        .replace(/%9%/, json[i]['thnum'])
                        .replace(/%10%/, json[i]['num']);
                    if(json[i]['last']['id'] == "无")
                        content = content.replace(/"[^"]*\/article\/[^"]*"/, "javascript:void(0);");
                    if(json[i]['dir'] == 1)
                        content = content.replace(/board\/%0%/, "section/" + json[i]['name']);
                }
                var ac = (json[i]['name'] == "")?"dd":"db";
                var param = '\'' + ac +'\', ' + json[i]['pos'];
                var del = "javascript:Fav.remove(" + param + ")";
                content = content.replace(/%0%/g, json[i]['name']);
                content = content.replace(/%1%/, json[i]['desc']);
                content = content.replace(/%11%/, del);
                ret += content;
            }
        }else{
            ret = Fav.template.replace(/^[\s\S]*$/, '<tr><td colspan="8" style="text-align:center;border-style:none">不存在任何版面</td></tr>');
        }
        $(Fav.areaSelector).append(ret);
    },

    remove: function(ac, v){
        var url = config.base + "/fav/op/" + Fav.level;
        var data = {ac:ac, v:v};
        $.post(url, data, Fav.onRemove, "json");
    },

    onRemove: function(json){
        if(json.st == "success"){
            Fav.update();
        }else if(json.st == "error"){
            alert("发生错误\ncode:" + json.code + "\n" + json.msg);
        }
    },

    goParent: function(event){
        if(Fav.pLevel.length > 0)
            Fav.update(Fav.pLevel.pop(), true);
        if(Fav.pLevel.length == 0)
            $('#pLevel').hide();
    }
};
$(function(){
    Fav.init();
    $('#ajaxArea tr').live("mouseover",function(){
        $(this).addClass("mouseover");
    }).live("mouseout", function(){
        $(this).removeClass("mouseover");
    });
});
