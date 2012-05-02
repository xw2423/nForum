$(function(){
var WidgetModel = BaseModel.extend({
    defaults:{
        wid:0,
        title:'',
        p:''
    }
});
var WidgetList = Backbone.Collection.extend({
    type:'',
    model:WidgetModel,
    setWidgets:function(type){
        this.type = type;
        this.fetch();
    },
    url:function(){
        var data = this.type.split('-');
        if(data.length != 2) return '';
        return ['widget/list.json?t=',data[0],'&tt=',data[1],'&_t=',$.random()].join('');
    }
});
var WidgetAppView = Backbone.View.extend({
    el:'#body',
    events: {
       'click .tab-normal' : 'click_tab',
       'click #widget_add .submit' : 'click_submit'
    },
    initialize: function() {
        var self = this;
        this.$('script[type="text/template"]').each(function(){
           self[this.id] = _.template($(this).html());
        });
        this.model.bind('reset', this.onWidgetUpdate, this);
    },
    click_tab: function(e){
        var self = $(e.target);
        if(self.hasClass('tab-down')) return false;
        self.addClass('tab-down').siblings().removeClass('tab-down');
        this.model.setWidgets(e.target.id.slice(e.target.id.indexOf('_')+1));
    },
    click_submit: function(e){
        var el = $(e.target).parent().parent(),
            wid = el.attr('id');
        DIALOG.formDialog(this.tmpl_app_add({wid:wid,title:el.attr('title')}),
            {width:300,title:'添加新应用',
             buttons:[
                {text:SYS.code.COM_OK,click:function(){
                    var data = $('#widget_add_form').getPostData();
                    data._t = $.random();
                    $.getJSON($('#widget_add_form').attr('action'), data, function(repo){
                        if(repo.ajax_st == 1){
                            $('#' + wid + ' .submit').val("已添加至首页")
                                .attr('disabled', true);
                        }else{
                            DIALOG.ajaxDialog(repo);
                        }
                    });
                    $(this).dialog('close');
                }},
                {text:SYS.code.COM_CANCAL,click:function(){$(this).dialog('close');}}
            ]}
        );
    },
    onWidgetUpdate:function(){
        var self = this,
        html = this.model.reduce(function(html,widget){
            html += self.tmpl_app(widget.toJSON());
            return html;
        },''); 
        this.$('#widget_add').empty().append(html);
    }
});
var widgets = new WidgetList();
var widgetApp = new WidgetAppView({model:widgets});
$('#body .tab-normal:first').click();
});
