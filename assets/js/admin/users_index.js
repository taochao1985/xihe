"use strict";
(function(window) { 
    var photo = window.photo; 
    var table = $('#datatable-fixed-header').DataTable({
          fixedHeader: true
        });
    var dom = {
        form_user    : $(".form_user"),
        apply_pass   : $(".agent_apply_pass"),
        apply_refuse : $(".agent_apply_refuse"),
        refuse_modal : $(".apply_refuse_modal"),
        refuse_save  : $(".refuse_save"),
        reason       : $(".reason")
    }
 
    photo._form_user = function(){
        var uid = $(this).attr('data_id');
        photo.error_modal({
            onok : function(){ photo._exec_delete('/admin/users/form_user',{ id : uid });},
            uid   : uid,
            msg  : "您确定要赠送会员吗？"
        });
    };

    photo._apply_pass = function(){
        var uid = $(this).attr('data_id');
        photo.error_modal({
            onok : function(){ photo._exec_delete('/admin/users/apply_formated',{ uid : uid , type : 'pass'});},
            uid   : uid,
            msg  : "您确定审核通过吗？"
        });
    };

    photo._refuse_save = function(){
        var uid = $(this).attr('data_id');
        var reason = dom.reason.val();
        dom.refuse_modal.modal('hide');
        photo.RequestDataPost({
            request_url : '/admin/users/apply_formated',
            data        : {
                type   : 'refused',
                uid    : uid,
                reason : reason
            },
            callback_data : function(data){ 
                photo.error_modal({
                    msg  : data.msg
                });
            }
        });
    };

    photo._apply_refuse = function(){
        var uid = $(this).attr('data_id');
        dom.refuse_save.attr('data_id', uid);
        dom.refuse_modal.modal('show');
    }

    dom.form_user.on('click', photo._form_user);
    dom.apply_pass.on('click', photo._apply_pass);
    dom.apply_refuse.on('click', photo._apply_refuse);
    dom.refuse_save.on('click', photo._refuse_save);
})(window)