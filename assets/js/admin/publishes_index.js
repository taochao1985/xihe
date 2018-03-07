"use strict";
(function(window) { 
    var photo = window.photo; 
    var dom = {
        venobox : $(".venobox"),
        add     : $(".add_comment"),
        edit    : $(".edit_comment"),
        del     : $(".delete_comment"),
        modal   : $(".comment_modal"),
        id      : $(".comment_id"),
        save    : $(".comment_save"),
        content : $(".comment_content"),
        pub_id  : $(".publish_id"),
        page    : $(".hidden_current_page"),
        del_pub : $(".delete_publish")
    };

    dom._init_modal = function(opt){
      dom.content.val(opt.content);
      dom.id.val(opt.id);
      dom.pub_id.val(opt.pub_id);
      dom.modal.modal('show');
    };

    dom._add_comment = function(){
      dom._init_modal({
        content : "",
        id      : 0,
        pub_id  : $(this).parents('td').attr('data_id')
      });
    };

    dom._edit_comment = function(){
      dom._init_modal({
        content : $(this).prev('span').html(),
        id      : $(this).attr('data_id'),
        pub_id  : $(this).parents('td').attr('data_id')
      });
    };

    dom._delete_comment = function(){
      var id = $(this).attr('data_id');
      photo.error_modal({
          onok : function(){ photo._exec_delete('/admin/publish/delete_comment',{ id : id });},
          id   : id,
          msg  : "您确定要删除此条数据吗？"
      });
    }

    dom._save_comment = function(){
      dom.modal.modal('hide');
      var id = dom.id.val();
      var pub_id = dom.pub_id.val();
      var content = dom.content.val();
      var page = dom.page.val();
      photo.RequestDataPost({
            request_url : '/admin/publish/store',
            data        : {id:id, content:content, pub_id: pub_id},
            callback_data : function(data){ 
                photo.error_modal({
                    onok : function(){ photo._go_next('/admin/publish/index/'+page);}, 
                    msg  : data.msg
                });
            }
        });
    }

    dom._delete_publish = function(){
      var id = $(this).attr('data_id');
      photo.error_modal({
          onok : function(){ photo._exec_delete('/admin/publish/delete',{ id : id });},
          id   : id,
          msg  : "您确定要删除此作品吗？"
      });
    }

    dom.venobox.lightGallery({
        download : false,
        thumbnail:true
    });
    dom.add.on('click', dom._add_comment);
    dom.edit.on('click', dom._edit_comment);
    dom.del.on('click', dom._delete_comment);
    dom.del_pub.on('click', dom._delete_publish);
    dom.save.on('click', dom._save_comment);
})(window)