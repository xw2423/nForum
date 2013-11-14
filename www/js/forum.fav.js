$(function(){
    var FavModel = BaseModel.extend({
        defaults : {
            bid:0,
            name:'',
            manager:'',
            description:0,
            position:-218,
            post_today_count:0,
            post_threads_count:0,
            post_all_count:0,
            user_online_count:0
        }
    });
    var FavCollection = Backbone.Collection.extend({
        level:[0],
        model:FavModel,
        setLevel:function(level){
            this.level.push(level);
            this.fetch();
        },
        getLevel:function(){
            return this.level[this.level.length-1];
        },
        parent:function(){
            if(this.level.length > 1){
                this.level.pop();
                this.fetch();
            }
        },
        url:function(){
            return ['fav/',this.level[this.level.length-1],'.json?_t=',$.random()].join('');
        }
    });
    var FavAppView = Backbone.View.extend({
        el:'#body',
        events: {
           'click #fav_update' : 'click_update',
           'click #fav_up' : 'click_up',
           'click #fav_list .fav-del' : 'click_delete',
           'click #fav_list .fav-link' : 'click_link',
           'click #fav_ab_btn' : 'click_ab',
           'click #fav_ad_btn' : 'click_ad',
           'keydown #fav_add .input-text' : 'keydown_input'
        },
        tmpl_fav: _.template($('#tmpl_fav').html()),
        initialize: function() {
            this.model.bind('reset', this.onFavUpdate, this);
            this.model.fetch();
        },
        click_update:function(){
            this.model.fetch();
        },
        click_up:function(){
            this.model.parent();
        },
        click_delete:function(e){
            var el = $(e.currentTarget)
            ,url = SYS.base + "/fav/op/" + this.model.getLevel() + '.json'
            ,data = {ac:el.attr('_ac'), v:el.attr('_npos')}
            ,m = this.model;
            DIALOG.confirmDialog('确认删除此' + (el.attr('_ac') == 'dd'?'目录':'版面') + '?', function(){
                $.post(url, data, function(json){
                    DIALOG.ajaxDialog(json);
                    if(json.ajax_st == 1){
                        m.fetch();
                        APP.renderTree();
                    }
                }, "json");
            });
        },
        click_link:function(e){
            var l = $(e.currentTarget).parent().parent().attr('id').split('_');
            this.model.setLevel(l[2]);
            return false;
        },
        click_ab:function(){
            var url = SYS.base + "/fav/op/" + this.model.getLevel() + '.json'
            ,data = {ac:'ab', v:$('#fav_ab_txt').val()}
            ,m = this.model;
            $.post(url, data, function(json){
                DIALOG.ajaxDialog(json);
                if(json.ajax_st == 1){
                    $('#fav_ab_txt').val('');
                    m.fetch();
                    APP.renderTree();
                }
            }, "json");
        },
        click_ad:function(){
            var url = SYS.base + "/fav/op/" + this.model.getLevel() + '.json'
            ,data = {ac:'ad', v:$('#fav_ad_txt').val()}
            ,m = this.model;
            $.post(url, data, function(json){
                DIALOG.ajaxDialog(json);
                if(json.ajax_st == 1){
                    $('#fav_ad_txt').val('');
                    m.fetch();
                    APP.renderTree();
                }
            }, "json");
        },
        keydown_input:function(e){
            if(e.keyCode == 13) $(e.currentTarget).next().click();
        },
        mouseover_tr:function(e){
            $(e.currentTarget).addClass("mouseover");
        },
        mouseout_tr:function(e){
            $(e.currentTarget).removeClass("mouseover");
        },
        onFavUpdate:function(){
            var self = this,
            html = this.model.reduce(function(html,fav){
                html += self.tmpl_fav(fav.toJSON());
                return html;
            },'');

            this.$('#fav_list').empty().append(html)
            .find('tr:odd td').addClass('bg-odd');
            window.KB.page.action('init', BODY.get('path'));
        }
    });
    var favs = new FavCollection();
    var favApp = new FavAppView({model:favs});
});
