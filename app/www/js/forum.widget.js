var wUrl = "/widget/setw";
var wApp = {
    config: config,
    template:"",
    areaSelector:"#application",
    filterSelector:".tab-normal",

    init: function(){
        this._init();
        $(this.filterSelector).click(function(){
            if($(this).hasClass('tab-down'))
                return false;
            $(this).addClass('tab-down').siblings().removeClass('tab-down');
            wApp.update(this.id.slice(this.id.indexOf('_')+1)); 
        }).eq(0).click();
    },

    _init: function(){
        this.template = $(this.areaSelector).html();
        $(this.areaSelector).empty().show();
    },
    
    update: function(type){
        var type = type.split("-");    
        var data = {t:type[0], tt:type[1], _v:Math.round(Math.random() *1000)};

        $(this.areaSelector).empty().append('<div style="text-align:center">加载中...</div>');
        $.getJSON(config.base + '/widget/list', data, function(json){
            if(json.st == "success"){
                json = json.v;
                if(json.length > 0){
                    var ret = "";
                    for(i in json){
                        var content = wApp.template;
                        content = content.replace(/%0%/, json[i]['wid'])
                        .replace(/%1%/, json[i]['title'].substring(0, 8))
                        .replace(/%2%/, json[i]['title'])
                        .replace(/%3%/, json[i]['p'])
                        .replace(/_src/, 'src');
                        ret += content;
                    }
                }else{
                    ret = wApp.template.replace(/^[\s\S]*$/, '<div style="text-align:center">不存在可添加的应用</div>');
                }
                $(wApp.areaSelector).empty().append(ret);
            }else{
                alert("发生错误\ncode:" + json.code + "\n" + json.msg);
            }

        });
    }
};

$(function(){
    $('#addform').dialog({
        modal:true,
        resizable:false,
        autoOpen:false,
        title:"添加新应用",
        width:250,
        bgiframe:true
    });
    $('#application li :submit').live("click", function(event){    
        var p = $(this).parents('li');
        $('#addform #title').val(p.attr("title"));
        $('#addform #wid').val(p.attr('id'));
        $('#addform').dialog('open');
    });
    $('#addform .submit').click(function(){    
        var wid = $('#wid').val(),
            title = $('#title').val();
        title = encodeURI(title);
        var data = {t:0, w:wid, ti:title,
            co:$('#color option:selected').val(),
            c:$('#col option:selected').val(), r:1},
            url = config.base + wUrl;
        $.getJSON(url, data, function(json){
            if(json.st == "success"){
                $('#' + wid + ' :submit').val("已添加至首页")
                    .attr('disabled', true);
            }else{
                alert("发生错误\ncode:" + json.code + "\n" + json.msg);
            }
        });
        $('#addform').dialog('close');
    });
    $("#w_search_btn").click(function(){wApp.update("search-" + encodeURI($("#w_search_txt").val()))});
    $("#w_search_txt").mouseover(function(){$(this).select();});
    wApp.init();
});

