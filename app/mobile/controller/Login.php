<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 手机APP端登录控制器
// +----------------------------------------------------------------------
namespace app\mobile\controller;

use think\Db;
use think\Validate;

class Login extends Base
{

    //登录验证
    public function runlogin()
    {
		$member_list_username=input('member_list_username');
		$member_list_pwd=input('member_list_pwd');
		$rule = [
			['member_list_username','require','{%username empty}'],
			['member_list_pwd','require','{%pwd empty}'],
		];
		$validate = new Validate($rule);
		$rst   = $validate->check(array('member_list_username'=>$member_list_username,'member_list_pwd'=>$member_list_pwd));
		if(true !==$rst){
			ajaxReturn(array('status'=>-1,'msg'=>'验证错误'));
		}
        $where['member_list_username']=$member_list_username;
		$member=Db::name("member_list")->where($where)->find();
		if (!$member||encrypt_password($member_list_pwd,$member['member_list_salt'])!==$member['member_list_pwd']){
			ajaxReturn(array('status'=>-1,'msg'=>'用户名或密码错误'));
		}else{
			if($member['member_list_open']==0){
				ajaxReturn(array('status'=>-1,'msg'=>'用户未激活'));
			}
			//更新字段
			$data = array(
				'last_login_time' => time(),
				'last_login_ip' => request()->ip(),
			);
			Db::name("member_list")->where(array('member_list_id'=>$member["member_list_id"]))->update($data);
			session('hid',$member['member_list_id']);
			session('user',$member);
			if($member['user_status']){
				//更新cookie
				cookie('yf_logged_user', jiami("{$member['member_list_id']}.{$data['last_login_time']}"));
			}
			ajaxReturn(array('status'=>1,'msg'=>'登录成功','uid'=>$member['member_list_id'],'childs_id'=>$member['childs_id'],'grade_id'=>$member['grade_id'],'school_id'=>$member['school_id']));
		}
    }
    //签到
    public function checkin()
    {
    	$member_list_id=input('uid');
    	Db::name("member_list")->where('member_list_id',$member_list_id)->setInc('score',10);
    	ajaxReturn(array('status'=>1,'msg'=>'签到成功，增加10积分'));
    }
    // 个人中心重置密码
    public function runpwd_reset()
    {
    	$uid=input('uid');
    	$oldpwd=input('oldpwd');
    	$password=input('password');
    	$member=Db::name("member_list")->where(array("member_list_id"=>$uid))->find();
    	$memberoldpwd=$member['member_list_pwd'];
    	// 将输入的密码进行加密处理
    	$eninputpwd=encrypt_password($oldpwd,$member['member_list_salt']);
		if($memberoldpwd !==$eninputpwd){
			ajaxReturn(array('status'=>0,'msg'=>"旧密码错误，请重新输入"));
		}else{
			$member_list_salt=random(10);
			$member_list_pwd=encrypt_password($password,$member_list_salt);
			$result=Db::name("member_list")->where(array("member_list_id"=>$uid))->update(array('member_list_pwd'=>$member_list_pwd,'user_activation_key'=>'','member_list_salt'=>$member_list_salt));
			if($result){
				ajaxReturn(array('status'=>1,'msg'=>"更改密码成功！"));
			}else {
				ajaxReturn(array('status'=>-1,'msg'=>"更改密码失败，请重新输入！"));
			}
		}
	}
    // 登录页面重置密码
    public function pwd_reset()
    {
    	$mobile=input('mobile');
    	$username=input('username');
    	$password=input('password');
    	$member=Db::name("member_list")->where(array("member_list_username"=>$username))->where(array("member_list_tel"=>$mobile))->find();
		if($member){
			$member_list_salt=random(10);
			$member_list_pwd=encrypt_password($password,$member_list_salt);
			$result=Db::name("member_list")->where(array("member_list_username"=>$username))->where(array("member_list_tel"=>$mobile))->update(array('member_list_pwd'=>$member_list_pwd,'user_activation_key'=>'','member_list_salt'=>$member_list_salt));
			if($result){
				ajaxReturn(array('status'=>1,'msg'=>"更改密码成功！"));
			}else {
				ajaxReturn(array('status'=>-1,'msg'=>"更改密码失败，请重新输入！"));
			}
		}else{
			ajaxReturn(array('status'=>0,'msg'=>"账号和手机不匹配，请重新输入"));
		}
	}
    // 更改昵称
    public function reset_nickname()
    {
    	$uid=input('uid');
    	$nickname=input('nickname');
		$result=Db::name("member_list")->where(array("member_list_id"=>$uid))->update(array('member_list_nickname'=>$nickname));
		if($result){
			ajaxReturn(array('status'=>1,'msg'=>"更改昵称成功！"));
		}else {
			ajaxReturn(array('status'=>-1,'msg'=>"更改昵称失败，请重新输入！"));
		}
	}
    // 更改签名
    public function reset_sign()
    {
    	$uid=input('uid');
    	$sign=input('sign');
		$result=Db::name("member_list")->where(array("member_list_id"=>$uid))->update(array('signature'=>$sign));
		if($result){
			ajaxReturn(array('status'=>1,'msg'=>"更改签名成功！"));
		}else {
			ajaxReturn(array('status'=>-1,'msg'=>"更改签名失败，请重新输入！"));
		}
	}

// 结束
}