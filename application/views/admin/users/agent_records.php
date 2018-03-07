<?php $this->load->view('admin/common_header');?>
  <link href="/node_modules/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="/node_modules/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
          
 <div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>推广记录</h2>
      <?php if ($userinfo) {?>
        <div class="pull-right red">推广人：<?php echo $userinfo->nickname;?></div>
      <?php }?>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <table id="datatable-fixed-header" class="table table-striped table-bordered">
        <thead>
          <tr >
            <th>被推广人昵称</th>
            <th>推广时间</th>
        </tr>
        </thead>
        <tbody>
          <?php  if($users){ foreach ($users as $key=>$val){?>
                <tr>
                     <td><?php echo $val->nickname;?></td> 
                     <td><?php echo date('Y-m-d H:i:s',$val->pay_time);?></td> 
                </tr>
            <?php } }?>

        </tbody>
      </table>
    </div>
  </div>
</div>
 

    <script src="/node_modules/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="/node_modules/datatables.net-bs/js/dataTables.bootstrap.js"></script>
    <script src="/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script> 
<?php $this->load->view('admin/common_footer');?>