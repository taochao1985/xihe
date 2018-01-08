"use strict";
(function(window) {
    var photo = window.photo; 
    var dom = {
        fileupload  : $(".fileupload"),
        description : $(".description"),
        editor      : null,
        save_btn    : $(".save_btn"),
        preview_btn : $(".save_btn_preview"),
        video_input : $(".hidden_video_input"),
        audio_input : $(".hidden_audio_input"),
        image_input : $(".hidden_image_input"),
        start_time  : $(".start_time"),
        id          : $(".lession_id"),
        lt_id       : $(".lt_id"),
        title       : $(".title"),
        current_time: $(".current_time"),
        preview_flag: 0
    };

    photo._init_editor = function(){
        var E = window.wangEditor;
        dom.editor = new E('#description');
        dom.editor.customConfig.uploadImgServer = '/admin/fileupload/mluti_upload';
        dom.editor.customConfig.uploadFileName = 'userfile[]';
        dom.editor.customConfig.uploadImgMaxLength = 50;
        dom.editor.customConfig.uploadImgHooks = {
            customInsert: function (insertImg, result, editor) { 
                var result = result.data;
                for(var i = 0 ; i < result.final_data.length; i++){
                    insertImg(photo.base_url+result.final_path+result.final_data[i].file_name); 
                }
            }
        };
        dom.editor.create()
    };

    photo._file_uploaded = function(data, target, file_type, item_input){
        if( data.errno == 0 ){
            var data      = data.data;
            var file_path = data.final_path+data.file_name;
            if( file_type == 'images' ){
                target.css('margin-top','0').html("<img src='"+file_path+"' class='venobox' href='"+file_path+"' />");
                $(".venobox").venobox();
            }else{
                target.html(data.client_name);
            }
            item_input.val(file_path);
        }else{
            target.addClass(".text-danger").html(data.msg);
        }
    };

    dom.fileupload.each(function(){
        photo._init_fileupload({
            target          : $(this),
            file_type       : $(this).attr('file_type'),
            upload_process  : $(this).parents(".upload_item").find(".progress"),
            name_area       : $(this).parents(".upload_item").find(".name_area"),
            target_input    : $(this).next('input')
        });
    });

    photo._save_form_data = function(form_data, request_url){

        photo.RequestDataPost({
            request_url : request_url,
            data        : form_data,
            callback_data : function(data){ 
                photo.error_modal({
                    onok : function(){
                        if ( dom.preview_flag ){
                            window.open('/admin/lessions/preview/' + data.id);
                        }else{
                            photo._go_next('/admin/lessions');
                        }
                    }, 
                    msg  : data.msg
                });
            }
        });
    };

    photo._get_form_data = function(){
        var id         = photo._int(dom.id.val());
        var video_path = photo._trim(dom.video_input.val());
        var video_name = photo._trim(dom.video_input.parents('.upload_item').find('.name_area').html());
        var audio_path = photo._trim(dom.audio_input.val());
        var audio_name = photo._trim(dom.audio_input.parents('.upload_item').find('.name_area').html());
        var image_path = photo._trim(dom.image_input.val());
        var start_time = dom.start_time.val();
        var description = dom.editor.txt.html();
        var title      = photo._trim(dom.title.val());
        var lt_id      = photo._int(dom.lt_id.val());

        if( title == "" ){
            dom.title.focus().parents(".item").addClass('has-error');
            return false;
        }else{
            dom.title.parents(".item").removeClass('has-error');
        }

        if( image_path == ""){
            photo.error_modal({
                    msg  : "请上传封面图片"
                }); 
            return false;       
        }
        var form_data = {
            id          : id,
            video_path  : video_path,
            audio_path  : audio_path,
            image_path  : image_path,
            video_name  : video_name,
            audio_name  : audio_name,
            description : description,
            start_time  : start_time,
            lt_id       : lt_id,
            title       : title
        }; 

        var request_url = "/admin/lessions/store";
        if( id > 0 ){
            request_url = "/admin/lessions/update";
        }
        photo._save_form_data(form_data, request_url);
    };

    photo._get_form_data_preview = function(){
        dom.preview_flag = 1;
        photo._get_form_data();
    };

    dom.start_time.datetimepicker({
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 1,
        minView: 4,
        forceParse: 0,
        startDate:dom.current_time.val(),
        format: 'yyyy-mm-dd hh:ii:ss'
    });

    photo._init_editor();

    dom.preview_btn.on('click',photo._get_form_data_preview);
    dom.save_btn.on('click',photo._get_form_data);
})(window)