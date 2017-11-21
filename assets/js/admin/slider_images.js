"use strict";
(function(window) {
    var photo = window.photo; 
    var dom = {
        form_save  : $(".lession_type_save"),
        relate_url : $(".relate_url"),
        id         : $(".lession_type_id"),
        modal      : $(".lession_type_modal"),
        edit_btn   : $(".edit_btn"),
        delete_btn : $(".delete_btn"),
        fileupload : $(".fileupload"),
        image_path : $(".hidden_image_input"),
        venobox    : $(".venobox")
    };
    photo._btn_click = function(){
        var id   = photo._int(dom.id.val());
        var relate_url = photo._trim(dom.relate_url.val());
        var image_path = photo._trim(dom.image_path.val());

        if ( image_path == ""){
            dom.image_path.parents(".form-group").addClass('has-error');
            return false;
        }

        var form_data = {
            id   : id,
            relate_url : relate_url,
            image_path : image_path
        };

        dom.modal.modal('hide');
        
        photo.RequestDataPost({
            request_url : '/admin/baseconfig/slider_images_store',
            data        : form_data,
            callback_data : function(data){ 
                window.location.reload()
            }
        });
    };

    photo._edit_btn_click = function(){
        var item = $(this).parents('tr');
        var id   = photo._int(item.children('td:eq(0)').html());
        var image_path = photo._trim(item.children('td:eq(1)').attr('data_path'));
        var relate_url = photo._trim(item.children('td:eq(2)').html());
        $(".name_area").css('margin-top','0').html("<img src='"+image_path+"' class='venobox' href='"+image_path+"' />"); 
        dom.id.val(id);
        dom.image_path.val(image_path);
        dom.relate_url.val(relate_url);
        dom.modal.modal('show');
    };

    photo._file_uploaded = function(data, target, file_type, item_input){
        if( data.errno == 0 ){
            var data      = data.data;
            var file_path = data.final_path+data.file_name;
            target.css('margin-top','0').html("<img src='"+file_path+"' class='venobox' href='"+file_path+"' />"); 
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
 
    photo._delete_btn_click = function(){
        var item = $(this).parents('tr');
        var id   = photo._int(item.children('td:eq(0)').html()); 
        photo.error_modal({
            onok : function(){ photo._exec_delete('/admin/baseconfig/slider_images_delete', id);},
            id   : id,
            msg  : "您确认要删除吗？"
        });
    };

    dom.venobox.venobox();

    dom.delete_btn.on('click', photo._delete_btn_click);
    dom.edit_btn.on('click', photo._edit_btn_click);
    dom.form_save.on('click', photo._btn_click);
})(window)