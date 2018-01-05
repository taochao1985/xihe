<?php $this->load->view('admin/common_header');?>
<link href="/node_modules/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<link href="/node_modules/wangeditor/release/wangEditor.min.css" rel="stylesheet" type="text/css" />

    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>公告维护</h2>

          <div class="clearfix"></div>
        </div>
        <div class="">
          <br>
            <form class="form-horizontal form-label-left" enctype="multipart/form-data">
                <input type="hidden" class="news_id" value="<?php if ($news){ echo $news->id; }else{?>0<?php }?>" />

                <div class="item form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="field_one">标题
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" name="title" value="<?php if($news){echo $news->title;}?>" class="form-control col-md-7 col-xs-12 title">
                  </div>
                </div>

                <div class="item form-group">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="field_one">正文
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12 upload_item" style="margin-top:8px; z-index: 0;">
                        <div class="description" id="description">
                            <?php if($news){echo $news->description;}?>
                        </div>
                    </div>
                </div> 
                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <button class="btn btn-success save_btn" type="button">保存</button>
                    <button class="btn btn-primary" type="button" onclick="history.go(-1)">返回</button>
                  </div>
                </div>

          </form>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript" src="/node_modules/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="/node_modules/wangeditor/release/wangEditor.min.js"></script>
<script src="/assets/js/admin/news_create.js"></script> 
<?php $this->load->view('admin/common_footer');?>