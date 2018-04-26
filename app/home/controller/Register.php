<?php
// +----------------------------------------------------------------------
// | oneMall [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
namespace app\home\controller;

use think\Db;
use think\captcha\Captcha;
use think\Validate;

class Register extends Base
{

    public function index()
    {
	    if(session('hid')){ //已经登录时直接跳到首页
	        $this->redirect(__ROOT__."/");
	    }else{
		    // 判断短信接口是否开启
		    $sms_sys=sys_config_get('think_sdk_sms');
		    $smsenable = $sms_sys['sms_open'];
			$this->assign('smsenable',$smsenable);
			return $this->view->fetch('user:register');
	    }
	}
	//验证码
	public function verify()
    {
        if (session('hid')) {
            $this->redirect(__ROOT__."/");
        }
		ob_end_clean();
		$verify = new Captcha (config('verify'));
		return $verify->entry('reg');
    }
    // 邮箱注册
    public function runregister()
    {
		if(request()->isPost()){
			$member_username=input('member_username');
			$member_email=input('member_email');
			$password=input('password');
			$repassword=input('repassword');
			$verify=input('verify');
			$verify_obj =new Captcha ();
			if (!$verify_obj->check($verify, 'reg')) {
				$this->error(lang('verifiy incorrect'));
			}
			$rule = [
				['member_email','require|email','{%email empty}|{%email format incorrect}'],
				['password','require|length:5,20','{%pwd empty}|{%pwd length}'],
				['member_username','require','{%username empty}'],
				['repassword','require|confirm:password','{%repassword empty}|{%repassword incorrect}']
			];
			$validate = new Validate($rule);
			$rst   = $validate->check(array(
				'member_username'=>$member_username,
				'password'=>$password,
				'repassword'=>$repassword,
				'member_email'=>$member_email
			));
			$users_model=Db::name("member");
			if(true !==$rst){
				$this->error(join('|',$validate->getError()));
			}
			//用户名需过滤的字符的正则
			$stripChar = '?<*.>\'"';
			if(preg_match('/['.$stripChar.']/is', $member_username)==1){
				$this->error(lang('username format incorrect',['stripChar'=>$stripChar]));
			}
			//判断是否存在
			$result = $users_model->where('member_username',$member_username)->whereOr('member_email',$member_email)->count();
			if($result){
				$this->error(lang('username exists'));
			}else{
				$member_salt=random(10);
				$active_options=get_active_options();
				$sl_data=array(
					'member_username'=>$member_username,
					'member_nickname'=>$member_username,
					'member_salt' => $member_salt,
					'member_pwd'=>encrypt_password($password,$member_salt),
					'member_email'=>$member_email,
					'member_groupid'=>1,
					'member_open'=>1,
					'member_addtime'=>time(),
					'user_status'=>empty($active_options['email_active'])?1:0,//需要激活,则为未激活状态,否则为激活状态
				);
				$rst=$users_model->insertGetId($sl_data);
				if($rst!==false){
					if(!empty($active_options['email_active'])){
						$activekey=md5($rst.time().uniqid());//激活码
						$result=$users_model->where(array("member_id"=>$rst))->update(array("user_activation_key"=>$activekey));
						if(!$result){
							$this->error(lang('activation code generation failed'));
						}
						//生成激活链接
						$url = url('home/Register/active',array("hash"=>$activekey), "", true);
						$template = $active_options['email_tpl'];
						$content = str_replace(array('http://#link#','#username#'), array($url,$member_username),$template);
						$send_result=sendMail($member_email, $active_options['email_title'], $content);
						if($send_result['error']){
							$this->error(lang('send active email failed'));
						}else{
							$this->success(lang('send active email success'),url('home/Login/index'));
						}
					}else{
						//更新字段
						$data = array(
							'last_login_time' => time(),
							'last_login_ip' => request()->ip(),
						);
						$sl_data['last_login_time']=$data['last_login_time'];
						$sl_data['last_login_ip']=$data['last_login_ip'];
						$users_model->where(array('member_id'=>$rst))->update($data);
						session('hid',$rst);
						session('user',$sl_data);
						$this->success(lang('register success'),url('home/Index/index'));
					}
				}else{
					$this->error(lang('register failed'));
				}
			}
		}
	}
	//激活
    public function active()
    {
		$hash=input('hash','');
		if(empty($hash)){
			$this->error(lang('pwd reset hash incorrect'));
		}
		$users_model=Db::name("member");
		$find_user=$users_model->where(array("user_activation_key"=>$hash))->find();
		if($find_user){
			$result=$users_model->where(array("user_activation_key"=>$hash))->update(array("user_activation_key"=>"","user_status"=>1));
			if($result){
				$find_user['user_status']=1;
				//更新字段
				$data = array(
					'last_login_time' => time(),
					'last_login_ip' => request()->ip(),
				);
				$find_user['last_login_time']=$data['last_login_time'];
				$find_user['last_login_ip']=$data['last_login_ip'];
				$users_model->where(array('member_id'=>$find_user["member_id"]))->update($data);
				session('hid',$find_user['member_id']);
				session('user',$find_user);
				$this->success(lang('active success'),url('home/Index/index'));
			}else{
				$this->error(lang('active failed'),url("home/Login/index"));
			}
		}else{
			$this->error(lang('pwd reset hash incorrect'),url("home/Login/index"));
		}
	}
 
    /**
     * 检测手机号是否已经存在
     */
    public function issetMobile()
    {
      $mobile = input("mobile",'0');  
      $users = Db::name("member")->where(array("member_tel"=>$mobile))->find();
      if($users)
          return 1;
      else 
          return 0; 
    }

    /**
     * 发送短信验证码: 
     */
    public function send_validate_code(){
    	$sms_sys=sys_config_get('think_sdk_sms');//获取短信接口配置
        $scene = input("scene");    //使用短信验证码的模板代码
        $mobile = input("mobile");	//短信接收者
        $sender = input("send");	//短信发送者
        $verify_code = input('verify');	//图像验证码
        $mobile = !empty($mobile) ?  $mobile : $sender ;
        // 获取session,如没有则用session_id()定义
        $session_id = input("unique_id", session_id());
        $params['session_id'] = $session_id;
        session("scene" , $scene);
        //判断能否发送验证码
        $res = checkEnableSendSms($scene);
        if($res['status'] != 1){
            ajaxReturn($res);
        }
       	//检验验证码
        if(!empty($verify_code)){
            $verify = new Captcha();
            if (!$verify->check($verify_code, 'reg')) {
                ajaxReturn(array('status'=>-1,'msg'=>'图形验证码错误'));
            }
        }
        //检索是否存在验证码
        $data = Db::name("sms_log")->where(array('mobile'=>$mobile,'session_id'=>$session_id, 'status'=>1))->order('id DESC')->find();
        //获取有效时间配置
        $sms_time_out = $sms_sys['sms_time_out'];
        //如系统没有配置有效时间，则由此处设置为300秒以内不可重复发送
        $sms_time_out = $sms_time_out ? $sms_time_out : 300;
        // 当用户已存在验证码时，并在有效时间内时
        if($data && (time() - $data['add_time']) < $sms_time_out){
            $return_arr = array('status'=>-1,'msg'=>$sms_time_out.'秒内不允许重复发送');
            ajaxReturn($return_arr);
        }
        //随机一个验证码
        $code = rand(1000, 9999);
		$params['code'] =$code;
		// 获取短信模板并替换相关关键词
        $send_scene = Db::name("sms_template")->where(array("scene_code"=>$scene))->find();
		$template = $send_scene['tpl_content'];
		$content = str_replace(array('#code#','#sms_sign#'), array($code,$send_scene['sms_sign']),$template);       
		$params['content'] =$content;
        //发送短信
        $resp = sendSms($scene , $mobile , $params);
	    if($resp['status'] == 1){
	        //发送成功, 修改发送状态位成功
	        $return_arr = array('status'=>1,'msg'=>'发送成功,请注意查收');
	    }else{
	        $return_arr = array('status'=>-1,'msg'=>'发送失败'.$resp['msg']);
	    }
    	ajaxReturn($return_arr);
    }
    // 手机注册
    public function runtelreg()
    {
    	$sms_sys=sys_config_get('think_sdk_sms');//获取短信接口配置
		$member_username=input('username');
		$member_tel=input('mobile');
		$password=input('password');
		$repassword=input('repassword');
		$session_id = input("unique_id", session_id());
		// 手机验证码验证部分
		$mobile_code = input('mobile_code');
        $data = Db::name("sms_log")->where(array('mobile'=>$member_tel,'code'=>$mobile_code,'session_id'=>$session_id, 'status'=>1))->order('id DESC')->find();
        $sms_time_out = $sms_sys['sms_time_out'];
        //如系统没有配置有效时间，则由此处设置为1800秒
        $sms_time_out = $sms_time_out ? $sms_time_out : 1800;
        if($data && (time() - $data['add_time']) > $sms_time_out){
            $return_arr = array('status'=>-1,'msg'=>'短信验证码超出有效时间');
            ajaxReturn($return_arr);
        }
		$rule = [
			['password','require|length:5,20','{%pwd empty}|{%pwd length}'],
			['member_username','require','{%username empty}'],
			['repassword','require|confirm:password','{%repassword empty}|{%repassword incorrect}']
		];
		$validate = new Validate($rule);
		$rst   = $validate->check(array(
			'member_username'=>$member_username,
			'password'=>$password,
			'repassword'=>$repassword
		));
		$users_model=Db::name("member");
		if(true !==$rst){
            $return_arr = array('status'=>-1,'msg'=>'用户名不规范');
            ajaxReturn($return_arr);
		}
		//用户名需过滤的字符的正则
		$stripChar = '?<*.>\'"';
		if(preg_match('/['.$stripChar.']/is', $member_username)==1){
            $return_arr = array('status'=>-1,'msg'=>'用户名不规范');
            ajaxReturn($return_arr);
		}
		//判断是否存在
		$result = $users_model->where('member_username',$member_username)->whereOr('member_tel',$member_tel)->count();
		if($result){
            $return_arr = array('status'=>-1,'msg'=>'用户名已经存在');
            ajaxReturn($return_arr);
		}else{
			$member_salt=random(10);
			$sl_data=array(
				'member_username'=>$member_username,
				'member_nickname'=>$member_username,
				'member_salt' => $member_salt,
				'member_pwd'=>encrypt_password($password,$member_salt),
				'member_tel'=>$member_tel,
				'member_groupid'=>1,
				'member_open'=>1,
				'member_addtime'=>time(),
				'user_status'=>1,
			);
			$rst=$users_model->insertGetId($sl_data);
			if($rst!==false){
	            $return_arr = array('status'=>1,'msg'=>'成功注册，跳转到登录画面！');
	            ajaxReturn($return_arr);
			}else{
            $return_arr = array('status'=>-1,'msg'=>'注册失败');
            ajaxReturn($return_arr);
			}
		}
	}

// 结束
}