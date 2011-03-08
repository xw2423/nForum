$(function(){
    /* simple tree */
    function serialize(){
        var data = []; 
        $('#xlist .x-child').each(function(i, e){data[i] = $(e).is(':visible')?1:0});
        $.cookie("nforum-left",data.join(""), {path:'/', domain:config.domain,expires: 30});
    }
    function deserialize(){
        var data = $.cookie("nforum-left") || "100";
        data = data.split("");
        $('#xlist .x-child').each(function(i, e){
            (data[i] == "1") && $(e).parent().find('>span .toggler').click();
        });
    } 
    $('.slist').SimpleTree({autoclose: true, persist:true, cookie:"left-index", ajax:config.base + "/slist?uid=" + uid + "&root=%id%"});
    $('.clist').SimpleTree({autoclose: true});
    if(uid != "guest")
        $('.flist').SimpleTree({autoclose: true,ajax:config.base + "/flist?uid=" + uid + "&root=%id%"});
    deserialize();
    $("#xlist .x-child").parent().find('>span .toggler').click(serialize);
    /* fix for first level menu */
    $("#xlist .x-folder").click(function(e){
        if(e.target != $(this).children('.toggler').get(0))
            $(this).children('.toggler').click();
        return false;
    });
    $("#xlist .x-leaf").not('.x-search').click(function(e){
        window.location.href = $(this).children('a').attr('href');
        return false;
    });
    /* fix end */
    /* simple tree end */

    /* left show */
    $('#left-line samp').click(function(){
        var s = !$(this).data("_show");
        $(this).toggleClass('ico-pos-show', 'ico-pos-hide').data("_show", s?1:0);
        $('#menu').width(s?156:0).children(':not(#left-line)')[s?"show":"hide"]().end().next().css("margin-left", s?162:5);
    $.cookie("left-show", s?1:0,{path:'/', domain:config.domain,expires:30});
    }).data("_show", 1);
    if($.cookie("left-show") == 0){
        $('#left-line samp').click();
    }
    /* left show end */

    /* buttons */
    $('#b_search').keydown(function(event){
        if(event.keyCode == 13){
            window.location.href = config.base + '/s/board?b=' + $(this).val();
        }
    }).mouseover(function(){$(this).select();return false;});
    $('#bb_reg').click(function(){window.location.href=config.base + "/reg"});
    /* buttons end */
});
