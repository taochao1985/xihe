"use strict";
(function(window) { 
    var photo = window.photo; 
    var dom = {
        venobox : $(".venobox"),
        add     : $(".add_comment"),
        modal   : $(".comment_modal"),
        id      : $(".comment_id"),
        msg_id  : $(".msg_id"),
        open_id : $(".open_id"),
        save    : $(".comment_save"),
        content : $(".comment_content"), 
        page    : $(".hidden_current_page")
    };

    dom._init_modal = function(opt){
      dom.content.val(opt.content);
      dom.id.val(opt.id);
      dom.msg_id.val(opt.msg_id);
      dom.open_id.val(opt.openid);
      dom.modal.modal('show');
    };

    dom._add_comment = function(){
      dom._init_modal({
        content : "",
        id      : $(this).parents('td').attr('data_id'),
        msg_id  : $(this).parents('td').attr('data_msg_id'),
        openid  : $(this).parents('td').attr('data_openid')
      });
    };
 

    dom._save_comment = function(){
      dom.modal.modal('hide');
      var id = dom.id.val();
      var msg_id = dom.msg_id.val();
      var open_id = dom.open_id.val();
      var content = dom.content.val();
      var page = dom.page.val();
      photo.RequestDataPost({
            request_url : '/admin/users/message_reply',
            data        : {id:id, content:content, msg_id: msg_id,openid:open_id},
            callback_data : function(data){ 
                photo.error_modal({
                    onok : function(){ photo._go_next('/admin/users/messages/'+page);}, 
                    msg  : data.msg
                });
            }
        });
    }

    dom.venobox.venobox();
    dom.add.on('click', dom._add_comment);
    dom.save.on('click', dom._save_comment);
})(window)