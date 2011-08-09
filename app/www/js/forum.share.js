;var BShare = {
    sites:[
        {
           "title":"分享到新浪微博"
           ,"url":"http://service.t.sina.com.cn/share/share.php?url=%url%&title=%content%&pic=&ralateUid="
           ,"img":'http://www.sinaimg.cn/blog/developer/wiki/16x16.png'
        }
       ,{
           "title":"分享到腾讯微博"
           ,"url":"http://v.t.qq.com/share/share.php?url=%url%&title=%content%"
           ,"img":'http://v.t.qq.com/share/images/s/weiboicon16.png'
       }
       ,{
           "title":"分享到搜狐微博"
           ,"url":"http://t.sohu.com/third/post.jsp?url=%url%&title=%content%&content=utf-8"
           ,"img":'http://s2.cr.itc.cn/img/t/152.png'
       }
       ,{
           "title":"分享到人人网"
           ,"url":"http://www.connect.renren.com/share/sharer?url=%url%&title=%content%"
           ,"img":'http://wiki.dev.renren.com/mediawiki/images/b/bd/Logo16.png'
       }
       ,{
           "title":"分享到开心网"
           ,"url":"http://www.kaixin001.com/repaste/bshare.php?rurl=%url%&rtitle=%content%"
           ,"img":'http://img1.kaixin001.com.cn/i3/platform/ico_kx16.gif'
       }
       ,{
           "title":"分享到豆瓣"
           ,"url":"http://www.douban.com/recommend/?url=%url%&title=%content%"
           ,"img":'http://img2.douban.com/pics/fw2douban_s.png'
       }
    ]
    ,template:'<img style="margin-left:5px;cursor:pointer;vertical-align:bottom;width:16px;height:16px;" title="%title%" src="%img%" />'
    ,init:function(area, url, content){
        $(this.sites).each(function(i,e){
            var template = BShare.template;
            e.url = e.url.replace('%url%', encodeURIComponent(url));
            e.url = e.url.replace('%content%', encodeURIComponent(content));
            template = template.replace('%img%', e.img); 
            template = template.replace('%title%', e.title); 
            $(template).bind('click', e.url, BShare.handler).appendTo(area);
        });
    }
    ,handler:function(e){
        var f=function(){
            if(!window.open(e.data,'_blank','toolbar=0,resizable=1,scrollbars=yes,status=1,width=640,height=480'))
                location.href=e.data;
        };
        if(/Firefox/.test(navigator.userAgent))
            setTimeout(f,0);
        else
            f();
    }
};
