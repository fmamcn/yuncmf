<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 基架
// +----------------------------------------------------------------------
namespace app\home\controller;

use app\common\controller\Common;
use app\admin\model\Options;
use think\Db;

class Base extends Common
{
	protected $view;
	protected $user;
	protected $theme_path;
	protected function _initialize()
    {
        parent::_initialize();
        //站点选项
		$site_options=Options::get_options('site_options');
		//统计代码
        $site_options['site_tongji']=htmlspecialchars_decode($site_options['site_tongji']);
        //版权
        $site_options['site_copyright']=htmlspecialchars_decode($site_options['site_copyright']);
        //定义前端模板
        if(request()->isMobile()){
            $theme=$site_options['site_tpl_m']?:$site_options['site_tpl'];
        }else{
            $theme=$site_options['site_tpl'];
        }
		$this->view=$this->view->config('view_path',APP_PATH.request()->module().'/view/'.$theme.'/');
		$theme_path=__ROOT__.'/app/home/view/'.$theme.'/';
		$this->assign($site_options);
		$this->assign('theme_path',$theme_path);
		$address='';
		$this->user=array();
		//判断是否登录
		$uid=session('hid');
		if(empty($uid)){
			//检测cookies
			$cookie = cookie('logged_user');//'id'.'时间'
			$cookie = explode(".", jiemi($cookie));
			$uid=empty($cookie[0])?0:$cookie[0];
			if($uid && !empty($cookie[1])){
				//判断是否存在此用户
				$member=Db::name("member")->find($uid);
				if($member && (time()-intval($cookie[1]))<config('cookie.expire')){
					//更新字段
					$data = array(
						'last_login_time' => time(),
						'last_login_ip' => request()->ip(),
					);
					Db::name("member")->where(array('member_id'=>$member["member_id"]))->update($data);
					$member['last_login_time']=$data['last_login_time'];
					$member['last_login_ip']=$data['last_login_ip'];
					//设置session
					session('hid',$member['member_id']);
					session('user',$member);
				}
			}
		}
		$is_admin=false;
		if(session('hid')){
			$this->user=Db::name('member')->find(session('hid'));
			if(!empty($this->user['member_province'])){
				$rst=Db::name('region')->field('name')->find($this->user['member_province']);
				$address.=$rst?$rst['name'].'省':'';
			}
			if(!empty($this->user['member_city'])){
				$rst=Db::name('region')->field('name')->find($this->user['member_city']);
				$address.=$rst?$rst['name'].'市':'';
			}
			if(!empty($this->user['member_town'])){
				$rst=Db::name('region')->field('name')->find($this->user['member_town']);
				$address.=$rst?$rst['name']:'';
			}
			//判断是否为管理员
			$admin=Db::name('admin')->where('member_id',$this->user['member_id'])->find();
			if($admin){
				$is_admin=true;
			}
		}
		$this->user['address']=$address;
		$this->assign("user",$this->user);
		$this->assign("is_admin",$is_admin);
	}
	/**
	 * 检查用户登录
	 */
	protected function check_login()
    {
		if(!session('hid')){
			$this->error('请重新登录',__ROOT__."/home/login");
		}
	}
	/**
	 * 检查操作频率
	 * @param int $t_check 距离最后一次操作的时长
	 */
	protected function check_last_action($t_check)
    {
		$action=MODULE_NAME."-".CONTROLLER_NAME."-".ACTION_NAME;
		$time=time();
		$action_s=session('last_action.action');
		if(!empty($action_s) && $action=$action_s){
			$t=$time-session('last_action.time');
			if($t_check>$t){
				$this->error('操作过于频繁');
			}else{
				session('last_action.time',$time);
			}
		}else{
			session('last_action.action',$action);
			session('last_action.time',$time);
		}
	}


}