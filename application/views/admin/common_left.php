  <?php $method = $this->uri->segment(2);
        $setting_array = array('wechat_config',
          'menu_settings_index',
          'first_follow',
          'menu_settings_add',
          'sub_menu_settings_add',
          'single_material_manage',
          'multiply_material_manage',
          'single_list',
          'multiply_list');

  ?>
  <?php $user = $this->session->userdata('admin'); $user = $user[0];$data['user'] = $user;?>

  <div class="col-md-3 left_col">
        <div class="left_col scroll-view">

          <div class="navbar nav_title" style="border: 0;">
            <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>PhotoGrapher!</span></a>
          </div>
          <div class="clearfix"></div>

          <!-- menu prile quick info -->
          <div class="profile">
            <div class="profile_pic">
              <img src="/assets/images/img.jpg" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
              <span>Welcome,</span>
              <h2><?php echo $user->username;?></h2>
            </div>
          </div>
          <!-- /menu prile quick info -->

          <br />

          <!-- sidebar menu -->
          <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

            <div class="menu_section">
              <h3>General</h3>
              <ul class="nav side-menu"> 
                <li><a><i class="fa fa-user"></i> 基础信息管理 <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu" style="display: none">
                    <li><a href="/admin/baseconfig/lession_type">课程类别管理</a>
                    </li>
                    <li><a href="/admin/lessions">课程管理</a>
                    </li>
                    <li><a href="/admin/baseconfig/slider_images">轮播图片管理</a>
                    </li>
                    <li><a href="/admin/users/index">用户管理</a>
                    </li>
                    <li><a href="/admin/news/index">系统公告管理</a>
                    </li>
                  </ul>
                </li>
                 
              </ul>
            </div>

          </div>
          <!-- /sidebar menu -->

        </div>
      </div>
