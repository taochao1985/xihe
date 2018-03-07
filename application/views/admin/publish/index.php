<?php $this->load->view('admin/common_header');?>
  <link href="/node_modules/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="/node_modules/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="/node_modules/lightgallery/dist/css/lightgallery.min.css" rel="stylesheet" type="text/css" />        
 <div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>作品管理</h2>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <table id="datatable-fixed-header" class="table table-striped table-bordered">
        <thead>
          <tr >
            <th>ID</th>
            <th style="width: 200px;">标题</th>
            <th>作品</th>
            <th>添加时间</th>
            <th>评论内容</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
          <?php  if($publishes){ foreach ($publishes as $key=>$val){?>
                <tr>
                     <td><?php echo $val->id;?></td>
                     <td><?php echo $val->description;?></td> 
                     <td class="venobox" style="width: 600px;">
          
                       <?php if ($val->images) {
                          foreach ($val->images as $k => $value) { ?>
                            <a href="<?php echo $value->image_path;?>" style="margin-right: 10px;display: inline-block;"><img src="<?php echo $value->thumb_image;?>"  style="width: 50px; " href="<?php echo $value->image_path;?>"></a>  
                       <?php }} ?>
                     </td>
                     <td><?php echo date('Y-m-d H:i:s',$val->created);?></td> 
                     <td data_id="<?php echo $val->id;?>">
                      <?php if( $val->comments ) { foreach ($val->comments as $k => $v) { ?>
                        <span><?php echo $v->content;?></span>
                        <button class="btn btn-info btn-xs edit_comment" data_id="<?php echo $v->id;?>"><i class="fa fa-pencil"></i>&nbsp;修改</button>
                        <button class="btn btn-danger btn-xs delete_comment" data_id="<?php echo $v->id;?>"><i class="fa fa-trash-o"></i>&nbsp;删除</button>
                        <br>
                      <?php }}?> 
                     <button class="btn btn-success navbar-right btn-xs add_comment">新增</button></td>
                     <td>
                       <button data_id="<?php echo $val->id;?>" class="btn btn-warning navbar-right btn-xs delete_publish">删除</button>
                     </td>
                </tr>
            <?php } }?>

        </tbody>
      </table>
      <?php  $this->load->view('admin/pagination',array('current_page'=>$current_page,'total'=>$total,'url'=>'/admin/publish/index/'));?>
    </div>
  </div>
</div>

<div class="modal fade comment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" >评论作品</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal">
          <div class="form-group">
            <label for="comment_content" class="control-label col-sm-2">评论内容:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control comment_content">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <input type="hidden" class="comment_id" value="0" />
        <input type="hidden" class="publish_id" value="0" />
        <button type="button" class="btn btn-primary comment_save">保存</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" class="hidden_current_page" value="<?php echo $current_page;?>">
<script src="/node_modules/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="/node_modules/datatables.net-bs/js/dataTables.bootstrap.js"></script>
<script src="/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="/node_modules/lightgallery/dist/js/lightgallery.min.js"></script>

<script src="/node_modules/lg-thumbnail/dist/lg-thumbnail.js"></script>
<script src="/assets/js/admin/publishes_index.js"></script>
<?php $this->load->view('admin/common_footer');?>