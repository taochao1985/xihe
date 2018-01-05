<?php $this->load->view('admin/common_header');?>
  <link href="/node_modules/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="/node_modules/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
          
 <div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>会员管理</h2>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <table id="datatable-fixed-header" class="table table-striped table-bordered">
        <thead>
          <tr >
            <th>ID</th>
            <th>上级代理</th>
            <th>昵称</th>
            <th>添加时间</th>
            <th>支付状态</th>
            <th>代理状态</th> 
        </tr>
        </thead>
        <tbody>
          <?php  if($users){ foreach ($users as $key=>$val){?>
                <tr>
                     <td><?php echo $val->uid;?></td>
                     <td><?php echo $val->agent_nickname;?></td> 
                     <td><?php echo $val->nickname;?></td> 
                     <td><?php echo date('Y-m-d H:i:s',$val->created);?></td> 
                     <td>
                        <?php if (!$val->pay_status){ ?>
                            <a href="javascript:void(0)" class="btn btn-info btn-xs form_user" data_id="<?php echo $val->uid;?>"><i class="fa fa-pencil"></i>&nbsp;立即赠送会员</a>
                        <?php } else if ($val->pay_status == 2) {?>
                            赠送会员
                        <?php }else {?>
                            普通会员
                        <?php }?>    
                    </td>
                    <td>
                        <?php if ( $val->agent_status == 1){ ?>
                            已是代理
                        <?php } else if ( $val->agent_status == 2){ ?>
                            审核被拒绝，理由：<?php echo $val->reason;?>
                        <?php } else if ($val->agent_apply == 1) {?>
                            <a href="javascript:void(0)" class="btn btn-info btn-xs agent_apply_pass" data_id="<?php echo $val->uid;?>"><i class="fa fa-pencil"></i>&nbsp;审核通过</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-xs agent_apply_refuse" data_id="<?php echo $val->uid;?>"><i class="fa fa-pencil"></i>&nbsp;审核不通过</a>
                        <?php }else {?>
                            暂未申请
                        <?php }?>   
                    </td>
                </tr>
            <?php } }?>

        </tbody>
      </table>
    </div>
  </div>
</div>

    <div class="modal fade apply_refuse_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" >拒绝代理</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal">
          <div class="form-group">
            <label for="lession-type-name" class="control-label col-sm-2">拒绝理由:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control reason">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <input type="hidden" class="lession_type_id" value="0" />
        <button type="button" class="btn btn-primary refuse_save">保存</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

    <script src="/node_modules/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="/node_modules/datatables.net-bs/js/dataTables.bootstrap.js"></script>
    <script src="/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script> 
    <script src="/assets/js/admin/users_index.js"></script>
<?php $this->load->view('admin/common_footer');?>