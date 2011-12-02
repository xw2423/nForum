/**
 * SimpleTree plugin
 * modify for nForum by xw
 */
$.fn.SimpleTree = function(opt){
    this.each(function(){
        var TREE = this,ROOT = $(this);
        TREE.option = {
            ajax: "tree_load.php?tree_id=%id%",
            persist: false,
            cookie:'',
            animate: false,        // this parameter has a value "true/false" (enable/disable animation for expanding/collapsing menu items) 
            autoclose: false,    // this parameter has a value "true/false" (enable/disable collapse of neighbor branches)
            speed: 'fast',        // speed open/close folder
            success: false,        // this parameter defines function, which executes after ajax is loaded (set to "false" by default)
            click: false        // this parameter defines function, which is executed after item clicked (set to "false" by default) 

        };
        TREE.option = $.extend(TREE.option,opt);
        TREE.setAjaxNodes = function(obj)
        {
            var url = $.trim($('>li', obj).text());
            $(obj).html(SYS.code.MSG_LOADING).show();
            if(url && url.indexOf('url:'))
            {
                url=$.trim(url.replace(/.*\{url:(.*)\}/i ,'$1'));
                $.getJSON(url,function(response){
                    if(response instanceof Array && response.length > 0)
                    {
                        obj.removeClass('ajax');
                        response = TREE.makeHtmlJson(response); 
                        obj.html(response);
                        TREE.setTreeNodes(obj, true);
                        if(TREE.option.persist)
                            TREE.deserialize(obj);
                        if(typeof TREE.option.success == 'function')
                        {
                            TREE.option.success();
                        }
                    }else{
                        var parent = obj.parent();
                        var pClassName = parent.attr('class');
                        pClassName = pClassName.replace('folder-open','leaf');
                        parent.attr('class', pClassName);
                        obj.remove();
                        //$('.toggler',parent).remove();
                    }
                });
            }
        };
        TREE.closeNearby = function(obj)
        {
            $(obj).siblings().filter('.folder-open, .folder-open-last').each(function(){
                var childUl = $('>ul',this);
                var className = this.className;
                className = className.replace('open','close');
                $(this).attr('class',className);
                if(TREE.option.animate)
                {
                    childUl.animate({height:"toggle"},TREE.option.speed);
                }else{
                    childUl.hide();
                }
            });
        };
        TREE.setEventToggler = function (obj)
        {
            if($('>span >.toggler', obj).length == 0)
                $('>span', obj).prepend('<span class="toggler"></span>');
            $('>span >.toggler', obj).bind('click', function(){

                var childUl = $('>ul',obj);
                var className = obj.className;
                if(childUl.is(':visible')){
                    className = className.replace('open','close');
                    $(obj).attr('class',className);
                    if(TREE.option.animate)
                    {
                        childUl.animate({height:"toggle"},TREE.option.speed);
                    }else{
                        childUl.hide();
                    }
                }else{
                    className = className.replace('close','open');
                    $(obj).attr('class',className);
                    if(TREE.option.animate)
                    {
                        childUl.animate({height:"toggle"},TREE.option.speed, function(){
                            if(TREE.option.autoclose)TREE.closeNearby(obj);
                            if(childUl.is('.ajax'))TREE.setAjaxNodes(childUl);
                        });
                    }else{
                        if(TREE.option.autoclose)TREE.closeNearby(obj);
                        if(childUl.is('.ajax'))TREE.setAjaxNodes(childUl);
                        childUl.show();
                    }
                }
                if(TREE.option.persist)
                    TREE.serialize(obj);
            });
        };
        TREE.setTreeNodes = function(obj, useParent)
        {
            obj = useParent? obj.parent():obj;
            $('li', obj).each(function(i){
                var className = this.className;
                var open = false;
                var childNode = $('>ul',this);

                if(childNode.size()>0){
                    var setClassName = 'folder-';
                    if(className && className.indexOf('open')>=0){
                        setClassName=setClassName+'open';
                        open=true;
                    }else{
                        setClassName=setClassName+'close';
                    }
                    this.className = setClassName + ($(this).is(':last-child')? '-last':'');
                    TREE.setEventToggler(this);
                    if(!open || className.indexOf('ajax')>=0)childNode.hide();

                }else{
                    var setClassName = 'leaf';
                    this.className = setClassName + ($(this).is(':last-child')? '-last':'');
                    $('>span', this).prepend('<span class="toggler"></span>');
                }
                $('>.text, >.active',this).bind('click', function(){
                    $('.active',TREE).attr('class','text');
                    $(this).attr('class','active');
                    if(typeof TREE.option.click == 'function')
                    {
                        TREE.option.click(this);
                    }
                });
            });
        };

        TREE.makeHtmlJson = function(json)
        {
            //[{"t":"aa", "id":"111", "n":[]}]
            var len = json.length, out = "";
            for(var i = 0; i <= len - 1; i++){
                out += '<li><span class="text">' + json[i].t + '</span>';
                if(json[i]. id){
                    out += '<ul class="ajax" style="display:none"><li>{url:' + TREE.option.ajax.replace('%id%', json[i].id) + '}</li></ul>';
                }else if(json[i].n){
                    out += '<ul>' + TREE.makeHtmlJson(json[i].n) + '</ul>';
                }
                out += '</li>';
            }
            return out;
        };

        TREE.serialize = function(obj){
            if($(obj).parent().parent().get(0) == TREE){
                var data = [];
                $(this).find('>ul >li').each(function(i,e){
                    data[i] = $(e).is(":has(>ul:visible)") ? 1 : 0;
                });
                $.cookie(TREE.option.cookie, data.join(""),{path:'/', domain:window.location.host,expires:30});
            }
        };

        TREE.deserialize = function(obj){
            if($(obj).parent().get(0) == TREE){
                var data = $.cookie(TREE.option.cookie);
                if (data) {
                    data = data.split("");
                    var len = data.length - 1;
                    for(var i=0;i<=len;i++){
                        if(data[i] == "1"){
                            $(this).find('>ul >li').eq(i).find('>span .toggler').click();
                            break;
                        }
                    }
                }
            }
        };

        TREE.init = function(obj)
        {
            obj.each(function(){
                TREE.setEventToggler(this);
                this.className += " folder-close";
                if(TREE.option.persist)
                    TREE.deserialize(this);
            });
        };
        TREE.init(ROOT);
    });
};

