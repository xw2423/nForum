/* SYS */
window.SYS = {
    domain: window.location.host,
    cookie_domain: window.location.host,
    base: "",
    home: "/default",
    prefix: "nforum",
    mWidth: 1000,
    iframe:true,
    allowFrame:"/att/.*|/user/face",
    protocol:"http://",
    redirect:3,
    session:{
        timeout:30
    },
    ajax:{
        session:'user/ajax_session.json',
        login:'user/ajax_login.json',
        logout:'user/ajax_logout.json',
        valid_id:'user/ajax_valid_id.json',
        user:'user/query',
        section_list:'slist.json',
        favor_list:'flist.json',
        friend_list:'friend/ajax_list.json'
    },
    widget:{
        widgets:{
            board:{rss:true},
            topten:{rss:true},
            recommend:{rss:true},
            bless:{rss:true}
        },
        url:"/widget/set.json",
        persistent:false
    },
    //cache value via key,if value null return value of key
    cache: function(key, value){
        if(typeof key !== 'string') return this._cache;
        if(typeof value !== 'undefined') return this._cache[key] = value;
        if(this._cache[key]) return this._cache[key];
        return null;
    },
    //clear cache via key,if key null,clear all
    clear: function(key){
        var ret = false;;
        if(typeof key !== 'string'){
            ret = this._cache;
            delete this._cache;
            this._cache = {};
        }else if(this._cache[key]){
            ret = this._cache[key];
            delete this._cache[key];
        }
        return ret;
    },
    _cache:{}
};
if(typeof sys_merge != 'undefined') SYS = $.extend(true, SYS, window.sys_merge);
SYS.code = {
    COM_OK:'确定',
    COM_CANCAL:'取消',
    COM_SUBMIT:'提交',
    COM_TITLE:'提示',
    COM_COMFIRM:'确认',
    COM_DETAIL:'详细信息',
    COM_REDIRECT:'单击以下按钮可以跳转至',
    COM_PREVIEW:'预览',
    COM_COLOR:'颜色',
    COM_LOADING:'请稍后...',
    MSG_SYSERROR:'系统发生错误!',
    MSG_LOADING:'数据加载中...',
    MSG_USER:'请输入用户名',
    MSG_PWD:'请输入密码',
    MSG_LOGIN:'您还未登录,请先登录!',
    MSG_NOPERM:'您没有发文权限!',
    MSG_SUBJECT:'请输入标题!',
    MSG_REPEAT:'请不要重复发文!',
    MSG_NULL:'不要回复空内容嘛!',
    MSG_UNLOAD:'当前内容将被丢弃,你确定离开此页?',
    MSG_DELETE:'确认删除文章?',
    WIG_LAST:'你只剩下一个模块了!',
    WIG_DEL:'模块删除后您可以在左侧"个性首页设置"中重新添加',
    WIG_NONE:'该应用不存在或被关闭',
    WIG_ERROR:'获取数据发生错误',
    MSG_JOKE:'WTF'
};
if(typeof code_merge != 'undefined') SYS.code = $.extend(SYS.code, window.code_merge);
