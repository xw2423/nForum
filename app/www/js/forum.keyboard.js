;(function($){
    var kb = {
        enable:typeof SYS.keyboard === 'boolean'?SYS.keyboard:true
        ,scrollStep:100
    }
    ,self = this
    ,_focus = null
    ,scanner = {
        _str:''
        ,_cb:function(){}
        ,_filter:function(){}
        ,_enable:false
        ,start:function(){
            this._str = '';
            this._enable = true;
            return this;
        }
        ,stop:function(){
            this._cb.call(this, this._str);
            this._enable = false;
            return this;
        }
        ,scan:function(e){
            if(this._enable){
                if(e.keyCode == 13){
                    this.stop()
                }else{
                    this._str += this._filter.call(this, e);
                }
                return false;
            }
            return true;
        }
        ,register:function(cb){
            if(typeof cb === 'function')
                this._cb = cb;
            return this;
        }
        ,filter:function(f){
            if(typeof f === 'function')
                this._filter = f;
            return this;
        }
    }
    ,page = {
        //{type:'', list:[], page:0, row:0, mPage:, mRow:}
        _page:null
        ,_action:['init', 'up', 'down', 'left', 'right', 'open', 'jump', 'quit', 'reply', 'post', 'subUp', 'subDown']
        ,_type:['board', 'article', 'mail', 'fav', 's', 'section', 'home']
        ,_cb:{}
        ,_url:''
        ,_cache:{}
        ,init:function(){
            for(var i=-1;this._type[++i];){
                this._cb[this._type[i]] = {};
                for(var j=-1;this._action[++j];){
                    this._cb[this._type[i]][this._action[j]] = function(){}
                }
            }
            return this;
        }
        ,register:function(_t, _a, _cb){
            if(typeof _t != 'string') return this;
            if(!this._cb[_t]) return this;
            if(typeof _a == 'object'){
                for(var i in _a){
                    this.register(_t, i, _a[i]);
                }
            }else if(typeof _a == 'string'){
                if(!this._cb[_t][_a]) return this;
                if(typeof _cb != 'function') return this;
                this._cb[_t][_a] = _cb;
            }
            return this;
        }
        ,action:function(_a, _param){
            if(this['_' + _a]) if(false === this['_' + _a](this._page, _param)) return this;
            this._cb[this._page['type']] && this._cb[this._page['type']][_a].call(this, this._page, _param);
            return this;
        }
        ,openPage:function(url, page){
            if(url.match(/([&\?]p=)\d+/))
                BODY.open(url.replace(/([&\?]p=)\d+/, "\$1" + page));
            else if(this._url.indexOf('?') === -1)
                BODY.open(url + '?p=' + page);
            else
                BODY.open(url + '&p=' + page);
            return this;
        }
        ,_getMaxPage:function(){
            var _t = $('#body .page-main:first li');
            if(_t.length <= 0) return 1;
            return parseInt(_t.eq(_t.length - 2).children('a').html());
        }
        ,_init:function(_p, _url){
            this._page = {
                type:_url.replace(/\?.*$/, '').split('/', 1)[0].toLowerCase()
                ,list:[]
                ,page:1
                ,row:-1
                ,mPage:1
                ,mRow:0
            };
            //hacker for refer
            if(this._page.type == 'refer') this._page.type = 'mail';
            //hacker for home
            if(this._page.type == SYS.home.replace(/^\/*/, '')) this._page.type = 'home';
            if(this._cb[this._page.type]){
                this._page = $.extend(this._page, this._cb[this._page.type]['init'].call(this, this._page));
            }
            this._page.mRow = this._page.list.length;
            this._url = _url;
            return false;
        }
        ,_up:function(p){
            if(p.row > 0){
                p.row --;
                this._page.list.eq(this._page.row).showInWindow();
            }
        }
        ,_down:function(p){
            if(p.row < p.mRow - 1){
                p.row ++;
                this._page.list.eq(this._page.row).showInWindow();
            }
        }
        ,_left:function(p){
            if(p.page > 1){
                this.openPage(this._url, p.page - 1);
            }
        }
        ,_right:function(p){
            if(p.page < p.mPage){
                this.openPage(this._url, p.page + 1);
            }
        }
        ,_quit:function(){
            if(false !== this._cb[this._page.type]['quit'].call(this, this._page))
                hist.back();
            return false;
        }
        ,_jump:function(p){
            var self = this;
            scanner.register(function(str){
                var n = parseInt(str);
                if(!isNaN(n)){
                    if(n < 1) n = 1;
                    else if(n > p.mPage) n = p.mPage;
                    self.openPage(self._url, n);
                }
            }).filter(function(e){
                if(e.keyCode >=48 && e.keyCode <=57)
                    return String.fromCharCode(e.keyCode);
                return '';
            }).start();
        }
    }
    ,hist = {
        //{type:,url:,env:}
        _stack:[]
        ,_back:false
        ,_weight:{
            home:10
            ,fav:9
            ,section:8
            ,sub_section:7
            ,board:6
            ,s:5
            ,article:4
            ,refer:3
            ,mail:3
            ,post:2
        }
        ,_compare:function(t1,t2){
            return this._weight[t1] - this._weight[t2];
        }
        ,_parse:function(url){
            urls = url.replace(/\?.*$/, '').split('/', 3);
            urls[0] = urls[0].toLowerCase();
            if('/' + urls[0] == SYS.home) return 'home';
            if(urls[0] == 'section' && urls[1] && urls[1].length > 1) return 'sub_section';
            if(urls[1] && urls[1] == 'send' || urls[2] && (urls[2] == 'post' || urls[2] == 'reply')) return 'post';
            if(this._weight[urls[0]]) return urls[0];
            return false;
        }
        ,_fill:function(_t){
            var self = this;
            this._stack = [];
            $('#notice_nav a').each(function(i,e){
                var _u = e.href.replace(SYS.protocol+SYS.domain+SYS.base, '').replace(/^\/*/, ''),_tt = self._parse(_u);
                if(_tt === false) return;
                if(self._compare(_tt, _t) > 0)
                   self._stack.push({url:_u,type:_tt,env:{}});
            });
        }
        ,_isPage:function(u1, u2){
            return u1.replace(/\?.*$/, '').toLowerCase() == u2.replace(/\?.*$/, '').toLowerCase();
        }
        ,push:function(url, env){
            if(this._back){
                this._back = false;
                return this;
            }
            var _t = this._parse(url);
            if(_t === false) return;
            var prev = this.top();
            if(prev === null){
                this._fill(_t);
            }else{
                if(this._isPage(prev.url, url)){
                    prev.url = url;
                    prev.env = {};
                    return this;
                }else if(this._compare(_t, prev.type) >= 0){
                    this._fill(_t);
                }
            }
            this._stack.push({url:url,type:_t, env:env || {}});
            return this;
        }
        ,back:function(){
            this._stack.pop();
            var h = this.top();
            if(h){
                this._back = true;
                BODY.open(h.url);
            }
            return;
        }
        ,top:function(){
            if(this._stack.length <= 0)
                return null;
            return this._stack[this._stack.length - 1];
        }
        ,setEnv:function(env){
            var h = this.top();
            if(h) h.env = $.extend(h.env, env || {});
        }
    };

    function _helper(){
        if(this._open && this._open === true) return;
        this._open = true;
        var html = '
        <div class="helper-title">键盘快捷键帮助</div>
        <table class="helper-table">
            <tr>
                <td class="helper-cell">
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">全局</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">w/W:</td>
                            <td class="helper-cell-desc">显示主页</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">依次按e 数字 回车:</td>
                            <td class="helper-cell-desc">跳转到指定数字的分区</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">s/S:</td>
                            <td class="helper-cell-desc">搜索版面</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">v/V:</td>
                            <td class="helper-cell-desc">进入收件箱</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">m/M:</td>
                            <td class="helper-cell-desc">撰写邮件</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">f/F:</td>
                            <td class="helper-cell-desc">进入收藏夹</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">a/A:</td>
                            <td class="helper-cell-desc">进入@我的文章</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">z/Z:</td>
                            <td class="helper-cell-desc">进入回复我的文章</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">u/U:</td>
                            <td class="helper-cell-desc">搜索用户</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">q/Q:</td>
                            <td class="helper-cell-desc">返回上一页或关闭打开的对话框</td>
                        </tr>
                    </table>
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">翻页（跳转）</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">h/l或←/→:</td>
                            <td class="helper-cell-desc">上一页/下一页</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">依次按g 数字 回车:</td>
                            <td class="helper-cell-desc">跳转到指定数字页面</td>
                        </tr>
                    </table>
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">首页</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">j/k/h/l或↑/↓/←/→:</td>
                            <td class="helper-cell-desc">在不同的模块之间切换</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">J/K或SHIFT+↑/↓:</td>
                            <td class="helper-cell-desc">选中模块的上一个/下一个条目</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">r/R或o/O或回车</td>
                            <td class="helper-cell-desc">进入模块中选中的条目</td>
                        </tr>
                    </table>
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">收藏夹</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">j/k或↑/↓:</td>
                            <td class="helper-cell-desc">上一版面/下一版面</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">r/R或o/O或回车</td>
                            <td class="helper-cell-desc">进入分区/版面/目录</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">q/Q:</td>
                            <td class="helper-cell-desc">返回上一层目录</td>
                        </tr>
                    </table>
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">编辑文章/撰写邮件</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">t/T</td>
                            <td class="helper-cell-desc">编辑标题</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">i/I:</td>
                            <td class="helper-cell-desc">编辑内容</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">CTRL+回车:</td>
                            <td class="helper-cell-desc">发表</td>
                        </tr>
                    </table>
                </td>
                <td class="helper-cell">
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">分区</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">j/k或↑/↓:</td>
                            <td class="helper-cell-desc">上一版面/下一版面</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">r/R或o/O或回车</td>
                            <td class="helper-cell-desc">进入版面或子分区</td>
                        </tr>
                    </table>
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">版面</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">j/k或↑/↓:</td>
                            <td class="helper-cell-desc">上一主题/下一主题</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">r/R或o/O或回车</td>
                            <td class="helper-cell-desc">进入主题</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">p/P:</td>
                            <td class="helper-cell-desc">发表话题</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">/:</td>
                            <td class="helper-cell-desc">搜索文章</td>
                        </tr>
                    </table>
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">文章</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">j/k或↑/↓:</td>
                            <td class="helper-cell-desc">上一楼/下一楼</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">p/P:</td>
                            <td class="helper-cell-desc">发表话题</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">i/I:</td>
                            <td class="helper-cell-desc">快捷回复</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">CTRL+回车:</td>
                            <td class="helper-cell-desc">快捷回复发表</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">r/R:</td>
                            <td class="helper-cell-desc">回复当前楼</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">/:</td>
                            <td class="helper-cell-desc">搜索文章</td>
                        </tr>
                    </table>
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">邮箱</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">j/k或↑/↓:</td>
                            <td class="helper-cell-desc">上一封/下一封邮件</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">r/R或o/O或回车</td>
                            <td class="helper-cell-desc">打开邮件</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">m/M:</td>
                            <td class="helper-cell-desc">撰写邮件</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">r/R:</td>
                            <td class="helper-cell-desc">回复打开的邮件</td>
                        </tr>
                    </table>
                    <table class="helper-cell-table">
                        <tr>
                            <td class="helper-cell-key"></td>
                            <td class="helper-cell-desc helper-cell-title">@我/回复我的文章</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">j/k或↑/↓:</td>
                            <td class="helper-cell-desc">上一篇/下一篇@我/回复我的文章</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">r/R或o/O或回车</td>
                            <td class="helper-cell-desc">打开@我/回复我的文章</td>
                        </tr>
                        <tr>
                            <td class="helper-cell-key">r/R:</td>
                            <td class="helper-cell-desc">回复打开的文章</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
'
        ,self = this;
        DIALOG.formDialog(html, {width:'90%', title:'键盘快捷键帮助',close:function(){
            self._open = false;
        }});
    }

    page.init().register('board',{
        init:function(){
            var _p = BODY.get('path').match(/^board\/.*[&\?]p=(\d+)/),_e = hist.top();
            p = {list:$('#body .board-list tbody tr'), page:parseInt(_p && _p[1] || 1), mPage:this._getMaxPage()};
            if(_e && _e.env){
                p.row = _e.env.row;
                p.list.eq(p.row<0?0:p.row).addClass('kb');
            }
            return p;
        }
        ,up:function(p){
            p.list.eq(p.row + 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,down:function(p){
            p.list.eq(p.row - 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,open:function(p){
            hist.setEnv({row:p.row});
            p.list.eq(p.row<0?0:p.row).find('.title_9 a:first').click();
        }
        ,post:function(){
            $('#body .t-pre:first .b-post:first').click();
        }
    }).register('article',{
        init:function(){
            var _p = BODY.get('path').match(/^article\/.*[&\?]p=(\d+)/),_e = hist.top();
            p = {list:$('#body .a-wrap'), page:parseInt(_p && _p[1] || 1), mPage:this._getMaxPage()};
            if(_e && _e.env){
                p.row = _e.env.row;
                p.list.eq(p.row<0?0:p.row).addClass('kb');
            }
            return p;
        }
        ,up:function(p){
            p.list.eq(p.row + 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,down:function(p){
            p.list.eq(p.row - 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,reply:function(p){
            hist.setEnv({row:p.row});
            p.list.eq(p.row<0?0:p.row).find('.a-post:first').click();
        }
        ,post:function(){
            hist.setEnv({row:p.row});
            $('#body .t-pre:first .a-post:first').click();
        }
    }).register('mail',{
        init:function(){
            var _p = BODY.get('path').match(/^(?:mail|refer).*[&\?]p=(\d+)/),_e = hist.top();
            p = {list:$('#body .m-table tbody tr'), page:parseInt(_p && _p[1] || 1), mPage:this._getMaxPage()};
            if(_e && _e.env){
                p.row = _e.env.row;
                p.list.eq(p.row<0?0:p.row).addClass('kb');
            }
            return p;
        }
        ,up:function(p){
            if(DIALOG.getTop() === null){
                p.list.eq(p.row + 1).removeClass('kb');
                p.list.eq(p.row).addClass('kb');
            }
        }
        ,down:function(p){
            if(DIALOG.getTop() === null){
                p.list.eq(p.row - 1).removeClass('kb');
                p.list.eq(p.row).addClass('kb');
            }
        }
        ,open:function(p){
            if(DIALOG.getTop() === null)
                p.list.eq(p.row<0?0:p.row).find('.title_3 a').click();
        }
        ,reply:function(p){
            if(DIALOG.getTop() !== null){
                hist.setEnv({row:p.row});
                DIALOG.getTop().find('.mail-reply:first,.a-post:first').click();
            }
        }
    }).register('fav',{
        init:function(){
            var p = {list:$('#fav_list tr')},_e = hist.top();
            if(_e && _e.env){
                p.row = _e.env.row;
                p.list.eq(p.row<0?0:p.row).addClass('kb');
            }
            return p;
        }
        ,up:function(p){
            p.list.eq(p.row + 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,down:function(p){
            p.list.eq(p.row - 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,open:function(p){
            hist.setEnv({row:p.row});
            p.list.eq(p.row<0?0:p.row).find('.title_1 a').click();
        }
        ,quit:function(){
            $('#fav_up').click();
            return false;
        }
    }).register('section',{
        init:function(){
            var p = {list:$('#body .board-list tbody tr')},_e = hist.top();
            if(_e && _e.env){
                p.row = _e.env.row;
                p.list.eq(p.row<0?0:p.row).addClass('kb');
            }
            return p;
        }
        ,up:function(p){
            p.list.eq(p.row + 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,down:function(p){
            p.list.eq(p.row - 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,open:function(p){
            hist.setEnv({row:p.row});
            p.list.eq(p.row<0?0:p.row).find('.title_1 a:first').click();
        }
    }).register('s',{
        init:function(){
            var _p = this._url.match(/^s\/.*[&\?]p=(\d+)/),_e = hist.top();
            var p = {list:$('#body .board-list tbody tr'), page:parseInt(_p && _p[1] || 1), mPage:this._getMaxPage()};
            if(_e && _e.env){
                p.row = _e.env.row;
                p.list.eq(p.row<0?0:p.row).addClass('kb');
            }
            return p;
        }
        ,up:function(p){
            p.list.eq(p.row + 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,down:function(p){
            p.list.eq(p.row - 1).removeClass('kb');
            p.list.eq(p.row).addClass('kb');
        }
        ,open:function(p){
            hist.setEnv({row:p.row});
            p.list.eq(p.row<0?0:p.row).find('.title_1 a:first,.title_9 a:first').click();
        }
    }).register('home',{
        init:function(){
            var p = {list:$('#columns .widget')}, _e = hist.top();
            p._col = [$('#column1>li').length, $('#column2>li').length, $('#column2>li').length];
            p._sub = -1;
            if(_e && _e.env){
                p.row = _e.env.row;
                p._sub = _e.env._sub;
                p.list.eq(p.row<0?0:p.row).addClass('kb')
                    .find('.w-list-line:visible>li,.w-list-float:visible>li')
                    .eq(p._sub).addClass('kb');
            }
            return p;
        }
        ,up:function(p){
            p.list.eq(p.row + 1).removeClass('kb').removeClass('corner')
                .find('.w-list-line:visible>li,.w-list-float:visible>li').removeClass('kb');
            p.list.eq(p.row).addClass('kb').addClass('corner');
            p._sub = -1;
        }
        ,subUp:function(p){
            if(p._sub > 0){
                var sl = p.list.eq(p.row).find('.w-list-line:visible>li,.w-list-float:visible>li');
                sl.eq(p._sub--).removeClass('kb');
                sl.eq(p._sub).addClass('kb');
            }
        }
        ,down:function(p){
            p.list.eq(p.row - 1).removeClass('kb').removeClass('corner')
                .find('.w-list-line:visible>li,.w-list-float:visible>li').removeClass('kb');
            p.list.eq(p.row).addClass('kb').addClass('corner');
            p._sub = -1;
        }
        ,subDown:function(p){
            var sl = p.list.eq(p.row).find('.w-list-line:visible>li,.w-list-float:visible>li');
            if(p._sub < sl.length - 1){
                sl.eq(p._sub++).removeClass('kb');
                sl.eq(p._sub).addClass('kb');
            }
        }
        ,left:function(p){
            if(p.row < 0) p.row = 0;
            if(p.row >= p._col[0] + p._col[1]){
                p.list.eq(p.row).removeClass('kb').removeClass('corner')
                    .find('.w-list-line:visible>li,.w-list-float:visible>li').removeClass('kb');
                p.row = p.row - p._col[1];
                if(p.row >= p._col[0] + p._col[1]) p.row = p._col[0] + p._col[1] - 1;
                p.list.eq(p.row).addClass('kb').addClass('corner').showInWindow();
                p._sub = -1;
            }else if(p.row >= p._col[0]){
                p.list.eq(p.row).removeClass('kb').removeClass('corner')
                    .find('.w-list-line:visible>li,.w-list-float:visible>li').removeClass('kb');
                p.row = p.row - p._col[0];
                if(p.row >= p._col[0]) p.row = p._col[0] - 1;
                p.list.eq(p.row).addClass('kb').addClass('corner').showInWindow();
                p._sub = -1;
            }
        }
        ,right:function(p){
            if(p.row < 0) p.row = 0;
            if(p.row < p._col[0]){
                p.list.eq(p.row).removeClass('kb').removeClass('corner')
                    .find('.w-list-line:visible>li,.w-list-float:visible>li').removeClass('kb');
                p.row = p.row + p._col[0];
                if(p.row >= p._col[0] + p._col[1]) p.row = p._col[0] + p._col[1] - 1;
                p.list.eq(p.row).addClass('kb').addClass('corner').showInWindow();
                p._sub = -1;
            }else if(p.row < p._col[0] + p._col[1] && p._col[3] != 0){
                p.list.eq(p.row).removeClass('kb').removeClass('corner')
                    .find('.w-list-line:visible>li,.w-list-float:visible>li').removeClass('kb');
                p.row = p.row + p._col[1];
                if(p.row >= p.mRow) p.row = p.mRow  - 1;
                p.list.eq(p.row).addClass('kb').addClass('corner').showInWindow();
                p._sub = -1;
            }
        }
        ,open:function(p){
            hist.setEnv({row:p.row,_sub:p._sub});
            p.list.eq(p.row<0?0:p.row)
                .find('.w-list-line:visible>li,.w-list-float:visible>li')
                .eq(p._sub).find('a').click();
        }
    });

    function _handler(e){
        if(!kb.enable || $(e.target).is('input,textarea,object') || e.ctrlKey || e.altKey || e.metaKey) return;
        //console.log(e.keyCode,String.fromCharCode(e.keyCode), e);
        if(!scanner.scan(e)) return false;
        var k = e.keyCode;
        if(k >= 65 && k <= 90){
            k = String.fromCharCode(!e.shiftKey?(k + 32):k);
        }
        switch(k){
            case 'J': case 'j': case 40:
                if(e.shiftKey) page.action('subDown');
                else page.action('down');
                break;
            case 'K': case 'k': case 38:
                if(e.shiftKey) page.action('subUp');
                else page.action('up');
                break;
            case 'H': case 'h': case 37:
                page.action('left');
                break;
            case 'L': case 'l': case 39:
                page.action('right');
                break;
            case 'R': case 'r':
                page.action('open');
                page.action('reply');
                break;
            case 'O': case 'o': case 13:
                page.action('open');
                break;
            case 'g':
                page.action('jump');
                break;
            case 'Q':case 'q':
                if(DIALOG.getTop() !== null)
                    DIALOG.getTop().dialog('close');
                else
                    page.action('quit');
                break;
            case 'P': case 'p':
                if(DIALOG.getTop() === null)
                    page.action('post');
                break;
            case 'M': case 'm':
                if(DIALOG.getTop() === null)
                    BODY.open('mail/send');
                else
                    $('#u_query_mail').click();
                break;
            case 'T': case 't':
                $('#post_subject').focus();
                break;
            case 'I': case 'i':
                if(DIALOG.getTop() === null)
                    $('#post_content,#quick_text').focus();
                else
                    $('#u_search_u').select();
                break;
            case 'W': case 'w':
                BODY.open(SYS.home);
                break;
            case 'S': case 's':
                _focus = $('#b_search').showInWindow().select();
                break;
            case 'U': case 'u':
                $('#u_query').click();
                break;
            case 'V': case 'v':
                $('#m_inbox').click();
                break;
            case 'A': case 'a':
                $('#m_at').click();
                break;
            case 'Z': case 'z':
                $('#m_reply').click();
                break;
            case 'F': case 'f':
                $('#u_fav').click();
                break;
            case 'E': case 'e':
                scanner.register(function(str){
                    if(str.length == 1);
                        BODY.open('section/' + str);
                }).filter(function(e){
                    if(e.keyCode >=48 && e.keyCode <=57 || e.keyCode >= 65 && e.keyCode <= 90)
                        return String.fromCharCode(e.keyCode);
                    return '';
                }).start();
                break;
            case 191:
                if(e.shiftKey){
                    _helper();
                }else{
                    $('#board_search input:first, #f_search input:first').showInWindow().select();
                }
                break;
            default:
                return true;;
        }
        return false;
    }

    kb.init = function(){
        $('body').on('keydown', _handler);
        BODY.bind("jumped", function(){
            if(_focus){
                _focus.blur();
                _focus = null;
                $(window).focus();
            }
            hist.push(BODY.get('path'));
            page.action('init', BODY.get('path'));
        }, self);
    };
    kb.page = page;
    kb.hist = hist;
    window.KB = kb;
})(jQuery);
