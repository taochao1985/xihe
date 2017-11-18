"use strict";
(function(window) {
    var photo = window.photo; 
    var dom = {
        form_save : $(".lession_type_save"),
        name      : $(".lession_type_name"),
        id        : $(".lession_type_id"),
        modal     : $(".lession_type_modal"),
        edit_btn  : $(".edit_btn"),
        delete_btn: $(".delete_btn")
    };
    photo._btn_click = function(){
        var id   = photo._int(dom.id.val());
        var name = photo._trim(dom.name.val());

        if ( name == ""){
            dom.name.parents(".form-group").addClass('has-error');
            return false;
        }

        var form_data = {
            id   : id,
            name : name
        };

        dom.modal.modal('hide');
        
        photo.RequestDataPost({
            request_url : '/admin/baseconfig/lession_type_store',
            data        : form_data,
            callback_data : function(data){ 
                window.location.reload()
            }
        });
    };

    photo._edit_btn_click = function(){
        var item = $(this).parents('tr');
        var id   = photo._int(item.children('td:eq(0)').html());
        var name = photo._trim(item.children('td:eq(1)').html());

        dom.id.val(id);
        dom.name.val(name);
        dom.modal.modal('show');
    };
 
    photo._delete_btn_click = function(){
        var item = $(this).parents('tr');
        var id   = photo._int(item.children('td:eq(0)').html()); 
        photo.RequestDataPost({
            request_url : '/admin/baseconfig/check_lessions',
            data        :  {id : id},
            callback_data : function(data){  
                if ( data.count == 0 ){
                    photo._exec_delete(id);  
                }else{ 
                    photo.error_modal({
                        onok : function(){ photo._exec_delete('/admin/baseconfig/lession_type_delete', id);},
                        id   : id,
                        msg  : "此分类尚有关联课程，您确认要删除吗？"
                    });
                }
                
            }
        });
    };

    dom.delete_btn.on('click', photo._delete_btn_click);
    dom.edit_btn.on('click', photo._edit_btn_click);
    dom.form_save.on('click', photo._btn_click);
})(window)