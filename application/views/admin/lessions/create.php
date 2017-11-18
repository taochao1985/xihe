<?php $this->load->view('admin/common_header');?>
<link href="/node_modules/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet"  type="text/css" href="/node_modules/blueimp-file-upload/css/jquery.fileupload.css" />
<link href="/node_modules/venobox/venobox/venobox.css" rel="stylesheet" type="text/css" />
<link href="/node_modules/wangeditor/release/wangEditor.min.css" rel="stylesheet" type="text/css" />

    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>课程维护</h2>

          <div class="clearfix"></div>
        </div>
        <div class="">
          <br>
            <form class="form-horizontal form-label-left" enctype="multipart/form-data">
                <input type="hidden" class="lession_id" value="<?php if ($lession){ echo $lession->id; }else{?>0<?php }?>" />
                <div class="item form-group field">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="field_one">分类
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                     <select name="lt_id" class=" selectpicker lt_id" >
                        <?php if($lession_types){ foreach($lession_types as $k=>$v){?>
                            <option value="<?php echo $v->id;?>" <?php if($lession&&($lession->lt_id == $v->id)) {?>selected = true<?php }?>><?php echo $v->name;?></option>
                        <?php }}?>
                     </select>
                  </div>
                </div>

                <div class="item form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="field_one">标题
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" name="title" value="<?php if($lession){echo $lession->title;}?>" class="form-control col-md-7 col-xs-12 title">
                  </div>
                </div>

                <div class="item form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="field_one">视频
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12 upload_item" style="margin-top:8px;">
                    <div class="btn btn-success fileinput-button pull-left">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>选择视频</span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input class="fileupload" type="file" name="userfile" multiple="" file_type="videos">
                        <input type="hidden" class="hidden_video_input" >
                    </div> 
                    <div class="progress upload-process pull-left hidden">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                    <div class="name_area upload-process name-area pull-left"></div>
                    <span class="clearfix"></span>
                  </div>
                </div>

                <div class="item form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="field_one">音频
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12 upload_item" style="margin-top:8px;">
                    <div class="btn btn-success fileinput-button pull-left">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>选择音频</span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input class="fileupload" type="file" name="userfile" multiple="" file_type="audios">
                        <input type="hidden" class="hidden_audio_input" >
                    </div> 
                    <div class="progress upload-process pull-left hidden">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                    <div class="name_area upload-process name-area pull-left"></div>
                    <span class="clearfix"></span>
                  </div>
                </div>

                <div class="item form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="field_one">图片
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12 upload_item" style="margin-top:8px;">
                    <div class="btn btn-success fileinput-button pull-left">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>选择图片</span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input class="fileupload" type="file" name="userfile" multiple="" file_type="images">
                        <input type="hidden" class="hidden_image_input" >
                    </div> 
                    <div class="progress upload-process pull-left hidden">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                    <div class="name_area upload-process name-area pull-left"></div>
                    <span class="clearfix"></span>
                  </div>
                </div>
                <div class="item form-group">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="field_one">正文
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12 upload_item" style="margin-top:8px; z-index: 0;">
                        <div class="description" id="description">
                            <?php if($lession){echo $lession->description;}?>
                        </div>
                    </div>
                </div> 
                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <button class="btn btn-success save_btn" type="button">保存</button>
                    <button class="btn btn-primary form_go_back" type="button">返回</button>
                  </div>
                </div>

          </form>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript" src="/node_modules/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/cors/jquery.xdr-transport.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload-process.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload-video.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload-audio.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload-validate.js"></script>
<script type="text/javascript" src="/node_modules/wangeditor/release/wangEditor.min.js"></script>
<script type="text/javascript" src="/node_modules/venobox/venobox/venobox.min.js"></script>
<script src="/assets/js/admin/lession_create.js"></script> 
<?php $this->load->view('admin/common_footer');?>