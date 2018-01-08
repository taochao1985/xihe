"user strict";

(function(window){
	if(!window.photo){
        window.photo = {};
    }
    var photo = window.photo;
    var common_dom = {
        ok_btn     : $(".confirm_btn"),
        cancel_btn : $(".cancel_btn"),
        error_modal: $(".error_modal"),
        go_back    : $(".form_go_back"),
        base_url   : $(".hidden_base_url").val()
    };
    photo.base_url = common_dom.base_url;
    photo.error_modal = {};

    photo.error_modal = function(opt){
        if ( opt.onok != undefined ){
            photo.error_modal._onokclick = opt.onok;
        }

        if( opt.id != undefined ){
            common_dom.ok_btn.attr('data_id', opt.id);
        }
        
        if( opt.show_cancel != undefined ){
            common_dom.error_modal.find('.cancel_btn').addClass("hidden");
        }
        common_dom.error_modal.find('.error_msg h2').html(opt.msg);
        common_dom.error_modal.modal('show');
    };
 
    photo.error_modal._close = function(){
        common_dom.error_modal.modal('hide');
    };

    photo.error_modal._ok_clicked = function(){
        if( photo.error_modal._onokclick ){
            photo.error_modal._onokclick();
        }else{
            window.location.reload();
        }
    };

    common_dom.ok_btn.on('click',photo.error_modal._ok_clicked);
    photo._trim = function(item){
        return $.trim(item);
    };

    photo._int = function(item){
        return parseInt(item);
    }

    photo._exec_delete =function(url, data){
        photo.RequestDataPost({
            request_url : url ,
            data        : data,
            callback_data : function(data){ 
                if(data.code == 0){
                    window.location.reload()
                }else{
                    photo.error_modal({
                            msg : data.msg
                        })
                }
            }
        });
    };

    photo.RequestDataPost = function(opt) {
        var request_method   = "POST";
        var request_datatype = "json";
        if ( opt.request_method ){
            request_method = opt.request_method;
        }
        if( opt.request_datatype ){
            request_datatype = opt.request_datatype;
        }
       
        $.ajax({
                type: request_method,
                url:  opt.request_url,
                data: opt.data,
                dataType: request_datatype
            })
            .success(function(data){ 
                    if (data.code == 0 ){
                        if(opt.option_data){
                            opt.callback_data(data, opt.option_data);
                        }else {
                            opt.callback_data(data);
                        }    
                    }else{
                        photo.error_modal({
                            msg : data.msg
                        })
                    }
            })
            .done(function(data){ 
                 
            })
            .error(function(err){
                photo.error_modal({
                    msg : err
                })
            })

        return false;
    };  

    var upload_url = "/admin/fileupload";

    photo._init_fileupload = function(opt){
    	opt.target.fileupload({
	        url: upload_url,
	        formData: {file_type: opt.file_type},
	        dataType: 'json',
	        done: function (e, data) {
	        	photo._file_uploaded(data.result, opt.name_area, opt.file_type, opt.target_input); 
	        }, 
	        progressall: function (e, data) {
                opt.name_area.addClass('hidden');
                opt.upload_process.removeClass('hidden');
	            var progress = parseInt(data.loaded / data.total * 100, 10);
	            opt.upload_process.find('.progress-bar').css(
	                'width',
	                progress + '%'
	            );
                if( progress == 100 ){
                    opt.upload_process.addClass('hidden');
                    opt.name_area.removeClass('hidden');
                }
	        }
	    }).prop('disabled', !$.support.fileInput)
	        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    };

    photo._go_back = function(){
        window.history.go(-1);
    };

    photo._go_next = function(url){
        setTimeout(function(){ window.location.href=url;},500);
    }

    common_dom.go_back.on('click',photo._go_back);

    var validator = new FormValidator();
    if(document.forms[0]){
        document.forms[0].addEventListener('blur', function(e){
             var v = $(this).val();
             if( v != ''){
                $(this).parents('item').removeClass('has-error');
             }
        }, true);

        document.forms[0].addEventListener('input', function(e){
            var v = $(this).val();
             if( v != ''){
                $(this).parents('item').removeClass('has-error');
             }
        }, true);

        document.forms[0].addEventListener('change', function(e){
            var v = $(this).val();
             if( v != ''){
                $(this).parents('item').removeClass('has-error');
             }
        }, true);
    }

})(window||global);