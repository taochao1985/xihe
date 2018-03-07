"use strict";
(function(window) { 
    var photo = window.photo; 
    var table = $('#datatable-fixed-header').DataTable({
          fixedHeader: true
        });
    var dom = {
        del_btn : $(".delete_configs")
    }
 
    photo._delete_lession = function(jd_id){
        
        photo.error_modal({
            onok : function(){ photo._exec_delete('/admin/lessions/delete',{ id : jd_id });},
            id   : jd_id,
            msg  : "您确定要删除此条数据吗？"
        });
    };
    $(document).on("click", ".delete_configs", function() {
        var jd_id = $(this).attr('data_id');
        photo._delete_lession(jd_id);
    });
    // var i = 0;
    // function img_rename(){
    //     photo.RequestDataPost({
    //         request_url : '/admin/admin/rename_img',
    //         data        : {i:i},
    //         callback_data : function(data){ 
    //             console.log(i);
    //             i++;
    //         }
    //     });
    // }
    // setInterval(img_rename, 2000);
})(window)