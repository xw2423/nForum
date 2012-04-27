/* jquery extention */
String.prototype.GBKlength = function(){
    var ret=0;
    for(var i=0;i<this.length;i++){
        if(this.charCodeAt(i)>=0&&this.charCodeAt(i)<=255)
            ret++;
        else
            ret+=2;
    }
    return ret;
};
String.prototype.GBKsubstr = function(start,length){
    if(start<0) return this;
    var ret="", len=0, processed=0, flag=0;
    for(var i=0;i<this.length;i++){
        if(!flag){
            if(len==start||len==start+1)
                flag=1;
        }
        if(this.charCodeAt(i)>=0&&this.charCodeAt(i)<=255){
            len++;
            if(flag){
                ret+=this.charAt(i);
                processed+=1;
            }
        }else{
            len+=2;
            if(flag){
                ret+=this.charAt(i);
                processed+=2;
            }
        }
        if(processed==length||processed==length+1)
            return ret;
    }
    return ret;
};
$.ajaxSetup({
    timeout: 20000
});
$.extend({
    random:function(){
        return this.now();
    },
    isIE:function(ver){
        if(ver){
            if(typeof this['_ie'+ver] === "undefined")
                this['_ie'+ver] = (!!$.browser.msie && $.browser.version == ver+'.0');
            return this['_ie'+ver];
        }else{
            (typeof this._ie === "undefined") && (this._ie = !!$.browser.msie);
            return this._ie;
        }
    },
    isMultiFile:function(){
        if(typeof this._mf === 'undefined'){
            this._mf = (!!$.browser.webkit
                && parseFloat($.browser.version) > 535
                || !!$.browser.mozilla
                && parseFloat($.browser.version) >= 1.9
            );
        }
        return this._mf;
    },
    isTop:function(){
        return (window.top == window.self);
    },
    setTitle:function(title){
        if($.isIE())
            window.origTitle = title;
        document.title = title;
    },
    sizeFormat:function(num){
        var sizes = [
            ['TB',1024 * 1024 * 1024 * 1024],
            ['GB',1024 * 1024 * 1024],
            ['MB',1024 * 1024],
            ['KB',1024]
        ];
        if(num < 1024)
            return num + 'B';
        for(var i = 0,size;size = sizes[i];i++){
            if(num >= size[1]){
                num = Math.round(num / size[1]) + size[0];
                break;
            }
        }
        return num;
    }
});
$.fn.extend({
    adjust:function(min){
        return this.each(function(i,e){
            var e = $(e),ow = e.data('o_w'),cw = e.width();
            if(cw > min){
                if(!ow) e.data('o_w', cw);
                e.width(min);
            }else{
                if(ow) e.width(ow > min?min:ow);
            }
        });
    },
    autoVerMiddle:function(){
        this.wrapInner('<div class="auto-ver-middle" />');
        this.css({'height':this.height()}).append('<div class="auto-ver-middle-max" />');
        return this;
    },
    getPostData:function(){
        var data = {};
        this.find('input,textarea,select')
            .add(this.filter('input,textarea,select'))
            .each(function(i){
                if($(this).attr('name')){
                    if($(this).is('input[type="checkbox"],input[type="radio"]')){
                        if($(this).is(':checked'))
                            data[$(this).attr('name')] = $(this).val();
                    }else
                        data[$(this).attr('name')] = $(this).val();
                }
           });
        return data;
    },
    loading:function(enable){
        var v = this.data('v');
        if(enable){
            if(!v) this.data('v', this.val());
            this.val(SYS.code.COM_LOADING)
                .attr('disabled', 'disabled');
        }else{
            if(v) this.val(v);
            this.removeAttr('disabled');
        }
        return this;
    }
});
/* jquery extention */

/* front base */
(function(){
    var BaseModel = Backbone.Model.extend({
        defaults : {
            ajax_st : 0,
            ajax_code : 0,
            ajax_msg : ''
        },
        ajaxOK:function(){
            return (this.get('ajax_st') == 1);
        }
    });
    var DialogModel = BaseModel.extend({
        ICO_ALERT: 'ui-state-error',
        ICO_INFO: 'ui-state-highlight',
        containers:[],
        getContainer:function(){
            var t;
            for(var i = 0;t = this.containers[i];i++)
                //clear nforum namespace event
                if (!t.dialog('isOpen')) return t.off('.nforum').off('img');
            t = $('<div />').appendTo($('body')).dialog({
                modal: true,
                autoOpen: false
            });
            this.containers.push(t);
            return t;
        },
        getTop:function(){
            for(var i = this.containers.length - 1;t = this.containers[i]; i--)
                if(t.dialog('isOpen')) return t;
            return null;
        },
        updateTop:function(content){
            var d = this.getTop(), p = d.html(content).parent()
                , t = $(window).height()-p.height();
            p.css('top', $(window).scrollTop() + (t>=0?t/2:0))
            .find('img').one('load', function(){
                if($(this).width() > d.width())
                    $(this).width(d.width() - 20);
                var t = $(window).height()-p.height();
                p.css('top', $(window).scrollTop() + (t>=0?t/2:0));
            });
        },
        open:function(content, option){
            option = option || {};
            var d = this.getContainer().html(content)
            .removeClass(this.ICO_ALERT + ' ' +this.ICO_INFO)
            .dialog('option',{close:function(){}})
            .dialog('option',option)
            .dialog('open');

            d.find('img').one('load', function(){
                if($(this).width() > d.width())
                    $(this).width(d.width() - 20);
                var p = d.parent(), t = $(window).height()-p.height();
                p.css('top', $(window).scrollTop() + (t>=0?t/2:0));
            });
            return d;
        },
        close:function(index){
            if(index)
                this.containers[index] && this.containers[index].dialog('close');
            else
                _.each(this.containers,function(el){el.dialog('close')});
        },
        alertDialog:function(el, ico, option){
            ico = ico || this.ICO_ALERT;
            option = _.extend({
                title: SYS.code.COM_TITLE,
                width:250,
                buttons:[
                    {text:SYS.code.COM_OK,click:function(){$(this).dialog('close');}}
                ]}, option || {}) ;
            return this.open(el, option)
            .addClass(ico)
            .prepend('<span class="ui-icon ui-icon-alert" style="float: left;margin-right:.3em;margin-bottom:-10px" />')
            .css({'min-height':50})
            .autoVerMiddle();
        },
        confirmDialog:function(el, callback, option){
            option = _.extend({
                title: SYS.code.COM_COMFIRM,
                width:250,
                buttons:[
                    {text:SYS.code.COM_OK,click:function(){if(_.isFunction(callback))callback.apply();$(this).dialog('close');}},
                    {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
                ]}, option || {}) ;
            return this.open(el, option)
            .removeClass(this.ICO_ALERT + ' ' +this.ICO_INFO)
            .addClass(this.ICO_INFO)
            .prepend('<span class="ui-icon ui-icon-alert" style="float: left;margin-right:.3em;margin-bottom:-10px" />')
            .css({'min-height':50})
            .autoVerMiddle();
        },
        formDialog:function(el, option){
            option = _.extend({
                title: SYS.code.COM_TITLE,
                width:350,
                buttons:[
                    {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
                ]}, option || {});
            return this.open(el, option)
        },
        ajaxDialog:function(repo, buttons){
            this.set(repo);
            var _prevent_default = false
                ,text = repo.ajax_msg
                ,opt = {width:400};
            buttons = _.reduce(repo.list || [], function(res, item){
                res.push({text:item.text.substr(0,25),click:function(){BODY.open(item.url);_prevent_default=true;$(this).dialog('close')}});
                return res;
            }, buttons || []);
            if(!_.isEmpty(buttons)){
                text += ',' + SYS.code.COM_REDIRECT;
                opt['buttons'] = buttons;
            }
            if(repo['default']){
                opt['close'] = function(){
                    if(!_prevent_default){
                        _prevent_default = true;
                        BODY.open(repo['default']);
                    }
                };
                setTimeout(function(){
                    !_prevent_default && DIALOG.getTop().dialog('close');
                }, SYS.redirect * 1000);
            }
            return this.alertDialog(text, this.ajaxOK()?this.ICO_INFO:this.ICO_ALERT, opt);
        }
    });
    var UserModel = BaseModel.extend({
        defaults : {
            id : '',
            user_name : false,
            face_url:'',
            face_width:0,
            face_height:0,
            gender : 'n',
            astro : '',
            life : 0,
            qq:'',
            msn:'',
            home_page:'',
            level : '',
            is_login : false,
            is_online : false,
            post_count : 0,
            last_login_time : 0,
            last_login_ip : '0.0.0.0',
            is_hide : false,
            is_register : false
        },
        url:function(){
            return SYS.ajax.user + '/' + this.id + '.json';
        }
    });
    var SessionModel = UserModel.extend({
        timeOut: SYS.session.timeout * 1000,
        updateTime:0,
        defaults : {
            new_mail : false,
            full_mail : false,
            is_login : false,
            is_admin : false,
            first_login_time: 0,
            login_count : 0
        },
        url:function(){
            return SYS.ajax.session
        },
        update:function(force){
            var now = $.now();
            if(force || this.get('is_login') && (now - this.updateTime > this.timeOut)){
                this.fetch();
                this.updateTime = now;
            }
        },
        //session will trigger 'login' event
        login:function(form){
            var self = this;
            $.post(SYS.ajax.login,form.getPostData(),function(json){
                //login will return session data, so force update here
                self.set(json);
                self.updateTime = $.now();
                if(self.ajaxOK()) self.trigger('login');
                else {self.trigger('logerror');DIALOG.ajaxDialog(json);}
            }, 'json');
        },
        //session will trigger 'logout' event
        logout:function(){
            var self = this;
            this.fetch({url:SYS.ajax.logout,success:function(){
                self.update(true);
                self.trigger('logout');
            }});
        }
    });
    var BodyModel = Backbone.Model.extend({
        defaults : {
            html:'',
            path:''
        },
        //open url of element 'a', open inside
        open:function(url, target){
            if(_.isElement(url)){
                var base = SYS.protocol + SYS.domain + (location.port == ''?'':(':'+location.port));
                target = url.target;
                url = url.href.replace(base,'');
            }
            target = target || '_self';
            if(!url) return;

            //outside link open directly
            if(target != '_self'){
                window.open(url, target || '_blank');
                return;
            }

            url = url.replace(new RegExp('^/*(' + SYS.base.substr(1) + '/?)?'), '');
            if(url.match(/^javascript/)){
                eval(url.replace(/^javascript:/,''));
                return;
            }
            if(url.match(/^(https|http|ftp|rtsp|mms):\/\//i)){
                window.open(url, target || '_blank');
                return;
            }

            //parse for 'href="?xx&xx"'
            if(url.match(/^(\?|%3F).*$/i))
                url = window.location.hash.replace(/(\?|%3F).*$/i,'') + url;
            else if(url.match(/^[\w\d]/))
                url = '#!' + url;
            else
                return;

            if(url == '#!' + this.get('path'))
                //if path no change refresh
                this.refresh();
            else
                window.open(url, '_self');
        },
        //do not use jump, using function open
        jump:function(path){
            var self = this,
            handler = function(repo){
                repo = _.isString(repo)?repo:repo.responseText;
                if(repo.match(/^location:.*$/i))
                    window.location.hash = '!'+ repo.replace(/^location:\/?/i, '');
                else{
                    self.set({html:repo,path:path},{silent: true});
                    self.change();
                    self.trigger('jumped');
                }
            };
            this.trigger('jump');
            $.get(path, $.isIE()?{_t:$.random()}:{}).success(handler).error(handler);
        },
        refresh:function(){
            this.jump(this.get('path'));
        }
    });
    var AppView = Backbone.View.extend({
        el:'body',
        session : null,
        body:null,
        events: {
           'click #u_login_submit' : 'click_submit',
           'click #u_login_reg' : 'click_reg',
           'click #u_login_out' : 'click_out',
           'keydown #b_search' : 'keydown_search',
           'click #u_query_search' : 'click_u_search',
           'click #u_query_mail' : 'click_u_mail',
           'click #u_query_add' : 'click_u_add',
           'click #left_line samp' : 'click_hide_left',
           'click a' : 'click_a'
        },

        initialize:function(session, body) {
            var self = this;

            //init template
            this.$('script[type="text/template"]').each(function(){
               self[this.id] = _.template($(this).html());
            });

            //init body
            this.body = body;
            this.body.bind("change", this.onBodyUpdate, this);
            this.body.bind("jump", this.onBodyJump, this);
            this.body.bind("jumped", this.onBodyJumped, this);

            //init session
            this.session = session;
            this.session.bind("change", this.onSessionUpdate, this);
            this.session.bind("login", this.onLogin, this);
            this.session.bind("logerror", this.onLogerror, this);
            this.session.bind("logout", this.onLogout, this);

            //hide btn
            this.$('#left_line samp').data('_show', 1);
            if($.cookie("left-show") == 0)
                this.$('#left_line samp').click();
        },
        click_submit:function(){
            if($.trim($('#u_login_id').val()) == '')
                DIALOG.alertDialog(SYS.code.MSG_USER);
            else if($('#u_login_passwd').val() == '')
                DIALOG.alertDialog(SYS.code.MSG_PWD);
            else{
                this.tips(true);
                this.session.login($('#u_login_form'));
            }
            return false;
        },
        click_out:function(e){
            this.tips(true);
            this.session.logout();
            return false;
        },
        click_reg:function(){
            this.body.open('/reg');
        },
        keydown_search:function(e){
            if(e.keyCode == 13){
                var url = '/s/board?b=' + encodeURIComponent(encodeURIComponent($('#b_search').val()));
                this.body.open(url);
            }
        },
        click_a:function(e){
            if(e.currentTarget.href && 0 === e.currentTarget.href.indexOf(SYS.protocol + SYS.domain + SYS.base + '/' + SYS.ajax.user))
                this.userQuery(e.currentTarget);
            else
                this.body.open(e.currentTarget);
            return false;
        },
        click_u_search:function(){
            var t = DIALOG.getTop(),u;
            if(!t) return false;
            if('' != (u = this.$('#u_search_u').val())){
                this.tips(true);
                user = new UserModel({id:u});
                user.bind('change', this._onUserQuery, this).fetch();
            }
            return false;
        },
        click_u_mail:function(){
            DIALOG.close();
        },
        click_u_add:function(e){
            var id = DIALOG.getTop().find('.u-name span').html();
            $.post(SYS.base + '/friend/ajax_add.json', {id:id}, function(json){
                delete json['default'];
                delete json['list'];
                DIALOG.ajaxDialog(json);
            }, 'json');
            return false;
        },
        click_hide_left:function(e){
            var self = $(e.currentTarget), s = !self.data('_show');
            self.toggleClass('ico-pos-show', 'ico-pos-hide').data("_show", s?1:0).css('right',s?-7:-12);
            $('#menu').width(s?156:0).children(':not(#left_line)')[s?"show":"hide"]().end().next().css("margin-left", s?162:3);
            $.cookie("left-show", s?1:0,{path:'/', domain:SYS.cookie_domain,expires:30});
        },
        onBodyJump:function(){
            this.tips(true);
        },
        onBodyJumped:function(){
            this.tips(false);
            this.session.update();
        },
        onLogin:function(){
            SYS.clear();
            this.body.refresh();
        },
        onLogerror:function(){
            this.tips(false);
        },
        onLogout:function(){
            SYS.clear();
            this.body.refresh();
        },
        onSessionUpdate:function(){
            if(!this.session.ajaxOK())
                return;
            var uid = this.session.get('id'), is_login = this.session.get('is_login');
            $('#u_login').html(this[is_login?'tmpl_u_login_info':'tmpl_u_login'].call(this,this.session.toJSON()));
            if(this.session.hasChanged('id')){
                this.renderTree();
                SYS.clear();
                this.tips(false);
            }

            //update bottom number
            $('#bot_info .c-total').html(this.session.get('forum_total_count'));
            $('#bot_info .c-user').html(this.session.get('forum_user_count'));
            $('#bot_info .c-guest').html(this.session.get('forum_guest_count'));
        },
        renderTree:function(){
            var uid = this.session.get('id'), is_login = this.session.get('is_login');
            $('#xlist').html(this.tmpl_left_nav.call(this,this.session.toJSON()));
            $('#xlist .slist').SimpleTree({autoclose: true, persist:true, cookie:"left-index", ajax:SYS.base + "/" +SYS.ajax.section_list + "?uid=" + uid  + "&root=%id%"});
            $('#xlist .clist').SimpleTree({autoclose: true});
            if(is_login)
                $('.flist').SimpleTree({autoclose: true,ajax:SYS.base + "/" + SYS.ajax.favor_list + "?uid=" + uid + "&root=%id%"});
            this.deserialize();
            $("#xlist .x-child").parent().find('>span .toggler').click(this.serialize);
            /* fix for first level menu */
            $("#xlist .x-folder").click(function(e){
                if(e.target != $(this).children('.toggler').get(0))
                    $(this).children('.toggler').click();
                return false;
            });
            var self = this;
            $("#xlist .x-leaf").not('.x-search').click(function(e){
                self.body.open($(this).children('a').get(0));
                return false;
            });
            /* fix end */
            $('#b_search').placeholder();
        },
        serialize:function(){
            var data = [];
            $('#xlist .x-child').each(function(i, e){data[i] = $(e).is(':visible')?1:0});
            $.cookie("nforum-left",data.join(""), {path:'/', domain:SYS.cookie_domain,expires: 30});
        },
        deserialize:function(){
            var data = $.cookie("nforum-left") || "100";
            data = data.split("");
            $('#xlist .x-child').each(function(i, e){
                (data[i] == "1") && $(e).parent().find('>span .toggler').click();
            });
        },
        onBodyUpdate:function(){
            $(window).scrollTop(0);
            $('#body').off().html(this.body.get('html'))
                .find('.a-swf').each(function(){
                    $(this).empty().flashembed({
                        src:$(this).attr('_src')
                        ,width:560
                        ,height:420
                        ,wmode: 'opaque'
                        ,allowscriptaccess:'never'
                        });
                }).end()
                .find('.resizeable').each(function(){
                    $(this).load(function(){
                        $(this).adjust($('body').width() - 410);
                    });
                    $.isIE() && (this.src = this.src);
                });
            //parse the last '#' to jump in page
            var name = this.body.get('path');
            if(name = name.match(/#(.*)$/)){
                var el = this.$('a[name="' + name[1] + '"]');
                if(el.length > 0)
                    $(window).scrollTop(el.position().top);
            }
        },
        tips:function(show){
            this.$('#nforum_tips')[!!show?'show':'hide']();
        },
        userQuery:function(el){
            var re = new RegExp(SYS.ajax.user + '/?([\\w\\d]*)$')
                , u, user;
            u = el.href.match(re);
            user = SYS.cache('user_' + u[1] || '');
            var d = DIALOG.formDialog(this.tmpl_user(user || {id:null}), {width:600});
            if(u[1] != '' && !user){
                this.tips(true);
                user = new UserModel({id:u[1]});
                user.bind('change', this._onUserQuery, this).fetch();
            }
            return false;
        },
        //only for user query,do not call by AppView
        _onUserQuery:function(user){
            if(!user.ajaxOK()){
                DIALOG.ajaxDialog(user.toJSON());
            }else{
                var t = new Date();
                t.setTime(user.get('first_login_time') * 1000);
                user.set({first_login_time:t.toLocaleString()},{silent:true});
                t.setTime(user.get('last_login_time') * 1000);
                user.set({last_login_time:t.toLocaleString()},{silent:true});
                var dd = user.toJSON();
                dd.session_login = this.session.get('is_login');
                dd.session_id = SESSION.get('id');
                dd.session_is_admin = SESSION.get('is_admin');
                SYS.cache('user_' + dd.id, dd);
                DIALOG.updateTop(this.tmpl_user(dd));
            }
            this.tips(false);
            delete user;
        }
    });
    var MainRouter = Backbone.Router.extend({

        body:null,

        initialize:function(body) {
            this.body = body;
        },
        routes: {
            "!*path":"handler"
            ,"*other":"err_handler"
        },
        handler:function(path) {
            if(path.match(/^[\w\d]/))
                this.body.jump(path);
            else
                this.body.jump('404');
        },
        err_handler:function(other) {
            this.body.jump(SYS.home.substr(1));
        }
    });

    //only Base&User can be new outside
    window.BaseModel = BaseModel;
    window.UserModel = UserModel;

    //system app init
    window.DIALOG = new DialogModel();

    //init front object
    window.front_init = function(){
        window.SESSION = new SessionModel();
        window.BODY = new BodyModel();
        window.ROUTER = new MainRouter(BODY);
        window.APP = new AppView(SESSION, BODY);
        SESSION.update(true);
    };

    //add jquery-alertDialog extension
    if(jQuery){
        jQuery.fn.alertDialog = function(el, ico, option){
            var self = this;
            option = option || {};
            option.close = function(){
                self.focus();
            };
            DIALOG.alertDialog(el, ico, option);
            return this;
        };
    }
})();
/* front base end */

$(window).resize(function(e){
    if($(window).width() <= SYS.mWidth)
        $('body').width(SYS.mWidth);
    else
        $('body').width("100%");
    $('.resizeable').adjust($('body').width() - 410);

    e.stopPropagation();
    return false;
}).resize();
if($.isIE()){
    $('.input-text,textarea').focus(function(){
        $(this).addClass("ie-input-focus");
    }).blur(function(){
        $(this).removeClass("ie-input-focus");
    });
}
if(!$.isTop()&& !SYS.iframe
    && !window.location.pathname.match(SYS.allowFrame)){
    window.top.location.href = window.location.href;
}

function front_startup(){
    //front_init
    window.front_init();

    if($.isIE()){
        document.onpropertychange = function(){
            if(window.event.propertyName == 'title' && document.title != window.origTitle)
                setTimeout(function(){document.title = window.origTitle},1);
        };
    }
    if($.isIE(6)){
        document.execCommand("BackgroundImageCache", false, true);
    }
    //load flash
    $('#ban_ner_border').flashembed({
        src:SYS.static + SYS.base + "/files//swf/adv.swf"
        ,width:600
        ,height:80
        ,wmode: 'opaque'
    },{
        borderwidth:600
        ,borderheight:80
        ,links:SYS.adv.links
        ,pics:SYS.adv.pics
    });

    //parse hash & load body
    $(function(){
        Backbone.history.start({pushState: false});
    });
}
