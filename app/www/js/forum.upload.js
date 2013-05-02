$(function(){
    if($('#upload').length <= 0)
        return false;

    var url = BODY.get('path').match(/[^\/]*(\/[^\/]*)(\/[^\/]*)(\/?[^\/?]*)/), bName = url[1] || '', id = url[2] === '/post'?'':(url[3] || '')
    ,maxNum = parseInt($('#upload .upload-max-num').html())
    ,maxSize = parseInt($('#upload .upload-max-size').html())
    ,sessionid = $('#sessionid').val()?('?sid=' + $('#sessionid').val()):'';

    $('#upload .upload-max-size').html($.sizeFormat(maxSize));
    plupload.XWNONE = 218;

    var uploader = {
        plupload: new plupload.Uploader({
            runtimes: $.isMultiFile()?'html5':'flash',
            browse_button: 'upload_select',
            drop_element: 'upload',
            container: 'upload',
            url: SYS.base + '/att' + bName + '/ajax_add' + id + '.json' + sessionid,
            flash_swf_url: SYS.static + SYS.base + '/files/swf/plupload.flash.swf'
        }),
        init:function(collection){
            this.plupload.bind('PostInit', function(up, params){
                if(up.runtime == 'flash') {
                    // under non-IE browsers, FileReference will not pass headers/cookie
                    up.settings.multipart_params = {'cookie' : document.cookie, 'emulate_ajax' : true};
                } else {
                    // HTML5, we use application/octet-stream instead of multipart/form-data
                    up.settings.headers = {'X-Requested-With' : 'XMLHttpRequest'};
                    up.settings.multipart = false;
                }
            });
            this.plupload.init();

            this.plupload.bind('FilesAdded', function(up, files){

                // filter same file
                var names = collection.pluck('name'),fsize = 0,same = [];
                for(var i = 0,f; f = files[i]; i++){
                    f.name = f.name.GBKsubstr(f.name.GBKlength()-60,60).replace(/[ :;|&><*?"'\\\/]/g, '_');
                    if(_.include(names, f.name)){
                        up.removeFile(f);
                        same.push(f);
                        continue;
                    }
                    fsize += f.size;
                }
                files = _.difference(files, same);
                // check num
                if(collection.num() + files.length > maxNum){
                    DIALOG.alertDialog('附件总数超过' + maxNum + ',请重新选择!');
                    _.each(files, up.removeFile, up);
                    return false;
                }

                // check size
                if(collection.size() + fsize > maxSize){
                    DIALOG.alertDialog('附件总大小超过' + $.sizeFormat(maxSize) + ',请重新选择!');
                    _.each(files, up.removeFile, up);
                    return false;
                }
                if(same.length > 0)
                    DIALOG.alertDialog('已自动过滤重名文件!');
                collection.add(files);
                up.refresh();
            });

            this.plupload.bind('UploadProgress', function(up, file) {
                collection.get(file.id).set(file);
            });
            this.plupload.bind('Error', function(up, err) {
                collection.get(file.id)
                    .set({status:plupload.FAILED})
                    .cancel();
                up.refresh();
            });

            this.plupload.bind('FileUploaded', function(up, file, resp) {
                var json = eval('[' + resp.response + ']')[0];
                if(json.ajax_st == 1){
                    var f = collection.get(file.id).set(file);
                    if($('#post_content').length > 0){
                        $('#post_content').get(0).value += '[upload=' + f.get('no') + '][/upload]';
                        if(json.exif != '')
                            $('#post_content').get(0).value += "\n" +
                            json.exif.replace(/\\n/g, "\n");
                    }
                }else{
                    collection.get(file.id)
                        .set({status:plupload.FAILED, err:json.ajax_msg || ''})
                        .cancel();
                    up.refresh();
                }
            });

            this.plupload.bind('QueueChanged', function(up, file){
                $('#upload_upload')[collection.queue().length > 0?'show':'hide']();
            });

            this.plupload.bind('UploadComplete', function(up, files){
                $('#upload_select').removeAttr('disabled')
                    .val('选择文件');
            });
        }
    };

    var FileModel = BaseModel.extend({
        defaults:{
            id:null,
            no:0,
            loaded:0,
            name:'',
            percent:0,
            size:0,
            pos:0,
            err:'',
            status:plupload.XWNONE
        },
        cancel:function(){
            this.collection.remove(this);
            uploader.plupload.removeFile(this);
            this.trigger('destroy');
        },
        'delete':function(){
            this.id = this.get('name');
            this.destroy({url:SYS.base + '/att' + bName + '/ajax_delete' + id + '.json?name=' + encodeURIComponent(this.get('name')) + sessionid.replace(/^\?/, '&')});
        }
    });
    var FilesModel = Backbone.Collection.extend({
        model:FileModel,
        url:function(){
            return SYS.base + '/att' + bName + '/ajax_list' + id + '.json' + sessionid;
        },
        num:function(){
            return this.length;
        },
        size:function(){
            return this.reduce(function(ret, item){
               return ret + item.get('size');
            }, 0);
        },
        updateNo:function(){
            this.each(function(item, k){
                item.set({no:k+1});
            })
        },
        queue:function(){
            return this.filter(function(file){
                return file.get('status') === plupload.QUEUED;
            });
        }
    });
    var FileView = Backbone.View.extend({
        tagName:'tr',
        events: {
            'click a':'click_a'
        },
        tmpl_upload_file:_.template($('#tmpl_upload_file').html()),
        initialize:function() {
            this.model.bind('change', this.render, this);
            this.model.bind('destroy', this.onRemove, this);
        },
        click_a:function(){
            var model = this.model, del = (model.get('status') === plupload.DONE || model.get('status') === plupload.XWNONE);
            DIALOG.confirmDialog('确认' + (del?'删除':'取消') + '附件(' + this.model.get('name') + ')?', function(){
                model[del?'delete':'cancel']();
            });
            return false;
        },
        render:function(){
            $(this.el).html(this.tmpl_upload_file(this.model.toJSON()));
            return this;
        },
        onRemove:function(){
            if(this.model.get('status') === plupload.FAILED || this.model.get('status') < 0)
                $(this.el).hide(3000, this.remove);
            else
                this.remove();
        }
    });

    var ListView = Backbone.View.extend({
        el: '#upload',
        events: {
            'click #upload_upload':'click_upload'
        },
        initialize:function() {
            this.model.bind('add', this.one, this);
            this.model.bind('reset', this.render, this);
            this.model.bind('remove', this.remove, this);
            this.model.bind('all', this.statistic, this);
            this.model.fetch();
        },
        click_upload:function(e){
            this.$('#upload_upload').hide();
            this.$('#upload_select').attr('disabled', 'disabled')
                .val('上传中...');
            uploader.plupload.start();
            e.preventDefault();
        },
        one:function(file, key){
            file.set({no:this.model.indexOf(file)+1}, {slient:true});
            var fv = new FileView({model:file});
            this.$('tbody').append(fv.render().el);
        },
        render:function(){
            this.model.each(this.one, this);
        },
        remove:function(){
            this.model.updateNo();
        },
        statistic:function(){
            this.$('#upload_num_count').html(this.model.num());
            this.$('#upload_size_count').html($.sizeFormat(this.model.size()));
            this.$('#upload_result tbody')[this.model.length > 0?'show':'hide']();
        }
    });

    var Files = new FilesModel();
    var list = new ListView({model:Files});
    uploader.init(Files);
});
