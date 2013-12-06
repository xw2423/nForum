$(function(){
    $('#body form').submit(function(){
        if($('#p_submit').length > 0){
            if($('#p_new1').val() != $('#p_new2').val()){
                DIALOG.alertDialog('两次输入的密码不一样!');
                $('#p_new1').select();
                return false;
            }
        }
        var btn = $(this).find('input[type="submit"]').loading(true);
        $.post($(this).attr('action'), $(this).getPostData(), function(json){
            btn.loading(false);
            DIALOG.ajaxDialog(json);
            SESSION.update(true);
        }, 'json');
        return false;
    });
    if($('.u-img-show').length > 0)
        $('.u-img-show').autoVerMiddle();

    if($('#face_upload_select').length > 0){
        var uploader = new plupload.Uploader({
            runtimes: 'html5,flash',
            browse_button: 'face_upload_select',
            drop_element: 'face_upload',
            container: 'face_upload',
            urlstream_upload: true,
            url: SYS.base + '/user/ajax_face.json',
            flash_swf_url: SYS.static + SYS.base + '/files/swf/plupload.flash.swf',
            filters:[
                {title : "Image files", extensions : "jpg,jpeg,gif,png"}
            ]
        });
        uploader.bind('PostInit', function(up, params){
            if(up.runtime == 'flash') {
                up.settings.multipart_params = {'emulate_ajax':true};
            } else {
                up.settings.headers = {'X-Requested-With':'XMLHttpRequest'};
                up.settings.multipart = false;
            }
        });
        uploader.init();
        uploader.bind('FilesAdded', function(up, files){
            if(files.length > 1){
                DIALOG.alertDialog('请选择一张图片');
                _.each(files, up.removeFile, up);
                return false;
            }
            $('#face_upload_select').attr('disabled','disabled')
                .val('上传中...');
            $('#face_upload_info').html('');
            uploader.start();
        });
        uploader.bind('FileUploaded', function(up, file, resp) {
            var json = eval('[' + resp.response + ']')[0];
            if(json.ajax_st == 1){
                var src = json.img || ''
                    ,w = json.width || ''
                    ,h = json.height || ''
                    ,mw = 120,mh = 120;
                if(w >= h && w > mw){
                    h = Math.ceil(h * mw / w);
                    w = mw;
                }else if(h >= w && h > mh){
                    w = Math.ceil(w * mh / h);
                    h = mh;
                }
                $('#furl').val(src);
                $('#fwidth').val(w);
                $('#fheight').val(h);
                $('#fpreview').attr('src', SYS.base + '/' + src)
                    .width(w)
                    .height(h);
            }else{
                $('#face_upload_info').html(json.ajax_msg || '');
            }
            $('#face_upload_select').removeAttr('disabled')
                .val('选择文件');
            return false;
        });
    }
});
