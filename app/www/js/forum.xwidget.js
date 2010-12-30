var    widgets={
    board:{rss:true},
    topten:{rss:true},
    recommend:{rss:true},
    bless:{rss:true}
},wUrl = "/widget/setw";
persistent = (typeof persistent == 'undefined')?false:persistent;


/*
 * script for byr widget
 * @requires jquery, jquery-ui core & sortable module 
 * @author xw
 */

var xWidget = {
    config : config,
    version: 1.6,
    login: false,
    uid: "guest",
    settings : {
        bound : 'document',
        columns : '.column',
        widgetSelector: '.widget',
        headSelector: '.widget-head',
        titleSelector: '.widget-title',
        opSelector: '.widget-op',
        contentSelector: '.widget-content',
        addSelector: '#widget-add',
        maxTitle: 10,

        colors : [
            'color-default',
            'color-red',
            'color-orange', 
            'color-yellow',
            'color-green',
            'color-blue',
            'color-white'
        ],

        widgetDefault : {
            movable: true,
            removable: true,
            collapsible: true,
            editable: true,
            active: true,
            rss: false,
            url: "/widget",
            style: "w-free"
        }
    },

    init : function (login, id) {
        this.login=login;
        this.uid=id;
        this.initControl();
        this.makeSortable();
    },

    getSettings : function (id) {
        var settings = this.settings,
        config = widgets;
        return (id&&config[id.split("-")[0]]) ? $.extend({},settings.widgetDefault,config[id.split("-")[0]]) : settings.widgetDefault;
    },
    
    initControl : function () {
        var xWidget = this,
        settings = this.settings,
        columnNum = $(settings.columns).length;
            
        if(columnNum != 3)
            $(settings.columns).css("width", 100/columnNum-0.1 + "%").filter(":last").find(settings.widgetSelector).css("margin-right", 0);
        $(settings.bound).resize(function(){$(settings.columns + ' ul').css("width" ,$(settings.columns).width())});

        //tab-control
        $('.w-tab-title li').live("click", function(){
            $(this).addClass('tab-down').parents('.w-tab-title').nextAll('.w-tab-content').hide().eq($(this).attr('_index')).show();
            $(this).siblings().removeClass('tab-down');
        });

        $(settings.widgetSelector).each(function () {
            var wSet = xWidget.getSettings(this.id);
            if (wSet.rss) {
                var rss = "/rss/" + this.id;

                $('<a target="_blank" href="' + config.base + rss + '" class="rss"><samp class="ico-pos-w-rss" /></a>')
                .prependTo($(settings.titleSelector,this));
            }

            if (wSet.collapsible) {
                $('<a href="#" class="collapse"><samp class="ico-pos-w-collapse-on" /></a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).toggle(function () {
                    $(this).find('>samp')
                        .removeClass("ico-pos-w-collapse-on")
                        .addClass("ico-pos-w-collapse-off")
                        .parents(settings.widgetSelector)
                        .find(settings.contentSelector).slideUp();
                    return false;
                },function () {
                    $(this).find('>samp')
                        .removeClass("ico-pos-w-collapse-off")
                        .addClass("ico-pos-w-collapse-on")
                        .parents(settings.widgetSelector)
                        .find(settings.contentSelector).slideDown();
                    return false;
                }).prependTo($(settings.titleSelector ,this));
            }
            
            if (wSet.active) {
                var id = this.id;
                $('<a href="#" class="update"><samp class="ico-pos-w-update" /></a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).click(function(e){
                    xWidget.updateWidget(id);
                    return false;
                }).appendTo($(settings.opSelector, this));
            }

            if (wSet.editable) {
                $('<a href="#" class="edit"><samp class="ico-pos-w-edit-on"/></a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).toggle(function () {
                    var widget = $(this).find('>samp').css("width", 55)
                        .removeClass("ico-pos-w-edit-on")
                        .addClass("ico-pos-w-edit-off")
                        .parents(settings.widgetSelector);
                    var input = widget.find('.edit-box').slideDown('fast').find('input').focus();
                    widget.data("_st", {title:input.val(),color:widget.attr('class').split(" ")[1]}).data("_show", 1);

                    return false;
                },function () {
                    $(this).find('>samp').css("width", 24)
                        .removeClass("ico-pos-w-edit-off")
                        .addClass("ico-pos-w-edit-on")
                        .parents(settings.widgetSelector).data("_show", 0)
                        .find('.edit-box').slideUp('fast');
                    xWidget.doModify($(this).parents((settings.widgetSelector)));
                    return false;
                }).appendTo($(settings.opSelector,this));

                $('<div class="edit-box" style="display:none;"/>')
                    .append('<div class="item"><label>标题:</label><input value="' + $(settings.titleSelector,this).text() + '"/></div>')
                    .append((function(){
                        var colorList = '<div class="item colors"><label>颜色:</label>';
                        $(settings.colors).each(function () {
                            colorList += '<span class="' + this + '"/>';
                        });
                        return colorList + '</div>';
                    })())
                    .insertAfter($(settings.headSelector,this));
            }

            if (wSet.removable) {
                $('<a href="#" class="remove"><samp class="ico-pos-w-remove" /></a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).click(function () {
                    if(confirm('删除后您可以在个性首页设置中重新添加')) {
                        xWidget.doDelete($(this).parents(settings.widgetSelector));
                    }
                    return false;
                //'this' is for the no head
                }).appendTo($(settings.opSelector, this));
            }
        });
        
        $('.edit-box').each(function () {
            $(this).find('input').keyup(function () {
                xWidget.setTitle($(this).parents(settings.widgetSelector), this.value);
            });
            $(this).find('.colors span').click(function () {
                xWidget.setColor($(this).parents(settings.widgetSelector),$(this).attr('class').match(/\bcolor-[\w]{1,}\b/)[0]);
                return false;
            });
        });
        
    },
    
    makeSortable : function () {
        var settings = this.settings,
            sortableItems = (function () {
                var notSortable = '';
                $(settings.widgetSelector,$(settings.columns)).each(function (i) {
                    if (!xWidget.getSettings(this.id).movable) {
                        if(!this.id) {
                            this.id = 'widget-nomove-' + i;
                        }
                        notSortable += '#' + this.id + ',';
                    }
                });
                if(notSortable != '')
                    return $('> li:not(' + notSortable + ')', settings.columns);
                else
                    return $('> li',settings.columns);
            })();
        
        sortableItems .data("_show", 0)
        .each(function(){$(this).data('_u', 0).data('_col', xWidget.getPos($(this)).col);})
        .find(settings.headSelector).css({
            cursor: 'move'
        }).mouseup(function (e) {
            $(this).find('.remove,.edit,.update').hide();
        }).mouseover(function(e){
            $(this).find('.remove,.edit,.update').show();
        }).mouseout(function(e){
            if($(this).parents(settings.widgetSelector).data("_show") == 0)
                $(this).find('.remove,.edit,.update').hide();
        }).find('.remove,.edit,.update').hide();
        $(settings.columns).sortable({
            items: sortableItems,
            connectWith: $(settings.columns),
            handle: settings.headSelector,
            placeholder: 'widget-placeholder',
            forcePlaceholderSize: true,
            update: function (e,ui) {
                xWidget.doMove($(ui.item));
            },
            opacity: 0.8,
            containment: settings.bound
        });
        if(!persistent)
            sortableItems.each(function(){xWidget.updateWidget(this.id);})
    },

    updateWidget: function(id){
        var settings = this.settings,
        ids = id.split("-"),
        idset = this.getSettings(ids[0]);
        if(idset.active){
            var url = config.base + idset.url + "/" + id,
            data = {'uid': xWidget.uid, '_v':xWidget.version};
            xWidget.startMask(id);    
            $.getJSON(url, data,function(json){
                if(json.st == "success"){
                    var con = $('#' + json.v.id + ' ' + settings.contentSelector);
                    if(json.v.list)
                        var str = xWidget.makeStyle(null, json.v.list);
                    con.is(':empty')?con.hide().append(str).slideDown():con.empty().append(str);
                }else{
                    $('#' + id + ' ' + settings.contentSelector).empty().append('<ul><li style="text-align:center">该应用不存在或被关闭</li></ul>');
                }
                xWidget.stopMask(id);
            });
        }
    },
    getPos: function(widget){
        var p = widget.parent();
        var pp = p.parent();
        return {col:pp.children().index(p) + 1, row:p.children().index(widget) + 1};
    },

    doMove: function(widget){
        if(!this.login) return;
        //update function will triggle twice,i will cancel one
        var pos = this.getPos(widget);
        if(widget.data('_u') == 1){
            widget.data('_u', 0) ;
            return
        }
        if(widget.data('_col') != pos.col){
            widget.data('_u', 1).data('_col', pos.col);
        }else{
            widget.data('_u', 0) ;
        }
        var url = config.base + wUrl,
        data = {'t':2, 'w':widget.attr('id'), 'c':pos.col, 'r':pos.row, '_t':Math.round(Math.random()* 10000)};

        $.get(url, data);
    },

    doDelete: function(widget){
        if(!this.login){
            widget.animate({
                opacity: 0    
            },function () {
                $(this).wrap('<div/>').parent().slideUp(function () {
                    $(this).remove();
                });
            });
            return
        }
        var url = config.base + wUrl,
        data = {'t':1, 'w':widget.attr('id'),'_t':Math.round(Math.random()* 10000)};
        $.getJSON(url, data, function(json){
            if(json.st == "success" || json.st == undefined){
                widget.animate({
                    opacity: 0    
                },function () {
                    $(this).wrap('<div/>').parent().slideUp(function () {
                        $(this).remove();
                    });
                });
            }else{
                alert(json.msg + '或是你只剩下一个模块了');
            }
        });
    },

    doModify: function(widget){
        if(!this.login) return;
        var url = config.base + wUrl,
        wid = widget.attr('id'),
        title = widget.find('input').val(),
        color = widget.attr('class').split(" ")[1],
        cid = 0;
        if(color == widget.data('_st').color && title == widget.data('_st').title)
            return;
        title = encodeURI(title);
        for(i in this.settings.colors){
            if(this.settings.colors[i] == color){
                cid = i;
                break;
            }
        }
        var data = {'t':4, 'w':wid, 'ti':title, 'co':cid, '_t':Math.round(Math.random()* 10000)};
        $.getJSON(url, data, function(json){
            xWidget.setTitle(widget,json.v.t);
            xWidget.setColor(widget,json.v.c);
        });
    },

    setTitle:function(widget, title){
        widget.find(xWidget.settings.titleSelector + " a:last").text(title.length>xWidget.settings.maxTitle ? title.substr(0,xWidget.settings.maxTitle)+'...' : title);
    },

    setColor:function(widget, color){
        var colorStylePattern = /\bcolor-[\w]{1,}\b/,
            thisWidgetColorClass = widget.attr('class').match(colorStylePattern);
        if (thisWidgetColorClass) {
            if(typeof xWidget.settings.colors[color] != 'undefined')
                widget.removeClass(thisWidgetColorClass[0]).addClass(xWidget.settings.colors[color]);
            else
                widget.removeClass(thisWidgetColorClass[0]).addClass(color);
        }
    },

    startMask:function(id){
        var mask = $('<div class="widget-mask" style="width:100%;height:100%;display:inline-block;*display:inline;position:absolute;top:0;left:0;text-align:center;font-size:16px;z-index:1;opacity:0.5; *filter:alpha(opacity=50);vertical-align:middle;"><div style="display:inline-block;*display:inline;*zoom:1;height:100%;vertical-align:middle;"></div><div style="display:inline-block; *display:inline;*zoom:1; position:relative;vertical-align:middle;">加载中...</div></div>');
        var widget = $('#' + id);
        widget.css("position", "relative");
        mask.css({height:widget.height()}) .prependTo(widget);
    },

    stopMask: function(id){
        $('#' + id).css("position", "static").find('.widget-mask').remove();
    },

    showError: function(id){
        $('#' + id).find(this.settings.contentSelector).empty().append('<div style="text-align:center">获取数据发生错误</div>');
    },
    makeStyle: function(style, val){
        var ret = "";
        if(!style)
            style = "tab";
        switch(style){
            case "tab":
                    if(val instanceof Array){
                        ret += ('<div class="w-tab"><div class="w-tab-title"><ul>');
                        var li = "", t_con="";
                        for(var i in val){        
                            li += ('<li _index="' + i + '" class="tab-normal' + ((i ==0)?' tab-down':'')+ '">' + val[i].t + '</li>');
                            t_con += ('<div class="w-tab-content w-tab-' + i + '"' + ((i ==0)?' style="display:block"':'')+ '>' +xWidget.makeStyle(null, val[i].v) + '</div>');
                        }
                        ret += (li + '</ul></div>' + t_con + "</div>");
                    }else{
                        ret += xWidget.makeStyle(val.s, val.v);
                    }
                break;
            case "w-free":
                ret += '<div class="' + style + '">';
                val = val[0]||val;
                ret += val.text;
                ret += '</div>';
                break;
            case "w-list-line":
            case "w-list-float":
                ret += '<ul class="' + style + '">';
                if(!(val instanceof Array)){
                    ret += '<li>发生错误</li>';
                }
                for(var i in val){
                    var ltext = val[i].text; 
                    var otext = ltext.replace(/<[\s\S]*?>([\s\S]*?)<\/[\s\S]*?>|<[\s\S]*?\/>/g, "$1");
                    if(val[i].url && val[i].url !="")
                        ret += ('<li title="' +otext + '"><a href="' + config.base + val[i].url + '">' + ltext + '</a></li>');
                    else
                        ret += ('<li>' + ltext + '</li>');
                }
                ret += '</ul>';
                break;
        }
        return ret;
    }
};
$(function(){
    $("#columns .widget").makeRC();
    xWidget.init(user_login, uid);
});
