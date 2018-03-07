"use strict";
(function(window) { 
    var photo = window.photo; 
    // var table = $('#datatable-fixed-header').DataTable({
    //       fixedHeader: true
    //     });
    var dom = {
        form_user    : $(".form_user"),
        user_del     : $(".form_user_delete"),
        apply_pass   : $(".agent_apply_pass"),
        apply_refuse : $(".agent_apply_refuse"),
        refuse_modal : $(".apply_refuse_modal"),
        refuse_save  : $(".refuse_save"),
        reason       : $(".reason"),
        time_field   : $(".time_field"),
        start_time   : $(".start_time"),
        end_time     : $(".end_time"),
        reset_btn    : $(".reset_btn"),
        search_btn   : $(".search_btn"),
        nickname     : $(".nickname"),
        agent_user   : $(".agent_user"),
        edit_reason  : $(".edit_refuse_reason")
    }
 
    photo._form_user = function(){
        var uid = $(this).attr('data_id');
        photo.error_modal({
            onok : function(){ photo._exec_delete('/admin/users/form_user',{ id : uid });},
            uid   : uid,
            msg  : "您确定要赠送会员吗？"
        });
    };

    photo._user_del = function(){
        var uid = $(this).attr('data_id');
        photo.error_modal({
            onok : function(){ photo._exec_delete('/admin/users/form_user_delete',{ id : uid });},
            uid   : uid,
            msg  : "您确定要取消赠送会员吗？"
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

    photo._edit_refuse_btn_click = function(){
        var uid = $(this).attr('data_id');
        dom.reason.val($(this).prev('span').html());
        dom.refuse_save.attr('data_id', uid);
        dom.refuse_modal.modal('show');
    };

    photo._url_exec = function(url){
        window.location.href = url;
    }

    photo._search_btn_click = function(){
        var start_time = dom.start_time.val();
        var end_time = dom.end_time.val();
        var nickname = $.trim(dom.nickname.val());
        var agent_user = dom.agent_user.val();
        var where="?1=1";
        if ( start_time ) {
           where += "&start_time="+start_time;
        }

        if ( end_time ) {
           where += "&end_time="+end_time;
        }

        if ( nickname ) {
           where += "&nickname="+nickname;
        }

        if ( agent_user ){
            where += "&agent_user=" + agent_user;
        }

         photo._url_exec('/admin/users/index'+where);
    }

    photo._reset_btn_click = function(){
        photo._url_exec('/admin/users/index?start_time=&end_time=&agent_user=-1');
    }

    dom.time_field.datetimepicker({
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 4,
        forceParse: 0,
        format: 'yyyy-mm-dd'
    });

    dom.edit_reason.on('click', photo._edit_refuse_btn_click);
    dom.search_btn.on('click', photo._search_btn_click);
    dom.reset_btn.on('click', photo._reset_btn_click);
    dom.user_del.on('click', photo._user_del);
    dom.form_user.on('click', photo._form_user);
    dom.apply_pass.on('click', photo._apply_pass);
    dom.apply_refuse.on('click', photo._apply_refuse);
    dom.refuse_save.on('click', photo._refuse_save);
})(window)