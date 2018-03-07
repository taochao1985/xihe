<?php $this->load->view('admin/common_header');?>
  <link href="/node_modules/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="/node_modules/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
          
 <div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="row tile_count">
          <div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count">
            <div class="left"></div>
            <div class="right">
              <span class="count_top"><i class="fa fa-user"></i> 关注总数</span>
              <div class="count green"><?= $user_count;?></div>
              <span class="count_bottom"><i class="green"><?= $yesterday_user_count;?> </i> 昨日数</span>
            </div>
          </div>
          <div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count">
            <div class="left"></div>
            <div class="right">
              <span class="count_top"><i class="fa fa-clock-o"></i> 会员总数</span>
              <div class="count info"><?= $member_count;?></div>
              <span class="count_bottom"><i class="info"><?= $yesterday_member_count;?></i> 昨日数</span>
            </div>
          </div>
          <div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count">
            <div class="left"></div>
            <div class="right">
              <span class="count_top"><i class="fa fa-user"></i> 发布作品总数</span>
              <div class="count yellow"><?= $publish_count; ?></div>
              <span class="count_bottom"><i class="yellow"><?= $yesterday_publish_count;?></i> 昨日数</span>
            </div>
          </div>
          <div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count">
            <div class="left"></div>
            <div class="right">
              <span class="count_top"><i class="fa fa-user"></i> 推广人数</span>
              <div class="count blue"><?= $agent_count; ?></div>
              <span class="count_bottom"><i class="blue"><?= $yesterday_agent_count; ?> </i> 昨日数</span>
            </div>
          </div>
          <div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count">
            <div class="left"></div>
            <div class="right">
              <span class="count_top"><i class="fa fa-user"></i> 推荐成功数</span>
              <div class="count red"><?= $agent_member_count; ?></div>
              <span class="count_bottom"><i class="red"><?= $yesterday_agent_member_count; ?> </i> 昨日数</span>
            </div>
          </div>
          <div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count">
            <div class="left"></div>
            <div class="right">
              <span class="count_top"><i class="fa fa-user"></i> 品论数</span>
              <div class="count"><?= $comment_count; ?></div>
              <span class="count_bottom"><i class=""><?= $yesterday_comment_count; ?> </i> 昨日数</span>
            </div>
          </div>

        </div>
    <div class="x_content">
       <div style="width: 100%;">
        <div id="container" class="demo-placeholder" style="width: 100%; height:600px;"></div>
      </div>
    </div>
  </div>
</div>
<script src="/assets/js/highcharts.js"></script>
<script src="/assets/js/admin/main_index.js"></script>
<?php $this->load->view('admin/common_footer');?>