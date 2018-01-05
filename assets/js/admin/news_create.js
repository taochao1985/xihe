"use strict";
(function(window) {
    var photo = window.photo; 
    var dom = {
        description : $(".description"),
        editor      : null,
        id          : $(".news_id"), 
        title       : $(".title"),
        save_btn    : $(".save_btn")
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

    photo._save_form_data = function(form_data, request_url){

        photo.RequestDataPost({
            request_url : request_url,
            data        : form_data,
            callback_data : function(data){ 
                photo.error_modal({
                    onok : function(){ photo._go_next('/admin/news');}, 
                    msg  : data.msg
                });
            }
        });
    };

    photo._get_form_data = function(){
        var id         = photo._int(dom.id.val()); 
        var description = dom.editor.txt.html();
        var title      = photo._trim(dom.title.val()); 

        if( title == "" ){
            dom.title.focus().parents(".item").addClass('has-error');
            return false;
        }else{
            dom.title.parents(".item").removeClass('has-error');
        }
 
        var form_data = {
            id          : id, 
            description : description,
            title       : title
        }; 

        var request_url = "/admin/news/store";
        if( id > 0 ){
            request_url = "/admin/news/update";
        }
        photo._save_form_data(form_data, request_url);
    };

    photo._init_editor();

    dom.save_btn.on('click',photo._get_form_data);
})(window)