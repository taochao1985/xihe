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
        editor    : null,
        delete_btn: $(".delete_btn")
    };

    photo._init_editor = function(){
        dom.editor = UE.getEditor('desc',{
            fileUrlPrefix  : "https://www.photoclub.vip",
            videoUrlPrefix : "https://www.photoclub.vip",
            imageUrlPrefix : "https://www.photoclub.vip"
        });
        console.log('A');
    };

    photo._btn_click = function(){
        var id   = photo._int(dom.id.val());
        var name = photo._trim(dom.name.val());
        var sort = photo._trim(dom.sort.val());
        var desc = dom.editor.getContent();
        var order= $('input[type="radio"][name="lession_order"]:checked').val();
        if ( name == ""){
            dom.name.parents(".form-group").addClass('has-error');
            return false;
        }

        var form_data = {
            id   : id,
            name : name,
            sort : sort,
            order: order,
            desc : desc
        };

        dom.modal.modal('hide');
        
        photo.RequestDataPost({
            request_url : '/admin/baseconfig/lession_type_store',
            data        : form_data,
            callback_data : function(data){ 
                //window.location.reload()
            }
        });
    };

    photo._edit_btn_click = function(){
        var item = $(this).parents('tr');
        var id   = photo._int(item.children('td:eq(0)').find('span').html());
        var name = photo._trim(item.children('td:eq(3)').html());
        var order= item.attr('data_lession_order');
        var desc = item.children('td:eq(0)').find('div').html();
        var sort = photo._trim(item.children('td:eq(1)').html());

        $('input[type="radio"][name="lession_order"][value="'+order+'"]').attr('checked', true);
        dom.id.val(id);
        dom.name.val(name);
        dom.sort.val(sort);
        dom.editor.setContent(desc);
        dom.modal.modal('show');
    };
 
    photo._delete_btn_click = function(){
        var item = $(this).parents('tr');
        var id   = photo._int(item.children('td:eq(0)').find('span').html()); 
        photo.RequestDataPost({
            request_url : '/admin/baseconfig/check_lessions',
            data        :  { id : id},
            request_method : 'get',
            callback_data : function(data){  
                if ( data.count == 0 ){
                    photo._exec_delete('/admin/baseconfig/lession_type_delete', { id : id });  
                }else{ 
                    photo.error_modal({
                        onok : photo.error_modal._close,
                        msg  : "此分类尚有关联课程，暂时无法删除？"
                    });
                }
                
            }
        });
    };

    photo._init_editor();
    dom.delete_btn.on('click', photo._delete_btn_click);
    dom.edit_btn.on('click', photo._edit_btn_click);
    dom.form_save.on('click', photo._btn_click);
})(window)