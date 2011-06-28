var config_default = {
    domain: window.location.host,
    base: "",
    prefix: "nforum",
    mWidth: 1000,
    iframe:true,
    allowFrame:"/att/.*|/user/face",
    wsapi:{
        u_checkid:"/wsapi/u_checkid",
        s_boards:"/wsapi/s_boards"
    }
};
config = $.extend(config_default, config);
var uid = $.cookie(config.prefix + '[UTMPUSERID]') || 'guest',user_login = (uid !== 'guest');
$.ajaxSetup({
    timeout: 20000, 
    error: function(xhr, st, et){
        if(this.url.match(/\/widget\/([^?]*)/)){
            var id = this.url.match(/\/widget\/([^?]*)/)[1];
            if(typeof(xWidget) != "undefined"){
                xWidget.stopMask(id);
                xWidget.showError(id);
            }
            return false;
        }
    } 
});
$.extend({
    isIE:function(){
        //thanks for Aleko
        typeof(this._ie === "undefined") && (this._ie = !-[1,]);
        return this._ie;
    },
    isTop:function(){
        return (window.top == window.self);
    }
});
$.fn.extend({
    adjust:function(bound, width){
        var p = this;
        p.data("o_w", p.width());
        function _adjust(width){
            var ow = p.data("o_w");
            p.width((ow>width)?width:ow);
        }
        _adjust(bound.width() - width);
        $(window).resize(function(){
            _adjust(bound.width() - width);
        });
        return this;
    },
    makeRC:function(setting){
        var rc_setting ={
            in_head_border:null,
            in_head_bg:null,
            in_bottom_border:null,
            in_bottom_bg:null,
            out_bg:null
        };
        setting = $.extend(rc_setting, setting);
        var ob = setting.out_bg?('background-color:' + setting.out_bg):"";
        var hr1 = setting.in_head_border?('background-color:' + setting.in_head_border):"",
            hr = (setting.in_head_bg?('background-color:' + setting.in_head_bg):"") + (setting.in_head_border?(';border-color:' + setting.in_head_border):""),
            br1 = setting.in_bottom_border?('background-color:' + setting.in_bottom_border):"",
            br = (setting.in_bottom_bg?('background-color:' + setting.in_bottom_bg):"") + (setting.in_bottom_border?(';border-color:' + setting.in_bottom_border):"");
        return this.prepend('<div class="rc-head" style="' + ob + '"><b class="rc1" style="' + hr1 + '"></b><b class="rc2" style="' + hr + '"></b><b class="rc3" style="' + hr + '"></b></div>')
            .append('<div class="rc-bottom"style="' + ob + '"><b class="rc3" style="' +  br+ '"></b><b class="rc2" style="' +  br + '"></b><b class="rc1" style="' +  br1+ '"></b></div>').css("zoom", 1);
    }
});
//make element resize
if($('.resizeable').length > 0){
    $('.resizeable').each(function(){
        $(this).load(function(){
            $(this).adjust($('body'), 410);
        }); 
        $.isIE() && (this.src = this.src);
    });
}
$(function(){
    if($.isTop()){
        $(window).resize(function(e){
            e.stopPropagation();    
            if($(window).width() <= config.mWidth)
                $('body').width(config.mWidth);
            else
                $('body').width("100%");
            return false;
        });
    }else if(!config.iframe && !window.location.pathname.match(config.allowFrame)){
        window.top.location.href = window.location.href;
    }

    $(window).resize();
    $('#main_wrap').makeRC({
        in_head_border:"#C3D9FF",
        in_head_bg:"#FFF",
        in_bottom_border:'#C3D9FF',
        in_bottom_bg:"#FFF",
        out_bg:"#ECF4FD"
    });
    if($('.m-table').length > 0){
        $('.m-table tr').mouseover(function(){
            $(this).addClass("mouseover");
        }).mouseout(function(){
            $(this).removeClass("mouseover");
        });
    }
    if($.isIE()){
        $('.input-text,textarea').focus(function(){
            $(this).addClass("ie-input-focus");
        }).blur(function(){
            $(this).removeClass("ie-input-focus");
        });
    }
});
