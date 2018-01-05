<?php $this->load->view('admin/common_header');?>
  <link href="/node_modules/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="/node_modules/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
          
 <div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>公告管理</h2>

      <a class="btn btn-success navbar-right btn-xs" href="/admin/news/create">新增</a>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <table id="datatable-fixed-header" class="table table-striped table-bordered">
        <thead>
          <tr >
            <th>ID</th>
            <th>标题</th>
            <th>添加时间</th>
            <th>修改时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
          <?php  if($news){ foreach ($news as $key=>$val){?>
                <tr>
                     <td><?php echo $val->id;?></td>
                     <td><?php echo $val->title;?></td> 
                     <td><?php echo date('Y-m-d H:i:s',$val->created);?></td> 
                     <td><?php echo date('Y-m-d H:i:s',$val->updated);?></td> 
                     <td>
                        <a href="/admin/news/edit/<?php echo $val->id;?>" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>&nbsp;修改</a>
                        <?php if($val->id > 2){ ?>
                            <a href="javascript:void(0)" class="btn btn-danger btn-xs delete_configs" data_id="<?php echo $val->id;?>"><i class="fa fa-trash-o"></i>&nbsp;删除</a>
                        <?php }?>
                    </td>
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
    <script src="/assets/js/admin/news_index.js"></script>
<script>

 </script>
<?php $this->load->view('admin/common_footer');?>