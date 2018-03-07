"use strict";
(function(window) {
    var photo = window.photo; 
    var dom = {
        form_save : $(".lession_type_save"),
        name      : $(".lession_type_name"),
        sort      : $(".lession_type_sort"),
        id        : $(".lession_type_id"),
        modal     : $(".lession_type_modal"),
        edit_btn  : $(".edit_btn"),
        delete_btn: $(".delete_btn")
    };
    photo._btn_click = function(){
        var id   = photo._int(dom.id.val());
        var words = photo._trim(dom.name.val()); 
        if ( words == ""){
            dom.name.parents(".form-group").addClass('has-error');
            return false;
        }

        var form_data = {
            id   : id,
            words : words
        };

        dom.modal.modal('hide');
        
        photo.RequestDataPost({
            request_url : '/admin/baseconfig/restrict_words_store',
            data        : form_data,
            callback_data : function(data){ 
                window.location.reload()
            }
        });
    };

    photo._edit_btn_click = function(){
        var item = $(this).parents('tr');
        var id   = photo._int(item.children('td:eq(0)').html());
        var words = photo._trim(item.children('td:eq(1)').html());
        dom.id.val(id);
        dom.name.val(words);
        dom.modal.modal('show');
    };
 
    photo._delete_btn_click = function(){
        var item = $(this).parents('tr');
        var id   = photo._int(item.children('td:eq(0)').html()); 
        photo.error_modal({
            onok : function(){ photo._exec_delete('/admin/baseconfig/restrict_words_delete',{ id : id });},
            id   : id,
            msg  : "您确定要删除此条数据吗？"
        });
    };

    dom.delete_btn.on('click', photo._delete_btn_click);
    dom.edit_btn.on('click', photo._edit_btn_click);
    dom.form_save.on('click', photo._btn_click);
})(window)