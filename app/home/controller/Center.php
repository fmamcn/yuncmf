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

class Center extends Base
{
	protected function _initialize()
    {
		parent::_initialize();
		$this->check_login();
	}
	public function index()
    {
        // 站点选项
        $site_options=Db::name('options')->where(array('option_name'=>'site_options'))->where(array('option_l'=>'zh-cn'))->select();
        $this->assign("options",$site_options);
        // 会员组
        $membergroud=Db::name('member_group')->where ( array('member_group_id'=>$this->user['member_groupid']) )->find ();
        $membergroudname=$membergroud['member_group_name'];
        // 收藏夹
        $favorites_model=Db::name("favorites");
        $favorites=$favorites_model->alias("a")->join(config('database.prefix').'news b','a.t_id =b.n_id')->where(array('uid'=>$this->user['member_id']))->order('a.id asc')->paginate(config('paginate.list_rows'));
        $fav_count=count($favorites);
        // 评论数
        $comments=Db::name('comments')->alias("a")->join(config('database.prefix').'news b','a.t_id =b.n_id')->where(array('uid'=>$this->user['member_id']))->order("createtime desc")->paginate(config('paginate.list_rows'));
        $com_count=count($comments);
        // 关联的学员
        $students=Db::name('student_list')->where (array('student_parents_id'=>$this->user['member_id']) )->select();
        $this->assign($this->user);
        $this->assign('membergroudname',$membergroudname);
        $this->assign("fav_count",$fav_count);
        $this->assign("com_count",$com_count);
        $this->assign("students",$students);
		return $this->view->fetch('user:center');
    }
    //编辑用户资料
	public function edit()
    {
		$province = Db::name('Region')->where ( array('pid'=>1) )->select ();
		$city=Db::name('Region')->where ( array('pid'=>$this->user['member_province']) )->select ();
		$town=Db::name('Region')->where ( array('pid'=>$this->user['member_city']) )->select ();
		$this->assign('province',$province);
		$this->assign('city',$city);
		$this->assign('town',$town);
		$this->assign($this->user);
		return $this->view->fetch('user:edit');
    }
    public function runedit()
    {
    	if(request()->isPost()){
			$post=input('post.');
			$rst=Db::name('member')->where(array('member_id'=>$this->user['member_id']))->update($post);
			if ($rst!==false) {
				$this->user=Db::name('member')->find($this->user['member_id']);
				session('user',$this->user);
				$this->success(lang('save success'),url("home/Center/edit"));
			} else {
				$this->error(lang('save failed'));
			}
    	}
    }
	//修改密码
	public function password()
    {
		$this->assign($this->user);
		return $this->view->fetch('user:password');
    }
	public function runchangepwd()
    {
    	if (request()->isPost()) {
			$old_password=input('old_password');
    		$password=input('password');
			$repassword=input('repassword');
    		if(empty($old_password)){
    			$this->error(lang('old pwd empty'));
    		}
    		if(empty($password)){
    			$this->error(lang('new pwd empty'));
    		}
			if($password!==$repassword){
    			$this->error(lang('pwd not same'));
    		}
			$member=Db::name('member');
    		$user=$member->where(array('member_id'=>$this->user['member_id']))->find();
			$member_salt=$user['member_salt'];
    		if(encrypt_password($old_password,$member_salt)===$user['member_pwd']){
				if(encrypt_password($password,$member_salt)==$user['member_pwd']){
					$this->error(lang('new pwd the same as old pwd'));
				}else{
					$data['member_pwd']=encrypt_password($password,$member_salt);
					$data['member_id']=$this->user['member_id'];
					$rst=$member->update($data);
					if ($rst!==false) {
						$this->success(lang('revise success'),url('home/Center/index'));
					} else {
						$this->error(lang('revise failed'));
					}
				}
    		}else{
    			$this->error(lang('old pwd not correct'));
    		}
    	}
    }
	public function avatar()
    {
        $imgurl=input('imgurl');
        //去'/'
        $imgurl=str_replace('/','',$imgurl);
        $rst=Db::name('member')->where(array('member_id'=>$this->user['member_id']))->update(array('member_headpic'=>$imgurl));
        if($rst!==false){
            session('user_avatar',$imgurl);
			$this->user['member_headpic']=$imgurl;
			$url='/data/upload/avatar/'.$imgurl;
			//写入数据库
			$data['uptime']=time();
			$data['filesize']=filesize('./'.$url);
			$data['path']=$url;
			Db::name('plug_files')->insert($data);
            $this->success (lang('avatar update success'),url('home/Center/index'));
        }else{
            $this->error (lang('avatar update failed'),url('home/Center/index'));
        }
    }
    public function bang()
    {
    	$oauth_user_model=Db::name("OauthUser");
    	$oauths=$oauth_user_model->where(array("uid"=>$this->user['member_id']))->select();
    	$new_oauths=array();
    	foreach ($oauths as $oa){
    		$new_oauths[strtolower($oa['oauth_from'])]=$oa;
    	}
    	$this->assign("oauths",$new_oauths);
		return $this->view->fetch('user:bang');
    }
    public function fav()
    {
		$favorites_model=Db::name("favorites");
        $favorites=$favorites_model->alias("a")->join(config('database.prefix').'news b','a.t_id =b.n_id')->where(array('uid'=>$this->user['member_id']))->order('a.id asc')->paginate(config('paginate.list_rows'));
		$show=$favorites->render();
		$this->assign('page',$show);
		$this->assign("favorites",$favorites);
		return $this->view->fetch('user:favorite');
	}
    public function delete_favorite()
    {
        $id=input("id",0,"intval");
        $p=input("p",1,"intval");
        $favorites_model=Db::name("favorites");
        $result=$favorites_model->where(array('id'=>$id,'uid'=>$this->user['member_id']))->delete();
        if($result){
            $this->success(lang('cancel collection success'),url('home/Center/fav',array('p'=>$p)));
        }else {
            $this->error(lang('cancel collection failed'));
        }
    }
}
