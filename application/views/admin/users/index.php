<?php $this->load->view('admin/common_header');?>
  <link href="/node_modules/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="/node_modules/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet"  type="text/css" href="/node_modules/bootstrap-datetime-picker/css/bootstrap-datetimepicker.min.css" />    

<link href="/node_modules/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
 <div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel form-inline">
    <div class="x_title">
      <h2>会员管理
        <?php if ($start_time || $end_time ) {?>
          <small class="bg-red btn btn-sm">搜索结果关注：<?= $search_count;?>人</small>
        <small class="bg-green btn btn-sm">搜索结果会员：<?= $search_member_count;?>人</small>
        <small class="btn-primary btn btn-sm">搜索结果推广人：<?= $search_agent_count;?>人</small>
        <?php }else{ ?>
          <small class="bg-red btn btn-sm">今日新增关注：<?= $new_count;?>人</small>
          <small class="bg-green btn btn-sm">今日新增会员：<?= $new_member_count;?>人</small>
          <small class="btn-primary btn btn-sm">今日新增推广人：<?= $new_agent_count;?>人</small>
        <?php }?>
      </h2>
      <h2 class="nav navbar-right panel_toolbox">
        <small class="bg-red btn btn-sm">累计关注：<?= $user_count;?>人</small>
        <small class="bg-green btn btn-sm">累计会员：<?= $user_member_count;?>人</small>
        <small class="btn-primary btn btn-sm">累计推广人：<?= $user_agent_count;?>人</small>
      </h2>  
      <div class="clearfix"></div>
    </div>
    <div class="form-group">
      <label for="ex3">时间段</label>
      <input type="text"  class="form-control start_time time_field" value="<?= $start_time;?>">
      &nbsp;&nbsp;--&nbsp;&nbsp;
      <input type="text"  class="form-control end_time time_field" value="<?= $end_time;?>">
    </div>
    <div class="form-group">
      <label for="ex3">昵称</label>
      <input type="text"  class="form-control nickname" value="<?= $nickname;?>">
    </div>
    <div class="form-group">
      <label for="ex3">推广人</label>
      <select class="agent_user selectpicker">
        <option value="-1" <?php if($agent_user == -1) { ?>selected<?php }?> >全部</option>
        <option value="1" <?php if($agent_user == 1) { ?>selected<?php }?> >已通过</option>
        <option value="2" <?php if($agent_user == 2) { ?>selected<?php }?> >已拒绝</option>
        <option value="0" <?php if($agent_user == 0) { ?>selected<?php }?> >待审核</option>
      </select>
    </div>
    <button type="button" class="btn btn-primary search_btn">立即搜索</button>
    <button type="button" class="btn btn-default reset_btn">重置</button>
    <div class="x_content">
      <table id="datatable-fixed-header" class="table table-striped table-bordered">
        <thead>
          <tr >
            <th>ID</th>
            <th>上级代理</th>
            <th>昵称</th>
            <th>微信号</th>
            <th>姓名</th>
            <th>性别</th>
            <th>省份</th>
            <th width="150px;">详细地址</th>
            <th>设备</th>
            <th>所获奖项</th>
            <th>手机号</th>
            <th>头像</th>
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
                     <td><?php echo $val->wechat_num;?></td>
                     <td><?php echo $val->real_name;?></td>  
                     <td><?php if ( $val->sex == 1) {echo '男';}else if( $val->sex == 2) { echo '女';} else{ echo '未知';}?></td> 
                     <td><?php echo $val->province;?></td>  
                     <td><?php echo $val->address;?></td>
                     <td><?php echo $val->device;?></td>
                     <td><?php echo $val->metals;?></td>    
                     <td><?php if ( $val->mobile != 0 ) {echo $val->mobile;} else {echo '未填写';}?></td>  
                     <td><img src="<?php echo $val->avatarurl;?>" style="width: 50px;" /></td> 
                     <td><?php echo date('Y-m-d H:i:s',$val->created);?></td> 
                     <td>
                        <?php if (!$val->pay_status){ ?>
                            <a href="javascript:void(0)" class="btn btn-info btn-xs form_user" data_id="<?php echo $val->uid;?>"><i class="fa fa-pencil"></i>&nbsp;立即赠送会员</a>
                        <?php } else if ($val->pay_status == 2) {?>
                            赠送会员 <a href="javascript:void(0)" class="btn btn-danger btn-xs form_user_delete" data_id="<?php echo $val->uid;?>"><i class="fa fa-pencil"></i>&nbsp;取消赠送</a>
                        <?php }else {?>
                            普通会员
                        <?php }?>    
                    </td>
                    <td>
                        <?php if ( $val->agent_status == 1){ ?>
                            已是代理&nbsp;&nbsp;
                            <a href="/admin/users/agent_records/<?php echo $val->uid;?>" class="btn btn-info btn-xs"><i class="fa fa-money"></i>&nbsp;推广记录</a>
                        <?php } else if ( $val->agent_status == 2){ ?>
                            <span><?php echo $val->reason;?></span>
                            <a href="javascript:void(0)" class="btn btn-info btn-xs edit_refuse_reason" data_id="<?php echo $val->uid;?>"><i class="fa fa-pencil"></i>&nbsp;修改理由</a>
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
      <?php  $this->load->view('admin/pagination',array('current_page'=>$current_page,'total'=>$total,'url'=>'/admin/users/index/','params'=>'?start_time='.$start_time.'&end_time='.$end_time.'&agent_user='.$agent_user.'&nickname='.$nickname));?>
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

<script type="text/javascript" src="/node_modules/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="/node_modules/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js"></script>
<script src="/assets/js/admin/users_index.js"></script>
<?php $this->load->view('admin/common_footer');?>