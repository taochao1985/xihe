<?php $this->load->view('admin/common_header');?>

<link rel="stylesheet"  type="text/css" href="/node_modules/blueimp-file-upload/css/jquery.fileupload.css" />
<link href="/node_modules/venobox/venobox/venobox.css" rel="stylesheet" type="text/css" />
 <div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>轮播图片管理</h2>
      <button class="btn btn-success navbar-right btn-xs" data-toggle="modal" data-target=".lession_type_modal">新增</button>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <table id="datatable-fixed-header" class="table table-striped table-bordered">
        <thead>
          <tr >
            <th>ID</th>
            <th>图片</th> 
            <th>链接</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
          <?php  if($images){
                   foreach ($images as $key=>$val){?>
                <tr>
                     <td><?php echo $val->id;?></td> 
                     <td data_path = "<?php echo $val->image_path;?>" ><img src="<?php echo $val->image_path;?>" href="<?php echo $val->image_path;?>" class="venobox"/></td>
                     <td><?php echo $val->relate_url;?></td>
                     <td><button class="btn btn-info btn-xs edit_btn"><i class="fa fa-pencil"></i>&nbsp;修改</button>
                     <button class="btn btn-danger btn-xs delete_btn" data_id="<?php echo $val->id;?>"><i class="fa fa-trash-o"></i>&nbsp;删除</button></td>
                </tr>
            <?php } }?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade lession_type_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" >轮播图片</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal">
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
          <div class="form-group">
            <label for="lession-type-name" class="control-label col-sm-2">关联链接:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control relate_url">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <input type="hidden" class="lession_type_id" value="0" />
        <button type="button" class="btn btn-primary lession_type_save">保存</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/cors/jquery.xdr-transport.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload-process.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload-video.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload-audio.js"></script>
<script type="text/javascript" src="/node_modules/blueimp-file-upload/js/jquery.fileupload-validate.js"></script>
<script type="text/javascript" src="/node_modules/venobox/venobox/venobox.min.js"></script>
<script src="/assets/js/admin/slider_images.js"></script>
<?php $this->load->view('admin/common_footer');?>