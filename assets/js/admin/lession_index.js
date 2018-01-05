"use strict";
(function(window) { 
    var photo = window.photo; 
    var table = $('#datatable-fixed-header').DataTable({
          fixedHeader: true
        });
    var dom = {
        del_btn : $(".delete_configs")
    }
 
    photo._delete_lession = function(){
        var jd_id = $(this).attr('data_id');
        photo.error_modal({
            onok : function(){ photo._exec_delete('/admin/lessions/delete',{ id : jd_id });},
            id   : jd_id,
            msg  : "您确定要删除此条数据吗？"
        });
    };

    dom.del_btn.on('click', photo._delete_lession);
})(window)