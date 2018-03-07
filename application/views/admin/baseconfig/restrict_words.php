<?php $this->load->view('admin/common_header');?>
 <div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>敏感词管理</h2>
      <button class="btn btn-success navbar-right btn-xs" data-toggle="modal" data-target=".lession_type_modal">新增</button>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <table id="datatable-fixed-header" class="table table-striped table-bordered">
        <thead>
          <tr >
            <th>ID</th>
            <th>敏感词</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
          <?php  if($types){
                   foreach ($types as $key=>$val){?>
                <tr >
                     <td><?php echo $val->id;?></td> 
                     <td><?php echo $val->words;?></td>
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
        <h4 class="modal-title" >敏感词管理</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal">
          <div class="form-group">
            <label for="lession-type-name" class="control-label col-sm-2">敏感词:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control lession_type_name">
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

<script src="/assets/js/admin/restrict_words.js"></script>
<?php $this->load->view('admin/common_footer');?>