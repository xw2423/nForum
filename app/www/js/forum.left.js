$(function(){
    function serialize(){
        var ret = [];
        $('#xlist .child_list').each(function(i, e){ret[i] = $(e).is(':visible')?1:0});
        $.cookie("nforum-left",ret.join(""), {path:'/', domain:config.domain,expires: 30});
    }
    function deserialize(){
        var data = $.cookie("nforum-left") || "100";
        data = data.split("");
        $('#xlist .child_list').each(function(i, e){
            $(e)[parseInt(data[i])?"show":"hide"]().prev().children('samp').filter(parseInt(data[i])?".ico-pos-tag-off":'').replaceClass("ico-pos-tag-off", "ico-pos-tag-on");
        });
    } 
    deserialize();
    $(".has_child>a").click(function(){
        $(this).next().toggle();
        $(this).find('>samp').swapClass('ico-pos-tag-on', 'ico-pos-tag-off');
        serialize();
    });

    $('#b_search').keydown(function(event){
        if(event.keyCode == 13){
            window.location.href = config.base + '/s/board?b=' + $(this).val();
        }
    }).mouseover(function(){$(this).select();return false;});
    $('#bb_reg').click(function(){window.location.href=config.base + "/reg"});

    $('#list-section').treeview({unique: true, collapsed:true, url:config.base + "/slist?uid=" + uid, asyncpersist:true});
    if(uid != "guest")
        $('#list-favor').treeview({unique: true, collapsed:true, url:config.base + "/flist?uid=" + uid});
    $('#left-line samp').click(function(){
        var s = !$(this).data("_show");
        $(this).swapClass('ico-pos-show', 'ico-pos-hide').data("_show", s?1:0);
        $('#menu').width(s?156:0).children(':not(#left-line)')[s?"show":"hide"]().end().next().css("margin-left", s?162:5);
    $.cookie("left-show", s?1:0,{path:'/', domain:config.domain,expires:30});
    }).data("_show", 1);
    if($.cookie("left-show") == 0){
        $('#left-line samp').click();
    }
});
