<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>PhotoGrapher | Backend </title>

  <link href="/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/node_modules/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="/node_modules/animate.css/animate.min.css" rel="stylesheet"> 
  <!-- Custom styling plus plugins -->
  <link href="/assets/css/custom.css" rel="stylesheet"> 
  <link href="/assets/css/admin.css" rel="stylesheet"> 
  <script src="/node_modules/jquery/jquery.min.js"></script> 
  <script src="/node_modules/@yaireo/validator/validator.js"></script>
</head>
<?php $user = $this->session->userdata('admin'); $user = $user[0];$data['user'] = $user;?>

<body class="nav-md">

  <div class="container body">
    <div class="main_container">

      <?php $this->load->view('admin/common_left',$data);?>

      <!-- top navigation -->
      <div class="top_nav">

        <div class="nav_menu">
          <nav class="" role="navigation">
            <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
              <li class="">
                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                 <?php echo $user->username;?>
                  <span class=" fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu pull-right">
                  <li><a href="/admin/login/logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                  </li>
                </ul>
              </li>
            </ul>
          </nav>
        </div>

      </div>
      <!-- /top navigation -->
      
  <!-- Modal -->
    <div class="modal fade error_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">操作提示</h4>
          </div>
          <div class="modal-body text-center error_msg"><h2>系统错误！</h2>
          </div>
          <div class="modal-footer text-center">
            <button type="button" class="btn btn-danger confirm_btn">确认</button>
            <button type="button" class="btn btn-default cancel_btn" data-dismiss="modal">取消</button>
          </div>
        </div>
      </div>
    </div>

  <script src="/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>  
  <script src="/assets/js/custom.js"></script>
  <script src="/assets/js/admin/common.js"></script>
      <!-- page content -->
    <div class="right_col" role="main" id="music_target" style="min-height:1000px;">
