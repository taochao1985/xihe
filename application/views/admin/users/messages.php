<?php $this->load->view('admin/common_header');?>

  <link href="/node_modules/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="/node_modules/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet"  type="text/css" href="/node_modules/venobox/venobox/venobox.css" />   
 <div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>消息管理</h2>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <table id="datatable-fixed-header" class="table table-striped table-bordered">
        <thead>
          <tr >
            <th>用户昵称</th>
            <th>消息内容</th>
            <th>添加时间</th>
            <th>回复内容</th>
        </tr>
        </thead>
        <tbody>
          <?php  if($messages){ foreach ($messages as $key=>$val){?>
                <tr>
                     <td><?php echo $val->nickname;?></td>
                     <td><?php if ($val->msg_type == 'text'){ echo $val->msg_content;}else {?> 
                      <img src="<?php echo $val->msg_content;?>" class="venobox"  style="width: 50px; " href="<?php echo $val->msg_content;?>" > <?php }?>  
                    </td> 
                     <td><?php echo date('Y-m-d H:i:s',$val->created);?></td> 
                     <td data_id="<?php echo $val->id;?>" data_openid="<?php echo $val->openid;?>" data_msg_id="<?php echo $val->msg_id;?>">
                      <?php if( $val->reply ) { foreach ($val->reply as $k => $v) { ?>
                        <span><?php echo $v->content;?></span>
                        <br>
                      <?php }}?> 
                     <button class="btn btn-success navbar-right btn-xs add_comment">回复</button></td>
                </tr>
            <?php } }?>

        </tbody>
      </table>
      <?php  $this->load->view('admin/pagination',array('current_page'=>$current_page,'total'=>$total,'url'=>'/admin/users/messages/'));?>
    </div>
  </div>
</div>

<div class="modal fade comment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" >消息回复</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal">
          <div class="form-group">
            <label for="comment_content" class="control-label col-sm-2">回复内容:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control comment_content">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <input type="hidden" class="comment_id" value="0" />
        <input type="hidden" class="msg_id" value="0" />
        <input type="hidden" class="open_id" value="0" />
        <button type="button" class="btn btn-primary comment_save">保存</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" class="hidden_current_page" value="<?php echo $current_page;?>">

<script src="/node_modules/venobox/venobox/venobox.min.js"></script>
<script src="/assets/js/admin/messages_index.js"></script>
<?php $this->load->view('admin/common_footer');?>