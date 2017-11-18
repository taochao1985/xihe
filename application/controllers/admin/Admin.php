<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    private $tokenFile;
    private $lastTimeFile;
    private $expire = 7000;
    function __construct()
    {
        parent::__construct();
        $this->load->model("music");
        define('PERPAGE', 10);
        $this->load->helper('file');
        $this->load->library('zip');
    }

    function index_bg_add(){
      if($_POST){
        $img_url = trim($_POST['img_url']);
        $insert_data = array('field_one'=>$img_url,'type'=>'index_bg');
        $re = $this->music->insert('configs',$insert_data);

        if ($re){
          $result = array('success'=>'yes','msg'=>'操作成功');
        }else{
          $result = array('success'=>'no');
        }
        echo json_encode($result);exit;
      }else{
          $this->load->view('admin/index_bg_add');
      }

    }

    function index_bg_list(){
      $imgs = $this->music->select('configs','*',array('type'=>'index_bg'));
      $data['imgs'] = $imgs;
      $this->load->view('admin/index_bg_list',$data);
    }

    function student_comments(){
        $sql = "select class.numbe,class_course_teacher.lession_no,class_roll_call.homework_comment,student.name as student_name,teacher.name as teacher_name from teacher,student,class_roll_call";
        $sql.= ",class,class_course_teacher where class.id = class_roll_call.class_id and student.id = class_roll_call.student_id and ";
        $sql.= "class_roll_call.class_course_teacher_id = class_course_teacher.id and teacher.id = class_course_teacher.teacher_id";
        $sql.= " and class_roll_call.homework_comment != ''";
        $data['comments'] = $this->music->personal_select($sql);

        $this->load->view('admin/student_comments', $data);
    }


    function suggest_video_add(){

        $admin = $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $level_id = trim($_POST['level_id']);
            $name = trim($_POST['name']);
            $instrument_id = trim($_POST['instrument_id']);
            $lesson_num = trim($_POST['lesson_num']);
            $link = trim($_POST['link']);

            $data_array = array(
                    'level_id'=>$level_id,
                    'name'=>$name,
                    'lesson_num'=>$lesson_num,
                    'link'=>$link,
                    'instrument_id'=>$instrument_id
            );

            if ($id){
                $re = $this->music->update('suggest_video',$data_array,array('id'=>$id));
                $re = $id;
            }else{
                $re = $this->music->insert('suggest_video',$data_array);
            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no','msg'=>'保存失败');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $class = $this->music->select('suggest_video','*',array('id'=>$id));

            if($class){
                $data['class'] = $class[0];
            }else{
                $data['class'] = '';
            }
            $data['level']    = $this->music->select('base_info','name,id',array('type'=>'class_level'));
            $data['instrument'] = $this->music->select('base_info','*',array('type' => 'instrument'));
            $this->load->view('admin/suggest_video_add',$data);
        }
    }



    function suggest_video_list(){
        $this->top();
        $class = $this->music->select('suggest_video');
        foreach ($class as $key => $value) {

          $temp_level_name = $this->music->select('base_info','name',array('id'=>$value->level_id));
          if($temp_level_name){
            $level_name = $temp_level_name[0]->name;
          }else{
            $level_name = '';
          }
          $temp_instrument = $this->music->select('base_info','name',array('id'=>$value->instrument_id));
          if($temp_instrument){
            $temp_instrument_name = $temp_instrument[0]->name;
          }else{
            $temp_instrument_name = '';
          }
          $class[$key]->instrument = $temp_instrument_name;
          $class[$key]->level_name    = $level_name;
        }
        $data['class'] = $class;
        $this->load->view('admin/suggest_video_list',$data);
    }


    function suggest_song_add(){

        $admin = $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $level_id = trim($_POST['level_id']);
            $name = trim($_POST['name']);
            $band = trim($_POST['band']);
            $instrument_id = trim($_POST['instrument_id']);
            $style = trim($_POST['style']);
            $link = trim($_POST['link']);
            $original = trim($_POST['original']);

            $data_array = array(
                    'level_id'=>$level_id,
                    'name'=>$name,
                    'band'=>$band,
                    'style'=>$style,
                    'link'=>$link,
                    'instrument_id'=>$instrument_id,
                    'original'=>$original
            );

            if ($id){
                $re = $this->music->update('suggest_song',$data_array,array('id'=>$id));
                $re = $id;
            }else{
                $re = $this->music->insert('suggest_song',$data_array);
            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no','msg'=>'保存失败');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $class = $this->music->select('suggest_song','*',array('id'=>$id));

            if($class){
                $data['class'] = $class[0];
            }else{
                $data['class'] = '';
            }
            $data['level']    = $this->music->select('base_info','name,id',array('type'=>'class_level'));
            $data['instrument'] = $this->music->select('base_info','*',array('type' => 'instrument'));
            $this->load->view('admin/suggest_song_add',$data);
        }
    }



    function suggest_song_list(){
        $this->top();
        $class = $this->music->select('suggest_song');
        foreach ($class as $key => $value) {

          $temp_level_name = $this->music->select('base_info','name',array('id'=>$value->level_id));
          if($temp_level_name){
            $level_name = $temp_level_name[0]->name;
          }else{
            $level_name = '';
          }
          $temp_instrument = $this->music->select('base_info','name',array('id'=>$value->instrument_id));
          if($temp_instrument){
            $temp_instrument_name = $temp_instrument[0]->name;
          }else{
            $temp_instrument_name = '';
          }
          $class[$key]->instrument = $temp_instrument_name;
          $class[$key]->level_name    = $level_name;
        }
        $data['class'] = $class;
        $this->load->view('admin/suggest_song_list',$data);
    }


    function _course_map_plan_items($type){
     $config_items = array(
                'map'=>array('title'=>'Course Map','content'=>array('name'=>'内容(CH)','en_name'=>'内容(EN)')),
                'plan'=>array('title'=>'Course Plan','content'=>array('name'=>'内容(CH)','en_name'=>'内容(EN)')),
                'intro'=>array('title'=>'Course Intro','content'=>array('name'=>'内容(CH)','en_name'=>'内容(EN)'))
                );

     return $config_items[$type];

  }

    //lists of config items
  function course_map_plan_list(){
    $this->top();
    $type = $this->uri->segment(3);

    $config_item = $this->_course_map_plan_items($type);
    $data['title'] = $config_item['title'];

    $configs = $this->music->select('course_map_plan','*',array('type' => $type));
    foreach($configs as $k=>$v){

      $temp_level = $this->music->select('base_info','name',array('id'=>$v->instrument_id));
      if($temp_level){
        $configs[$k]->instrument_name = $temp_level[0]->name;
      }else{
        $configs[$k]->instrument_name = '';
      }
    }
    $data['course_map_plan'] = $configs;
    $data['type'] = $type;

    $this->load->view('admin/course_map_plan_list',$data);
  }


  //new one config item according to different type
  function course_map_plan_add(){
    $this->top();
    if($_POST){
      $type = trim($_POST['type']);
      $jc_id = intval($_POST['id']);
      $instrument_id = intval($_POST['instrument_id']);
      $data_array = array(
                    'url'=>trim($_POST['coverurl']),
                    'instrument_id'=>$instrument_id,
                    'file_name'=>trim($_POST['file_name']),
                    'type' => $type
            );
      if ($jc_id){
        $re = $this->music->update('course_map_plan',$data_array,array('id'=>$jc_id));
      }else{
        $re = $this->music->insert('course_map_plan',$data_array);

      }
      if ($re){
        $result = array('success'=>'yes','msg'=>'操作成功');
      }else{
        $result = array('success'=>'no');
      }
      echo json_encode($result);exit;

    }else{
      $type = $this->uri->segment(3);
      $config_id = $this->uri->segment(4);
      if($config_id){
        $config = $this->music->select('course_map_plan','*',array('id'=>$config_id));
        $data['instrument'] = $config[0];
      }else{
        $data['instrument'] = '';
      }
      $data['type'] = $type;
      $config_item = $this->_course_map_plan_items($type);
      $data['title'] = $config_item['title'];
      $data['instruments']    = $this->music->select('base_info','name,id',array('type'=>'instrument'));
      $this->load->view('admin/course_map_plan_add',$data);
    }
  }


  function _course_config_items($type){
     $config_items = array(
                'content'=>array('name'=>'内容(CH)','en_name'=>'内容(EN)'),
                'material'=>array('name'=>'材料(CH)','en_name'=>'材料(EN)'),
                'homework'=>array('name'=>'作业(CH)','en_name'=>'作业(EN)'),
                'suggest_song'=>array('name'=>'推荐歌曲(CH)','en_name'=>'推荐歌曲(EN)')
                );

     return $config_items[$type];

  }

    //lists of config items
  function course_config_list(){
    $this->top();
    $type = $this->uri->segment(3);

    $config_item = $this->_course_config_items($type);
    $data['config_item'] = $config_item;

    $configs = $this->music->select('course_config','*',array('type' => $type));
    foreach($configs as $k=>$v){
      $temp_teacher = $this->music->select('teacher','name',array('id'=>$v->teacher_id));
      if($temp_teacher){
        $configs[$k]->teacher_name = $temp_teacher[0]->name;
      }else{
        $configs[$k]->teacher_name = '';
      }
    }
    $data['instrument'] = $configs;
    $data['type'] = $type;

    $this->load->view('admin/course_config_list',$data);
  }


    //neW one config item according to different type
  function course_config_add(){
    $this->top();
    if($_POST){
      $type = trim($_POST['type']);
      $jc_id = intval($_POST['id']);
      $teacher_id = intval($_POST['teacher_id']);
      $data_array = array(
                    'name'=>trim($_POST['name']),
                    'en_name'=>trim($_POST['en_name']),
                    'teacher_id'=>$teacher_id,
                    'display_order'=>trim($_POST['display_order']),
                    'type' => $type
            );
      if ($jc_id){
        $re = $this->music->update('course_config',$data_array,array('id'=>$jc_id));
      }else{
        $re = $this->music->insert('course_config',$data_array);

      }
      if ($re){
        $result = array('success'=>'yes','msg'=>'操作成功');
      }else{
        $result = array('success'=>'no');
      }
      echo json_encode($result);exit;

    }else{
      $type = $this->uri->segment(3);
      $config_id = $this->uri->segment(4);
      if($config_id){
        $config = $this->music->select('course_config','*',array('id'=>$config_id));
        $data['instrument'] = $config[0];
      }else{
        $data['instrument'] = '';
      }
      $data['type'] = $type;
      $config_item = $this->_course_config_items($type);
      $data['config_item'] = $config_item;
      $data['teachers'] = $this->music->select('teacher','name,id');
      $this->load->view('admin/course_config_add',$data);
    }
  }



    function class_add(){

        $admin = $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $level_id = trim($_POST['level_id']);
            $category_id = trim($_POST['category_id']);
            $branch_id = trim($_POST['branch_id']);
            $tuition = trim($_POST['tuition']);
            $duration = trim($_POST['duration']);
            $instrument_id = trim($_POST['instrument_id']);
            $class_start_time = trim($_POST['class_start_time']);
            $class_end_time = trim($_POST['class_end_time']);
            $class_week = trim($_POST['class_week']);

            $data_array = array(
                    'level_id'=>$level_id,
                    'category_id'=>$category_id,
                    'branch_id'=>$branch_id,
                    'tuition'=>$tuition,
                    'duration'=>$duration,
                    'instrument_id'=>$instrument_id,
                    'class_start_time'=>$class_start_time,
                    'class_end_time'=>$class_end_time,
                    'class_week'=>$class_week
            );
            $temp_instrument = $this->music->select('base_info','first_label',array('id'=>$instrument_id));
            if($temp_instrument){
              $first_label = $temp_instrument[0]->first_label;
            }else{
              $first_label = 'C';
            }

            if ($id){
                $re = $this->music->update('class',$data_array,array('id'=>$id));
                $re = $id;
            }else{
                $data_array['numbe'] = $this->music->_generate_num('class',$first_label);
                $re = $this->music->insert('class',$data_array);
            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no','msg'=>'保存失败');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $class = $this->music->select('class','*',array('id'=>$id));

            if($class){
                $data['class'] = $class[0];
            }else{
                $data['class'] = '';
            }
            $data['level']    = $this->music->select('base_info','name,id',array('type'=>'class_level'));
            $data['category'] = $this->music->select('base_info','name,id',array('type'=>'class_cate'));
            $data['branch']   = $this->music->select('branch','name,id');
            $data['instrument'] = $this->music->select('base_info','*',array('type' => 'instrument'));
            $this->load->view('admin/class_add',$data);
        }
    }



    function class_list(){
        $this->top();
        $class = $this->music->select('class');
        foreach ($class as $key => $value) {
          $temp_category_name = $this->music->select('base_info','name',array('id'=>$value->category_id));
          if($temp_category_name){
            $category_name = $temp_category_name[0]->name;
          }else{
            $category_name = '';
          }

          $temp_level_name = $this->music->select('base_info','name',array('id'=>$value->level_id));
          if($temp_level_name){
            $level_name = $temp_level_name[0]->name;
          }else{
            $level_name = '';
          }
          $temp_branch_name = $this->music->select('branch','name',array('id'=>$value->branch_id));
          if($temp_branch_name){
            $branch_name = $temp_branch_name[0]->name;
          }else{
            $branch_name = '';
          }

          $temp_instrument = $this->music->select('base_info','name',array('id'=>$value->instrument_id));
          if($temp_instrument){
            $temp_instrument_name = $temp_instrument[0]->name;
          }else{
            $temp_instrument_name = '';
          }
          $class[$key]->instrument = $temp_instrument_name;
          $class[$key]->category_name = $category_name;
          $class[$key]->level_name    = $level_name;
          $class[$key]->branch_name   = $branch_name;
        }
        $data['class'] = $class;
        $this->load->view('admin/class_list',$data);
    }



    function branch_add(){

        $admin = $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);
            $mobile = trim($_POST['mobile']);
            $desc = trim($_POST['desc']);
            $en_desc = trim($_POST['en_desc']);
            $en_address = trim($_POST['en_address']);
            $pics = trim($_POST['pics']);
            $name = trim($_POST['name']);
            $en_name = trim($_POST['en_name']);
            $email = trim($_POST['email']);

            $data_array = array(
                    'phone'=>$phone,
                    'mobile'=>$mobile,
                    'desc'=>$desc,
                    'en_desc'=>$en_desc,
                    'address'=>$address,
                    'en_address'=>$en_address,
                    'pics'=>$pics,
                    'name'=>$name,
                    'email'=>$email,
                    'en_name'=>$en_name,
                );

            if ($id){
                $re = $this->music->update('branch',$data_array,array('id'=>$id));
                $re = $id;
            }else{
                $data_array['numbe'] = $this->music->_generate_num('branch','B');
                $re = $this->music->insert('branch',$data_array);
            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no','msg'=>'保存失败');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $branch = $this->music->select('branch','*',array('id'=>$id));

            if($branch){
                $data['branch'] = $branch[0];
            }else{
                $data['branch'] = '';
            }

            $this->load->view('admin/branch_add',$data);
        }
    }



    function branch_list(){
        $this->top();
        $data['branchs'] = $this->music->select('branch','*');
        $this->load->view('admin/branch_list',$data);
    }

    function listen_list(){
        $this->top();
        $data['history'] = $this->music->select('listen_history','*');
        $this->load->view('admin/listen_list',$data);
    }


  function _config_items($type){
     $config_items = array(
                'instrument'=>array('name'=>'乐器名(CH)','en_name'=>'乐器名(EN)'),
                'class_cate'=>array('name'=>'分类名(CH)','en_name'=>'分类名(EN)'),
                'class_level'=>array('name'=>'级别名(CH)','en_name'=>'级别名(EN)')
                );

     return $config_items[$type];

  }

    //lists of config items
  function base_info_list(){
    $this->top();
    $type = $this->uri->segment(3);

    $config_item = $this->_config_items($type);
    $data['config_item'] = $config_item;

    $configs = $this->music->select('base_info','*',array('type' => $type));
    $data['instrument'] = $configs;
    $data['type'] = $type;

    $this->load->view('admin/base_info_list',$data);
  }


    //neW one config item according to different type
  function base_info_add(){
    $this->top();
    if($_POST){
      $type = trim($_POST['type']);
      $jc_id = intval($_POST['id']);
      $data_array = array(
                    'name'=>trim($_POST['name']),
                    'en_name'=>trim($_POST['en_name']),
                    'display_order'=>trim($_POST['display_order']),
                    'type' => $type,
                    'first_label'=>trim($_POST['first_label'])
            );
      if ($jc_id){
        $re = $this->music->update('base_info',$data_array,array('id'=>$jc_id));
      }else{
        $re = $this->music->insert('base_info',$data_array);

      }
      if ($re){
        $result = array('success'=>'yes','msg'=>'操作成功');
      }else{
        $result = array('success'=>'no');
      }
      echo json_encode($result);exit;

    }else{
      $type = $this->uri->segment(3);
      $config_id = $this->uri->segment(4);
      if($config_id){
        $config = $this->music->select('base_info','*',array('id'=>$config_id));
        $data['instrument'] = $config[0];
      }else{
        $data['instrument'] = '';
      }
      $data['type'] = $type;
      $config_item = $this->_config_items($type);
      $data['config_item'] = $config_item;
      $this->load->view('admin/base_info_add',$data);
    }
  }


    function teacher_add(){

        $admin = $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $gender = trim($_POST['gender']);
            $password = trim($_POST['password']);
            $mobile = trim($_POST['mobile']);
            $desc = trim($_POST['desc']);
            $en_desc = trim($_POST['en_desc']);
            $lang = trim($_POST['lang']);
            $instrument = rtrim($_POST['instrument'],',');
            $thumb = trim($_POST['thumb']);
            $name = trim($_POST['name']);
            $en_name = trim($_POST['en_name']);
            $username = trim($_POST['username']);
            $en_country = trim($_POST['en_country']);
            $country = trim($_POST['country']);
            $email = trim($_POST['email']);

            $mobile_check = $this->music->select('teacher','id',array('mobile'=>$mobile));
            if($mobile_check){
               if($id){
                  if($mobile_check[0]->id != $id){
                    echo json_encode(array('success'=>'no','msg'=>'手机号已被别人使用，请确认'));exit;
                  }
               }else{
                 echo json_encode(array('success'=>'no','msg'=>'手机号已被别人使用，请确认'));exit;
               }
            }

            $data_array = array(
                    'gender'=>$gender,
                    'mobile'=>$mobile,
                    'desc'=>$desc,
                    'en_desc'=>$en_desc,
                    'lang'=>$lang,
                    'instrument'=>$instrument,
                    'thumb'=>$thumb,
                    'name'=>$name,
                    'en_name'=>$en_name,
                    'username'=>$username,
                    'en_country'=>$en_country,
                    'country'=>$country,
                    'email'=>$email
                );
            if($password){
              $data_array['password'] = md5(md5($password.'music'));
            }
            if ($id){
                $re = $this->music->update('teacher',$data_array,array('id'=>$id));
                $re = $id;
            }else{
                $data_array['numbe'] = $this->music->_generate_num('teacher','T');
                $data_array['created'] = date('Y-m-d H:i:s');
                $re = $this->music->insert('teacher',$data_array);
            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no','msg'=>'保存失败');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $teacher = $this->music->select('teacher','*',array('id'=>$id));

            if($teacher){
                $teacher = $teacher[0];
                $data['instrument_select'] = explode(',', $teacher->instrument);
                $data['teacher'] = $teacher;
            }else{
                $data['teacher'] = '';
            }

            $data['instrument'] = $this->music->select('base_info','*',array('type'=>'instrument'));
            $this->load->view('admin/teacher_add',$data);
        }
    }



    function teacher_list(){
        $this->top();
        $teachers = $this->music->select('teacher','*');
        foreach ($teachers as $key => $value) {
           $instrument = explode(',', $value->instrument);
           $instrument_info = '';
           foreach($instrument as $k=>$v){
             $temp_instr = $this->music->select('base_info','name',array('id'=>$v));
             if($temp_instr){
               $instrument_info.=$temp_instr[0]->name.',';
             }
           }
           $teachers[$key]->instrument = rtrim($instrument_info,',');
        }
        $data['teachers'] = $teachers;

        $this->load->view('admin/teacher_list',$data);
    }


    function student_roll_call(){
      $this->top();
        $student_id = $this->uri->segment(3);
        $class_id = $this->uri->segment(4);

        $class_record = $this->music->select('class_course_teacher','*',array('class_id'=>$class_id));
        $temp_class = $this->music->select('class','*',array('id'=>$class_id));
        $data['class_info'] = $temp_class[0];

        $temp_instrument = $this->music->select('base_info','name',array('id'=>$temp_class[0]->instrument_id));
        $data['instrument_name'] = $temp_instrument[0]->name;

        $temp_student = $this->music->select('student','name',array('id'=>$student_id));
        $data['student_name'] = $temp_student[0]->name;

        foreach($class_record as $k=>$v){

            $roll_call = $this->music->select('class_roll_call','id',array('student_id'=>$student_id,'class_course_teacher_id'=>$v->id));
            if($roll_call){
              $roll_call_flag = 1;
            }else{
              $roll_call_flag = 0;
            }
            $class_record[$k]->roll_flag = $roll_call_flag;
        }
        $data['class_record'] = $class_record;
        $this->load->view('admin/student_roll_call',$data);
    }

    public function student_list(){
        $this->top();
        $flag = $this->uri->segment(3);
        $student_pay = $this->music->select('student_class_order','student_id',array('status'=>20));
        $student_id = '';
        if($student_pay){
          $temp_array = array();
          foreach($student_pay as $k=>$v){
            if(!in_array($v->student_id, $temp_array)){
                $student_id .= $v->student_id.',';
                $temp_array[] = $v->student_id;
            }

          }
          $student_id = rtrim($student_id,',');
        }
        $sql = "select * from student ";
        if($flag == 1){
          if($student_id){

           $sql.=" where id not in (".$student_id.")";
          }
        }else if($flag == 2){
          if(!$student_id){
            $student_id = '9999999';
          }
          $sql.=" where id  in (".$student_id.")";
        }
        $students = $this->music->personal_select($sql);
        foreach($students as $k=>$v){
            $sql = "select base_info.name as instrument_name,class.numbe as class_numbe from base_info,student_class_order,class where base_info.id = class.instrument_id and class.id = student_class_order.class_id";
            $sql.=" and student_class_order.student_id = ".$v->id;

            $temp_instrument = $this->music->personal_select($sql);
            $instrument_name = '';
            $class_numbe = '';
            foreach($temp_instrument as $key=>$val){
              $instrument_name = $val->instrument_name.';';
              $class_numbe = $val->class_numbe.';';
            }
            $students[$k]->instrument_name = rtrim($instrument_name,';');
            $students[$k]->class_numbe = rtrim($class_numbe,';');
        }
        $data['students'] = $students;
        $this->load->view('admin/student_list',$data);
    }


    public function export(){

        //Get all datas:model,category,version,receive,send
         $result = $this->music->select('admin','*');
         $file_name = array();

         $name = $this->generate_excel($result);
         $file_name[]=$name;
         $this->zip->read_file($name);
         $this->zip->download('back_up.zip');

  }

  public function generate_excel($result){
    require_once APPPATH.'libraries/PHPExcel/PHPExcel.php';
    require_once APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Office 2007 XLSX Test Document")
    ->setSubject("Office 2007 XLSX Test Document")
    ->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");

    //First sheet started
    $objPHPExcel->setActiveSheetIndex(0);
    $objRichText = new PHPExcel_RichText();
    $objRichText->createText('');
    $objPayable = $objRichText->createTextRun('PHP导出的Excel');

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);

    $objPHPExcel->getActiveSheet()->setCellValue('A1', '用户名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '手机号');

    $i=2;
    foreach($result as $k=>$val){
      $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$i, $val->username,PHPExcel_Cell_DataType::TYPE_STRING);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $val->mobile);
      $i++;
    }
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle('Adminlist');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    date_default_timezone_set('Asia/Chongqing');
    $name = 'asset/excel/export_'.$mod_name.date('Y.m.d h.i.s').'.xls';
    $name = iconv('utf-8','gb2312',$name);
    $objWriter->save($name);
    return $name;
  }


    function single_list(){
        $this->top();
        $cond = '';

        $total = $this->music->select_count_where('materials',array('type'=>'single'));
        $page = $this->uri->segment(3);

        $perpage = PERPAGE;
        if($page==""){
          $page=1;
        }
        $start = ($page-1)*$perpage;

        $data['total'] = ceil($total/$perpage);
        $data['start']=$start;
        $data['current_page'] = $page;
        $materials = $this->music->select('materials','*',array('type'=>'single'),$perpage,$start);

        $data['materials'] = $materials;

        $this->load->view('admin/single_list',$data);
    }


    function multiply_list(){
        $this->top();
        $cond = '';

        $total = $this->music->select_count_where('materials',array('type'=>'multiply'));
        $page = $this->uri->segment(3);

        $perpage = PERPAGE;
        if($page==""){
          $page=1;
        }
        $start = ($page-1)*$perpage;

        $data['total'] = ceil($total/$perpage);
        $data['start']=$start;
        $data['current_page'] = $page;
        $materials = $this->music->select('materials','*',array('type'=>'multiply'),$perpage,$start,array('id'=>'desc'));
        foreach ($materials as $key => $value) {
            $tem = array();
            $temp_title = explode('|||',$value->title);
            $temp_content = explode('|||',$value->content);
            $temp_sourceurl = explode('|||',$value->source_url);
            $temp_coverurl = explode('|||',$value->coverurl);
            foreach($temp_title as $k=>$v){
              $tem[] = array(
                'title'=>$v,
                'content'=>isset($temp_content[$k])?$temp_content[$k]:'',
                'source_url'=>isset($temp_sourceurl[$k])?$temp_sourceurl[$k]:'',
                'coverurl' =>isset($temp_coverurl[$k])?$temp_coverurl[$k]:''
                );
            }
            $materials[$key]->content = $tem;
        }
        $data['materials'] = $materials;
        $this->load->view('admin/multiply_list',$data);
    }




/*
 * single_material_manage
 */
   function single_material_manage(){
          $this->top();
        if ($_POST){

           $db_data = array(
                          'title'=>trim($_POST['title']),
                          'content'=>trim($_POST['maincontent']),
                          'source_url'=>trim($_POST['source_url']),
                          'coverurl'=>trim($_POST['coverurl']),
                          'summary'=>trim($_POST['summary']),
                          'createtime'=>date('Y-m-d H:i:s'),
                          'keywords'=>trim($_POST['keywords'])
            );

           if(trim($_POST['m_id']) == 'first_follow'){
              $db_data['type'] = 0;
              $this->music->delete('configs',array('type'=>'first_follow'));
              $this->music->delete('materials',array('keywords'=>'first_follow'));
           }else{
              $db_data['type'] = 'single';
           }
           if (trim($_POST['m_id'])&&($_POST['m_id'] != 'first_follow')){
             $re = $this->music->update('materials',$db_data,array('id'=>intval($_POST['m_id'])));
           }else{

            $re = $this->music->insert('materials',$db_data);

           }
           if ($re){
             $r = array('success'=>'yes');
           }else{
             $r = array('success'=>'no');
           }
           echo json_encode($r);exit;


        }else {
           $m_id = $this->uri->segment(3);

           $data = array();
           if ($m_id){
             if($m_id == 'first_follow'){
                $material = $this->music->select('materials','*',array('keywords'=>'first_follow','type'=>0));
                if($material){
                    $data['material'] = $material[0]->content;

                }else{
                    $data['material'] = '';
                }
                $data['keywords'] = 'first_follow';
             }else{
                $material = $this->music->select('materials','*',array('id'=>$m_id));
                $data['keywords'] = $material[0]->keywords;
                    $data['material'] = $material[0];
             }

           }else{
              $data['keywords'] = '';
             $data['material'] = '';
           }
           $data['m_id'] = $m_id;
           $this->load->view('admin/single_material_manage',$data);
        }
    }

/*
 * multiply_material_manage
 */function multiply_material_manage(){
           $this->top();
        if ($_POST){
           $pos = json_decode($_POST['jsonData']);
           $data = array();
           $title = '';
           $content = '';
           $sourceurl = '';
           $coverurl = '';
          foreach ($pos as $k=>$val){
              $post = (array)$val;
              if($post['title']){
                $title.=trim($post['title']).'|||';
                $content.=trim($post['content']).'|||';
                $sourceurl.=trim($post['sourceurl']).'|||';
                $coverurl.=trim($post['cover']).'|||';

              }
          }

          $title = rtrim($title, '|||');
          $content = rtrim($content, '|||');
          $sourceurl = rtrim($sourceurl, '|||');
          $coverurl = rtrim($coverurl, '|||');
           $db_data = array(
            'content'=>$content,
            'title'=>$title,
            'source_url'=>$sourceurl,
            'coverurl' =>$coverurl,
            'createtime'=>date('Y-m-d H:i:s'),
            'keywords'=>trim($_POST['keywords'])
            );
           if(trim($_POST['m_id']) == 'first_follow'){
              $db_data['type'] = 1;
              $this->music->delete('configs',array('type'=>'first_follow'));
              $this->music->delete('materials',array('keywords'=>'first_follow'));
           }else{
              $db_data['type'] = 'multiply';
           }
           if (trim($_POST['m_id'])&&($_POST['m_id'] != 'first_follow')){
             $re = $this->music->update('materials',$db_data,array('id'=>intval($_POST['m_id'])));
           }else{

            $re = $this->music->insert('materials',$db_data);

           }

           if ($re){
             $r = array('success'=>'yes');
           }else{
             $r = array('success'=>'no');
           }
           echo json_encode($r);exit;

        }else{

          $m_id = $this->uri->segment(3);
          $data = array();
           if ($m_id){
             if($m_id == 'first_follow'){
                $material = $this->music->select('materials','*',array('keywords'=>'first_follow','type'=>1));
                if($material){
                  $material = $material[0];
                  $tem = array();
                  $temp_title = explode('|||',$material->title);
                  $temp_content = explode('|||',$material->content);
                  $temp_sourceurl = explode('|||',$material->source_url);
                  $temp_coverurl = explode('|||',$material->coverurl);
                  foreach($temp_title as $k=>$v){
                    $tem[] = array(
                      'title'=>$v,
                      'content'=>isset($temp_content[$k])?$temp_content[$k]:'',
                      'source_url'=>isset($temp_sourceurl[$k])?$temp_sourceurl[$k]:'',
                      'coverurl' =>isset($temp_coverurl[$k])?$temp_coverurl[$k]:''
                      );
                  }
                  $data['createtime'] = $material->createtime;
                  $data['material'] = $tem;
                }else{
                    $data['material'] = '';
                }
                $data['keywords'] = 'first_follow';
             }else{
                $material = $this->music->select('materials','*',array('id'=>$m_id));
                $material = $material[0];
                  $tem = array();
                  $temp_title = explode('|||',$material->title);
                  $temp_content = explode('|||',$material->content);
                  $temp_sourceurl = explode('|||',$material->source_url);
                  $temp_coverurl = explode('|||',$material->coverurl);
                  foreach($temp_title as $k=>$v){
                    $tem[] = array(
                      'title'=>$v,
                      'content'=>isset($temp_content[$k])?$temp_content[$k]:'',
                      'source_url'=>isset($temp_sourceurl[$k])?$temp_sourceurl[$k]:'',
                      'coverurl' =>isset($temp_coverurl[$k])?$temp_coverurl[$k]:''
                      );
                  }
                $data['material'] = $tem;
                $data['createtime'] = $material->createtime;
                $data['keywords'] = $material->keywords;
             }
           }else{
             $data['material'] = '';
             $data['keywords'] = '';
           }
          $data['m_id'] = $m_id;
          $this->load->view('admin/multiply_material_manage',$data);
        }
    }


    function first_follow(){
        $this->top();
        if($_POST){
            $config = $this->music->select('configs','*',array('type'=>'first_follow'));
            $content = trim($_POST['content']);
            $data_array = array('type'=>'first_follow','field_one'=>$content);
            $this->music->delete('materials',array('keywords'=>'first_follow'));
            if ($config){
                $re = $this->music->update('configs',$data_array,array('type'=>'first_follow'));
            }else{

                $re = $this->music->insert('configs',$data_array);

            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no');
            }
            echo json_encode($result);exit;

        }else{
            $config = $this->music->select('configs','*',array('type'=>'first_follow'));

            if($config){
                $data['config'] = $config[0];
            }else{
                $data['config'] = '';
            }

            $this->load->view('admin/first_follow',$data);
        }
    }

    function brief_info(){
        $this->top();
        $type = $this->uri->segment(3);
        if($_POST){
            $config = $this->music->select('brief_info','*',array('cate'=>$type));
            $desc = trim($_POST['desc']);
            $en_desc = trim($_POST['en_desc']);
            $type = trim($_POST['type']);
            $data_array = array('cate'=>$type,'en_desc'=>$en_desc,'desc'=>trim($_POST['desc']));

            if ($config){
                $re = $this->music->update('brief_info',$data_array,array('cate'=>$type));
            }else{
                $re = $this->music->insert('brief_info',$data_array);

            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no');
            }
            echo json_encode($result);exit;

        }else{
            $config = $this->music->select('brief_info','*',array('cate'=>$type));

            if($config){
                $data['brief_info'] = $config[0];
            }else{
                $data['brief_info'] = '';
            }
            $data['type'] = $type;
            $this->load->view('admin/brief_info',$data);
        }
    }

    function index_bottom(){
        $this->top();
        if($_POST){
            $config = $this->music->select('configs','*',array('type'=>'index_bottom'));
            $content = trim($_POST['content']);
            $data_array = array('type'=>'index_bottom','field_one'=>$content);
            if ($config){
                $re = $this->music->update('configs',$data_array,array('type'=>'index_bottom'));
            }else{
                $re = $this->music->insert('configs',$data_array);

            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no');
            }
            echo json_encode($result);exit;

        }else{
            $config = $this->music->select('configs','*',array('type'=>'index_bottom'));

            if($config){
                $data['config'] = $config[0];
            }else{
                $data['config'] = '';
            }

            $this->load->view('admin/index_bottom',$data);
        }
    }


    function wechat_config(){
        $this->top();
        if($_POST){
            $config = $this->music->select('configs','*',array('type'=>'weichat_config'));
            $content = trim($_POST['content']);
            $data_array = array('type'=>'weichat_config','field_one'=>$content,'field_two'=>trim($_POST['phone']));
            if ($config){
                $re = $this->music->update('configs',$data_array,array('type'=>'weichat_config'));
            }else{
                $re = $this->music->insert('configs',$data_array);

            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no');
            }
            echo json_encode($result);exit;

        }else{
            $config = $this->music->select('configs','*',array('type'=>'weichat_config'));

            if($config){
                $data['config'] = $config[0];
            }else{
                $data['config'] = '';
            }

            $this->load->view('admin/wechat_config',$data);
        }
    }

  public function _get_request($url){

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            $jsoninfo = json_decode($output, true);
            //$token = $jsoninfo["access_token"];


        return $jsoninfo;
    }

    private function get_access_token(){
        $wei_config = $this->music->select('configs','*',array('type'=>'weichat_config'));
        $weiconfig = $wei_config[0];

        $appid = trim($weiconfig->field_one);
        $appsecret = trim($weiconfig->field_two);

        $this->tokenFile= 'asset/token_login.txt';
        $this->lastTimeFile= 'asset/token_last.txt';
        /*$this->cookieFile =  $touser.'_cookie.txt';

        if(!file_exists($this->cookieFile)){
            $fh = fopen($this->cookieFile,"w");
            fclose($fh);
        }*/

        if(!file_exists($this->tokenFile)){
            $fh = fopen($this->tokenFile,"w");
            fclose($fh);
        }

        if(!file_exists($this->lastTimeFile)){
            $fh = fopen($this->lastTimeFile,"w");
            fclose($fh);
        }

        $needLogin=true;
        $nowTime=time();
        if($lastTime=file_get_contents($this->lastTimeFile)){

        }else{
            $lastTime=0;
        }

        if(($nowTime-$lastTime)<=$this->expire){
            $needLogin=false;
        }

        if($needLogin==true){

            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;

            $token = $this->_get_request($url);
            $token = $token['access_token'];
            if($token){
                file_put_contents($this->lastTimeFile,$nowTime);
                file_put_contents($this->tokenFile,$token);
                $this->token=$token;
                return array('access_token'=>$token);
            }else{
                return false;
            }
        }else{
            if($token=file_get_contents($this->tokenFile)){
                $this->token=$token;
                return array('access_token'=>$token);
            }else{
                return false;
            }
        }
    }


    //创建自定义菜单
    public function create_menu($data,$url= "api.weixin.qq.com"){
        $arr = $this->get_access_token();
        if($arr['access_token']){
            $ACCESS_TOKEN=$arr['access_token'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://".$url."/cgi-bin/menu/create?access_token={$ACCESS_TOKEN}");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $tmpInfo = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Errno'.curl_error($ch);
            }
            curl_close($ch);
            return json_decode($tmpInfo,1);
        }else{
            return $arr;
        }
    }

    //查询自定义菜单
    public function get_menu(){
        $arr = $this->get_access_token();
        if($arr['access_token']){
            $ACCESS_TOKEN=$arr['access_token'];
            $url="https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$ACCESS_TOKEN;
            $arr = json_decode(file_get_contents($url),1);
            return $arr;
        }else{
            return $arr;
        }
    }


    function menu_update(){
        $this->top();
        $menus = $this->music->select('menu_settings','*',array('tms_main_id'=>'0'));
        foreach($menus as $k=>$v){
            $data['button'][$k]['name']=urlencode($v->tms_main_menu);
            if($v->tms_main_type=='click'){
                $data['button'][$k]['type']='click';
                $data['button'][$k]['key']=urlencode($v->tms_main_key);

            }

            if($v->tms_main_type=='view'){
                $data['button'][$k]['type']='view';
                $data['button'][$k]['url']=urlencode($v->tms_main_url);
            }

            $list[$k]['son']=$this->music->select('menu_settings','*',array('tms_main_id'=>$v->id));

            foreach($list[$k]['son'] as $key=>$value){
                $data['button'][$k]['sub_button'][$key]['name']=urlencode($value->tms_sub_menu);
                if($value->tms_sub_type=='click'){
                    $data['button'][$k]['sub_button'][$key]['type']='click';
                    $data['button'][$k]['sub_button'][$key]['key']=urlencode($value->tms_sub_key);
                }
                if($value->tms_sub_type=='view'){
                    $data['button'][$k]['sub_button'][$key]['type']='view';
                    $data['button'][$k]['sub_button'][$key]['url']=urlencode($value->tms_sub_url);
                }
            }
        }

        $re = $this->del_menu();
        if($re){
            $return=$this->create_menu(urldecode(json_encode($data)));


            if ($return['errmsg']=='ok'){
                $re = json_encode(array('success'=>'yes','msg'=>'Success'));
            }else{
                $re = json_encode(array('success'=>'no','msg'=>$return['errmsg']));
            }
            echo $re;
            exit;
        }
    }

    //删除自定义菜单
    public function del_menu($url= "api.weixin.qq.com"){
        $arr = $this->get_access_token();
        if($arr['access_token']){
            $ACCESS_TOKEN=$arr['access_token'];
            $url="https://".$url."/cgi-bin/menu/delete?access_token=".$ACCESS_TOKEN;
            $arr = json_decode(file_get_contents($url),1);
            return $arr;
        }else{
            return $arr;
        }
    }




    function menu_settings_delete(){
        $id = $this->uri->segment(3);
        $this->music->delete('menu_settings',array('tms_main_id'=>$id));
        $this->music->delete('menu_settings',array('id'=>$id));
        redirect('admin/menu_settings_index');
    }


    function sub_menu_settings_add(){
        $this->top();
        if ($_POST) {
            $id = intval($_POST['me_id']);
            $p_id = intval($_POST['main_id']);
            $da = array(
                    'tms_main_menu'=>trim($_POST['main_menu']),
                    'tms_main_key'=>trim($_POST['main_key']),
                    'tms_sub_menu'=>trim($_POST['sub_menu']),
                    'tms_sub_key'=>trim($_POST['sub_key']),
                    'tms_sub_url'=>trim($_POST['sub_url']),
                    'tms_sub_type'=>trim($_POST['sub_type']),
                    'tms_main_id'=>$p_id
            );
            if ($id){
                $re = $this->music->update('menu_settings',$da,array('id'=>$id));
            }else{
                $re = $this->music->insert('menu_settings',$da);
            }
            $re = array('success'=>'yes','msg'=>'操作成功');
            echo json_encode($re);exit;
        }else{
            $p_id = $this->uri->segment(3);
            $data = array();
            if ($p_id){
                $menu_settings = $this->music->select('menu_settings','*',array('id'=>$p_id));
                $menu_settings = $menu_settings[0];

                if ($menu_settings->tms_main_id=='0'){
                    $data['main_id'] = $menu_settings->id;
                    $data['me_id'] = 0;
                    $data['main_menu_settings'] = $menu_settings;
                }else{
                    $main_menu_settings = $this->music->select('menu_settings','*',array('id'=>$menu_settings->tms_main_id));
                    $data['main_id'] = $menu_settings->tms_main_id;
                    $data['me_id'] = $menu_settings->id;
                    $data['main_menu_settings'] = $main_menu_settings[0];
                }
                $data['menu_settings'] = $menu_settings;
            }
            $this->load->view('admin/sub_menu_settings_add',$data);
        }
    }


    function menu_settings_add(){
        $this->top();
        $id = $this->uri->segment(3);
        if ($_POST){
            $main_id = intval($_POST['main_id']);

            $da = array(
                    'tms_main_menu'=>trim($_POST['main_menu']),
                    'tms_main_type'=>trim($_POST['main_type']),
                    'tms_main_url'=>trim($_POST['main_url']),
                    'tms_main_key'=>trim($_POST['main_key'])
            );

            if ($main_id){
                $re = $this->music->update('menu_settings',$da,array('id'=>$main_id));
            }else{
                $re = $this->music->insert('menu_settings',$da);
            }
            $re = array('success'=>'yes','msg'=>'操作成功');
            echo json_encode($re);exit;
        }else{
            $data = array();
            if ($id){
                $menu_settings = $this->music->select('menu_settings','*',array('id'=>$id));
                $menu_settings = $menu_settings[0];
                $data['menu_settings'] = $menu_settings;
            }
            $this->load->view('admin/menu_settings_add',$data);
        }
    }



    function menu_settings_index(){
        $this->top();
        $data['menu_settings'] = $this->music->select('menu_settings');

        $this->load->view('admin/menu_settings_index',$data);
    }



   function student_search(){
      if($_POST){
        $keywords = trim($_POST['keywords']);
        $sql = "select `id` from student where numbe like '%".$keywords."%' or name like '%".$keywords."%' or mobile like '%".$keywords."%' or parents_name='%".$keywords."%'";
        $result = $this->music->personal_select($sql);
        if($result){
          echo json_encode(array('success'=>'yes','student_id'=>$result[0]->id));exit;
        }else{
          echo json_encode(array('success'=>'no','msg'=>'用户不存在，请确认'));exit;
        }
      }else{
        $this->load->view('admin/student_search');
      }
   }


   function student_get_course(){
      if($_POST){
        $course_num = intval($_POST['course_num']);
        $student_course_id = intval($_POST['student_course_id']);
        $student_id = intval($_POST['student_id']);

        $data_check = $this->music->select('student_course','*',array('id'=>$student_course_id,'student_id'=>$student_id));
        if(!$data_check){
          echo json_encode(array('success'=>'no','msg'=>'学员未选此课程'));exit;
        }else{
          $course = $data_check[0];
          $left_num = $course->left_num - $course_num;
          if($left_num < 0){
            echo json_encode(array('success'=>'no','msg'=>'学员课时不足'));exit;
          }
          $this->music->translate_begin();
          $already_num = $course->already_num+$course_num;
          $update_data = array('left_num'=>$left_num,'already_num'=>$already_num);
          $update_result = $this->music->update('student_course',$update_data,array('id'=>$course->id));

          $student_course_log_data = array(
                'student_id'=>$student_id,
                'course_id'=>$course->course_id,
                'course_num'=>$course_num,
                'created'=>date('Y-m-d H:i:s'),
                'left_num'=>$left_num
            );
          $log_result = $this->music->insert('student_course_log',$student_course_log_data);

          if($update_result && $log_result){
            $this->music->translate_commit();
            echo json_encode(array('success'=>'yes','msg'=>'操作成功'));exit;
          }else{
            $this->music->trans_rollback();
            echo json_encode(array('success'=>'no','msg'=>'操作失败'));exit;
          }
        }

      }else{
        $student_id = $this->uri->segment(3);
        $sql = "select course.name as course_name,course.start_date,course.end_date,course.money,student.name,student_course.*";
        $sql.=" from course,student,student_course where course.id = student_course.course_id and student.id = student_course.student_id and student_course.student_id = ".$student_id;
        $data['data'] = $this->music->personal_select($sql);
        $data['student_id'] = $student_id;
        $this->load->view('admin/student_get_course',$data);
      }
    }

    function student_picked_course(){
      $student_id = $this->uri->segment(3);
      $sql = "select course.name as course_name,course.start_date,course.end_date,course.money,student.name,student_course.*";
      $sql.=" from course,student,student_course where course.id = student_course.course_id and student.id = student_course.student_id and student_course.student_id = ".$student_id;
      $data['data'] = $this->music->personal_select($sql);
      $this->load->view('admin/student_picked_course',$data);
    }

    function student_pick_course(){
       if($_POST){
        if(isset($_POST['keywords'])){
          $keywords = trim($_POST['keywords']);
          $this->session->set_userdata('student_course_keywords',$keywords);
          echo json_encode(array('success'=>'yes'));exit;
        }
        if(isset($_POST['course_id'])){
           $course_id = rtrim($_POST['course_id'],',');
           $course_id = explode(',', $course_id);
           $student_id = intval($_POST['student_id']);
           foreach($course_id as $k=>$v){
              $temp_course = $this->music->select('course','course_num',array('id'=>$v));
              $course_check = $this->music->select('student_course','id',array('course_id'=>$v,'student_id'=>$student_id));
              if($temp_course && !$course_check){
                 $student_course_data = array(
                      'student_id'=>$student_id,
                      'course_id'=>$v,
                      'created'=>date('Y-m-d H:i:s'),
                      'course_num'=>$temp_course[0]->course_num,
                      'left_num'=>$temp_course[0]->course_num,
                      'already_num'=>0
                );
                 $this->music->insert('student_course', $student_course_data);
               }
           }
           echo json_encode(array('success'=>'yes','msg'=>'选课成功'));exit;
        }

       }else{
        $data['student_id'] = $this->uri->segment(3);
        $search_keywords = $this->session->userdata('student_course_keywords');
        $like = array();
        if($search_keywords){
          $like['name'] = $search_keywords;
        }
        $data['keywords'] = $search_keywords;
        $data['course'] = $this->music->select('course','*',array('end_date >'=>date('Y-m-d')),'','','',$like);
        $this->load->view('admin/student_pick_course', $data);
       }
    }


     function student_add(){

        $admin = $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $gender = trim($_POST['gender']);
            $password = trim($_POST['password']);
            $mobile = trim($_POST['mobile']);
            $email = trim($_POST['email']);
            $username = trim($_POST['username']);
            $en_name = trim($_POST['en_name']);
            $name = trim($_POST['name']);
            $mobile_check = $this->music->select('student','id',array('mobile'=>$mobile));
            if($mobile_check){
               if($id){
                  if($mobile_check[0]->id != $id){
                    echo json_encode(array('success'=>'no','msg'=>'手机号已被别人使用，请确认'));exit;
                  }
               }else{
                 echo json_encode(array('success'=>'no','msg'=>'手机号已被别人使用，请确认'));exit;
               }
            }

            $email_check = $this->music->select('student','id',array('email'=>$email));
            if($email_check && ($email_check[0]->id != $id)){
               if($id){
                  if($mobile_check[0]->id != $id){
                    echo json_encode(array('success'=>'no','msg'=>'Email已被别人使用，请确认'));exit;
                  }
               }else{
                 echo json_encode(array('success'=>'no','msg'=>'Email已被别人使用，请确认'));exit;
               }
            }

            $data_array = array(
                    'gender'=>$gender,
                    'mobile'=>$mobile,
                    'email'=>$email,
                    'en_name'=>$en_name,
                    'name'=>$name,
                    'username'=>$username
                );
            if($password){
              $data_array['password'] = md5(md5($password.'music'));
            }
            if ($id){
                $re = $this->music->update('student',$data_array,array('id'=>$id));
                $re = $id;
            }else{
                $data_array['numbe'] = $this->music->_generate_num('student','M');
                $re = $this->music->insert('student',$data_array);
            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $student = $this->music->select('student','*',array('id'=>$id));

            if($student){
                $data['student'] = $student[0];

            }else{
                $data['student'] = '';
            }
            $this->load->view('admin/student_add',$data);
        }
    }

    function event_add(){
        $admin = $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $name = trim($_POST['name']);
            $en_name = trim($_POST['en_name']);
            $desc = trim($_POST['desc']);
            $en_desc = trim($_POST['en_desc']);
            $event_type = trim($_POST['event_type']);
            $event_url = trim($_POST['event_url']);
            $display_order = trim($_POST['display_order']);
            $is_top = intval($_POST['is_top']);
            $data_array = array(
                                'en_name'=>$en_name,
                                'name'=>$name,
                                'desc'=>$desc,
                                'event_type'=>$event_type,
                                'event_img'=>$event_url,
                                'en_desc'=>$en_desc,
                                'is_top'=>$is_top,
                                'display_order'=>$display_order
                                );
            if ($id){
                $re = $this->music->update('event',$data_array,array('id'=>$id));

            }else{
                $data_array['createtime'] = date('Y-m-d H:i:s');
                $re = $this->music->insert('event',$data_array);
            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $event = $this->music->select('event','*',array('id'=>$id));

            if($event){
                $event = $event[0];
                $data['event'] = $event;
            }else{
                $data['event'] = '';
            }
            $this->load->view('admin/event_add',$data);
        }
    }

    function event_list(){
        $admin = $this->top();
        $data['event'] = $this->music->select('event','*');
        $this->load->view('admin/event_list',$data);
    }


    function course_add(){
        $admin = $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $name = trim($_POST['name']);
            $en_name = trim($_POST['en_name']);
            $desc = trim($_POST['desc']);
            $en_desc = trim($_POST['en_desc']);
            $display_order = trim($_POST['display_order']);
            $recommand_pic = trim($_POST['recommand_pic']);
            $en_recommand_pic = rtrim($_POST['en_recommand_pic']);
            $pdf_name = trim($_POST['pdf_name']);
            $en_pdf_name = trim($_POST['en_pdf_name']);
            $pdf = trim($_POST['pdf']);
            $en_pdf = trim($_POST['en_pdf']);
            $is_top = intval($_POST['is_top']);
            $data_array = array(
                                'en_name'=>$en_name,
                                'name'=>$name,
                                'desc'=>$desc,
                                'en_desc'=>$en_desc,
                                'display_order'=>$display_order,
                                'recommand_pic'=>$recommand_pic,
                                'en_recommand_pic'=>$en_recommand_pic,
                                'pdf_name'=>$pdf_name,
                                'en_pdf_name'=>$en_pdf_name,
                                'pdf'=>$pdf,
                                'en_pdf'=>$en_pdf,
                                'is_top' => $is_top
                                );
            if ($id){
                $re = $this->music->update('course',$data_array,array('id'=>$id));

            }else{
                $data_array['createtime'] = date('Y-m-d H:i:s');
                $re = $this->music->insert('course',$data_array);
            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $course = $this->music->select('course','*',array('id'=>$id));

            if($course){
                $data['course'] = $course[0];

            }else{
                $data['course'] = '';
            }
            $this->load->view('admin/course_add',$data);
        }
    }

    function course_list(){
        $admin = $this->top();
        $data['course'] = $this->music->select('course','*');
        $this->load->view('admin/course_list',$data);
    }


    function store_add(){
        $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $name = trim($_POST['name']);
            $address = trim($_POST['address']);
            $chairman = trim($_POST['chairman']);
            $phone = trim($_POST['phone']);
            $data_array = array('phone'=>$phone,'name'=>$name,'chairman'=>$chairman,'address'=>$address);
            if ($id){
                $re = $this->music->update('store',$data_array,array('id'=>$id));
            }else{
                $re = $this->music->insert('store',$data_array);

            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $store = $this->music->select('store','*',array('id'=>$id));

            if($store){
                $data['store'] = $store[0];
            }else{
                $data['store'] = '';
            }

            $this->load->view('admin/store_add',$data);
        }
    }

    function store_list(){
        $admin = $this->top();
        $cond = array();
        if($admin->level){
          $cond['id'] = $admin->store_id;
        }
        $total = $this->music->select_count_where('store',$cond);
        $page = $this->uri->segment(3);

        $perpage = PERPAGE;
        if($page==""){
          $page=1;
        }
        $start = ($page-1)*$perpage;

        $data['total'] = ceil($total/$perpage);
        $data['start']=$start;
        $data['current_page'] = $page;
        $score = $this->music->select('store','*',$cond,$perpage,$start);
        $data['store'] = $score;
        $data['admin'] = $admin;
        $this->load->view('admin/store_list',$data);
    }


    function index(){

        $this->top();
       // $this->load->view('admin/main');
       redirect('/admin/wechat_config');

    }


    function data_delete(){
        $table = trim($_POST['table']);
        $id    = intval($_POST['id']);


        $re = $this->music->delete($table,array('id'=>$id));
        if ($re){

            $result = array('success'=>'yes','msg'=>'操作成功');
        }else{
            $result = array('success'=>'no');
        }
        echo json_encode($result);exit;
    }

    function admin_add(){
        $this->top();
        if($_POST){
            $id    = trim($_POST['id']);
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $level = trim($_POST['level']);
            $store_id = trim($_POST['store_id']);
            $mobile = trim($_POST['mobile']);
            $data_array = array('mobile'=>$mobile,'username'=>$username,'level'=>$level,'store_id'=>$store_id);
            if ($id){

                if($password){
                   $data_array['password']= md5($password);
                }
                $re = $this->music->update('admin',$data_array,array('id'=>$id));
            }else{
                $data_array['password']= md5($password);
                $re = $this->music->insert('admin',$data_array);

            }
            if ($re){
                $result = array('success'=>'yes','msg'=>'操作成功');
            }else{
                $result = array('success'=>'no','操作失败');
            }
            echo json_encode($result);exit;

        }else{
            $id = $this->uri->segment(3);
            $admin = $this->music->select('admin','*',array('id'=>$id));

            if($admin){
                $data['admin'] = $admin[0];
            }else{
                $data['admin'] = '';
            }
            $data['store'] = $this->music->select('store','*');
            $this->load->view('admin/admin_add',$data);
        }
    }


    function admin_list(){
        $this->top();
        $cond = '';

        $total = $this->music->select_count_where('admin',array('store_id !='=>0));
        $page = $this->uri->segment(3);

        $perpage = PERPAGE;
        if($page==""){
          $page=1;
        }
        $start = ($page-1)*$perpage;

        $data['total'] = ceil($total/$perpage);
        $data['start']=$start;
        $data['current_page'] = $page;
        $admin = $this->music->select('admin','*',array('store_id !='=>0),$perpage,$start);
        foreach($admin as $k=>$v){
            $temp_store = $this->music->select('store','name',array('id'=>$v->store_id));
            $admin[$k]->store_name = $temp_store[0]->name;
        }
        $data['admin'] = $admin;
        $this->load->view('admin/admin_list',$data);
    }

    function login(){
        if ($_POST){
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $re = $this->music->select('admin','*',array('username'=>$username,'password'=>md5($password)));
            if ($re){

                $this->session->set_userdata('admin',$re);
                $r = array('success'=>'yes');

            }else{
                $r = array('success'=>'no');
            }
            $user = $this->session->userdata('admin');
            echo json_encode($r);exit;

        }else{
            $this->load->view('admin/login');
        }
    }


    function logout(){
        $user = $this->session->userdata('admin');
        if($user==""){
            //直接跳转到登录页面
            redirect('admin/login');exit;
        }else{
            $this->session->sess_destroy();
            redirect('admin/index');exit;
        }
    }


    function top(){
        $user = $this->session->userdata('admin');
        if($user==""){
            //直接跳转到登录页面
            redirect('/admin/login');exit;
        }else{
          return $user[0];
        }
    }

}
