<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 手机APP端注册控制器
// +----------------------------------------------------------------------
namespace app\mobile\controller;

use think\Db;
use think\captcha\Captcha;
use think\Validate;

class Register extends Base
{
    /**
     * 检测手机号是否已经存在
     */
    public function issetMobile()
    {
      $mobile = input("mobile",'0');  
      $users = Db::name("member_list")->where(array("member_list_tel"=>$mobile))->find();
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
        //判断能否发送验证码
        $res = checkEnableSendSms($scene);
        if($res['status'] != 1){
            ajaxReturn($res);
        }
        // 获取session,如没有则用session_id()定义
        $session_id = input("unique_id", session_id());
        $params['session_id'] = $session_id;
        //检索是否存在验证码
        $data = Db::name("sms_log")->where(array('mobile'=>$mobile,'status'=>1))->order('id DESC')->find();
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
		// 根据手机号搜索用户名
	    $users = Db::name("member_list")->where(array("member_list_tel"=>$mobile))->find();
		// 获取短信模板并替换相关关键词
        $send_scene = Db::name("sms_template")->where(array("scene_code"=>$scene))->find();
		$template = $send_scene['tpl_content'];
		$content = str_replace(array('#code#','#sms_sign#','#user_name#'), array($code,$send_scene['sms_sign'],$users['member_list_username']),$template);       
		$params['content'] =$content;
        // 上线前干掉下面一行
    	// ajaxReturn(array('status'=>1,'msg'=>'发送成功,请注意查收','code'=>$code));
        //发送短信
        $resp = sendSms($scene , $mobile , $params);
	    if($resp['status'] == 1){
	        //发送成功, 修改发送状态位成功
	        $return_arr = array('status'=>1,'msg'=>'发送成功,请注意查收','code'=>$code);
	    }else{
	        $return_arr = array('status'=>-1,'msg'=>'发送失败'.$resp['msg']);
	    }
    	ajaxReturn($return_arr);
    }
    // 手机注册
    public function runregister()
    {
    	$sms_sys=sys_config_get('think_sdk_sms');//获取短信接口配置
		$member_list_username=input('username');
		$member_list_tel=input('mobile');
		$password=input('password');
		// 手机验证码验证部分
		$mobile_code = input('mobile_code');
        $data = Db::name("sms_log")->where(array('mobile'=>$member_list_tel,'code'=>$mobile_code,'status'=>1))->order('id DESC')->find();
        $sms_time_out = $sms_sys['sms_time_out'];
        //如系统没有配置有效时间，则由此处设置为1800秒
        $sms_time_out = $sms_time_out ? $sms_time_out : 1800;
        if($data && (time() - $data['add_time']) > $sms_time_out){
            $return_arr = array('status'=>-1,'msg'=>'短信验证码超出有效时间');
            ajaxReturn($return_arr);
        }
		$rule = [
			['password','require|length:5,20','{%pwd empty}|{%pwd length}'],
			['member_list_username','require','{%username empty}'],
		];
		$validate = new Validate($rule);
		$rst   = $validate->check(array(
			'member_list_username'=>$member_list_username,
			'password'=>$password,
		));
		$users_model=Db::name("member_list");
		if(true !==$rst){
            $return_arr = array('status'=>-1,'msg'=>'用户名不规范');
            ajaxReturn($return_arr);
		}
		//用户名需过滤的字符的正则
		$stripChar = '?<*.>\'"';
		if(preg_match('/['.$stripChar.']/is', $member_list_username)==1){
            $return_arr = array('status'=>-1,'msg'=>'用户名不规范');
            ajaxReturn($return_arr);
		}
		//判断是否存在
		$result = $users_model->where('member_list_username',$member_list_username)->whereOr('member_list_tel',$member_list_tel)->count();
		if($result){
            $return_arr = array('status'=>-1,'msg'=>'用户名或手机号码已经被注册');
            ajaxReturn($return_arr);
		}else{
			$member_list_salt=random(10);
			$sl_data=array(
				'member_list_username'=>$member_list_username,
				'member_list_nickname'=>$member_list_username,
				'member_list_salt' => $member_list_salt,
				'member_list_pwd'=>encrypt_password($password,$member_list_salt),
				'member_list_tel'=>$member_list_tel,
				'member_list_groupid'=>1,
				'member_list_open'=>1,
				'member_list_addtime'=>time(),
				'user_status'=>1,
			);
			$rst=$users_model->insertGetId($sl_data);
			if($rst!==false){
	            $return_arr = array('status'=>1,'msg'=>'成功注册，请重新登录！');
	            ajaxReturn($return_arr);
			}else{
                $return_arr = array('status'=>-1,'msg'=>'注册失败');
                ajaxReturn($return_arr);
			}
		}
	}

// 结束
}