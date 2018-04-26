<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 系统管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\AuthRule;
use app\admin\model\Options;
use think\Db;
use think\Cache;

class Sys extends Base
{
	/**
	 * 站点设置
	 */
	public function sys()
	{
		//主题
		$tpls=Options::themes();
		$this->assign('templates',$tpls);
		$sys=Options::get_options('site_options');
		$this->assign('sys',$sys);
		return $this->fetch();
	}
	/**
	 * 站点设置保存
	 */
	public function runsys()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/sys'));
		}else{
			//logo图片
			$checkpic=input('checkpic');
			$oldcheckpic=input('oldcheckpic');
			$options=input('post.options/a');
			$img_url='';
			if ($checkpic!=$oldcheckpic){
				$file = request()->file('file0');
				if(!empty($file)){
					//本地
					$validate=config('upload_validate');
					$info = $file->validate($validate)->rule('uniqid')->move(ROOT_PATH . config('upload_path') . DS . date('Y-m-d'));
					if($info) {
						$img_url=config('upload_path'). '/' . date('Y-m-d') . '/' . $info->getFilename();
						//写入数据库
						$data['uptime']=time();
						$data['filesize']=$info->getSize();
						$data['path']=$img_url;
						Db::name('plug_files')->insert($data);
					}else{
						$this->error($file->getError(),url('admin/Sys/sys'));//否则就是上传错误，显示错误原因
					}
				$options['site_logo']=$img_url;
				}
			}else{
				//原有图片
				$options['site_logo']=input('oldcheckpicname');
			}
			
			//更新设置
			$rst=Options::set_options($options,'site_options');
			if($rst!==false){
				cache('site_options_', $options);
				$this->success('站点设置保存成功',url('admin/Sys/sys'));
			}else{
				$this->error('提交参数不正确',url('admin/Sys/sys'));
			}
		}
	}
	/**
	 * 日志设置
	 */
	public function logsys()
	{
		$log=config('log');
		$log['level']=empty($log['level'])?join(',',['log', 'error', 'info', 'sql', 'notice', 'alert', 'debug']):join(',',$log['level']);
		$this->assign('log',$log);
		return $this->fetch();
	}
	/**
	 * 日志设置保存
	 */
	public function runlogsys()
	{
		$log_level=input('log_level/a');
		$log['clear_on']=input('clear_on',0,'intval')?true:false;
		$log['timebf']=input('timebf',2592000,'intval');
		$log['level']=(count($log_level)==7 || empty($log_level))?[]:$log_level;
		sys_config_setbykey('log',$log);
		cache::clear();
		$this->success('日志设置成功',url('admin/Sys/logsys'));
	}
	/**
	 * 路由设置
	 */
	public function urlsetsys()
	{
		$routes=Db::name('route')->order('listorder')->paginate(config('paginate.list_rows'),false,['query'=>get_query()]);
		$show = $routes->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('page',$show);
		$this->assign('routes',$routes);
		if(request()->isAjax()){
			return $this->fetch('ajax_urlsetsys');
		}else{
			return $this->fetch();
		}
	}
	/*
	 * 添加路由规则操作
	 */
	public function route_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/urlsetsys'));
		}		
		Db::name('route')->insert(input('post.'));
		$p=input('p',1,'intval');
		if(config('url_route_mode')=='2') Cache::rm('routes');
		$this->success('路由规则添加成功',url('admin/Sys/urlsetsys',array('p'=>$p)),1);
		
	}
	/*
	 * 修改路由规则操作
	 */
	public function route_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/sys'));
		}
		$p=input('p',1,'intval');
		$sl_data=array(
			'id'=>input('id'),
			'full_url'=>input('full_url'),
			'url'=>input('url'),
			'status'=>input('status'),
			'listorder'=>input('listorder'),
		);
		$rst=Db::name('route')->update($sl_data);
		if($rst!==false){
			if(config('url_route_mode')=='2') Cache::rm('routes');
			$this->success('路由规则修改成功',url('admin/Sys/urlsetsys',array('p'=>$p)));
		}else{
			$this->error('路由规则修改失败',url('admin/Sys/urlsetsys',array('p'=>$p)));
		}
	}
	/*
	 * 路由规则修改返回值操作
	 */
	public function route_edit()
	{
		$id=input('id');
		$route=Db::name('route')->where(array('id'=>$id))->find();
		$route['code']=1;
		return json($route);
	}
	/*
	 * 路由规则排序
	 */
	public function route_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/urlsetsys'));
		}	
		$route=Db::name('route');
		foreach (input('post.') as $id => $listorder){
			$route->where(array('id' => $id ))->setField('listorder' , $listorder);
		}
		if(config('url_route_mode')=='2') Cache::rm('routes');
		$this->success('排序更新成功',url('admin/Sys/urlsetsys'));
	}
	/*
	 * 删除路由规则
	 */
	public function route_del()
	{
		$rst=Db::name('route')->where(array('id'=>input('id')))->delete();
		if($rst!==false){
			$p=input('p',1,'intval');
			if(config('url_route_mode')=='2') Cache::rm('routes');
			$this->success('路由规则删除成功',url('admin/Sys/urlsetsys',array('p'=>$p)));
		}else{
			$this->error('路由规则删除失败',url('admin/Sys/urlsetsys'));
		}
	}
	/*
	 * 修改路由规则状态
	 */
	public function route_state()
	{
		$id=input('x');
		if (empty($id)){
			$this->error('规则ID不存在',url('admin/Sys/urlsetsys'));
		}
		$status=Db::name('route')->where(array('id'=>$id))->value('status');//判断当前状态情况
		if($status==1){
			$statedata = array('status'=>0);
			Db::name('route')->where(array('id'=>$id))->setField($statedata);
			if(config('url_route_mode')=='2') Cache::rm('routes');
			$this->success('状态禁止');
		}else{
			$statedata = array('status'=>1);
			Db::name('route')->where(array('id'=>$id))->setField($statedata);
			if(config('url_route_mode')=='2') Cache::rm('routes');
			$this->success('状态开启');
		}
	}

	/**
	 * 显示路由基本设置
	 */
	public function urlsys(){
		return $this->fetch();
	}
	/*
	 * 路由基本规则设置
	 */
	public function runurlsys()
	{
		$route_on=input('route_on',0,'intval')?true:false;
		$route_must=input('route_must',0,'intval')?true:false;;
		$complete_match=input('complete_match',0,'intval')?true:false;;
		$html_suffix=input('html_suffix','');
		$url_route_mode=input('url_route_mode','');
		sys_config_setbykey('url_route_on',$route_on);
		sys_config_setbykey('url_route_must',$route_must);
		sys_config_setbykey('route_complete_match',$complete_match);
		sys_config_setbykey('url_html_suffix',$html_suffix);
		sys_config_setbykey('url_route_mode',$url_route_mode);
		Cache::rm('routes');
		$this->success('URL基本设置成功',url('admin/Sys/urlsetsys#basic'));
	}
	/**
	 * 显示邮箱设置
	 */
	public function emailsys()
	{
		$sys=Options::get_options('email_options');
		$this->assign('sys',$sys);
		return $this->fetch();
	}
	/**
	 * 保存邮箱设置
	 */
	public function runemail()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/emailsys'));
		}else{
			$options=input('post.options/a');
			$rst=Options::set_options($options,'email_options');
			if($rst!==false){
				cache("email_options",null);
				$this->success('邮箱设置保存成功',url('admin/Sys/emailsys'));
			}else{
				$this->error('提交参数不正确',url('admin/Sys/emailsys'));
			}
		}
	}
	/**
	 * 显示邮箱激活
	 */
	public function activesys()
	{
		$sys=Options::get_options('active_options');
		$this->assign('sys',$sys);
		return $this->fetch();
	}
	/**
	 * 保存邮箱激活设置
	 */
	public function runactive()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/activesys'));
		}else{
			$options=input('post.options/a');
			$options['email_tpl']=htmlspecialchars_decode($options['email_tpl']);
			$rst=Options::set_options($options,'active_options');
			if($rst!==false){
				cache("active_options",null);
				$this->success('帐号激活设置保存成功',url('admin/Sys/activesys'));
			}else{
				$this->error('提交参数不正确',url('admin/Sys/activesys'));
			}
		}
	}
	/**
	 * 短信设置
	 */
	public function smssys()
	{
		$sms_sys=sys_config_get('think_sdk_sms');
		$sms_template=Db::name('sms_template')->order('tpl_id asc')->paginate(config('paginate.list_rows'));
		$this->assign('sms_template',$sms_template);
		$page = $sms_template->render();
		$this->assign('page',$page);
		$this->assign('sms_sys',$sms_sys);
		return $this->fetch();
	}
	/**
	 * 保存短信设置
	 */
	public function runsmssys()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/smssys'));
		}else{
			$data = array(
				'think_sdk_sms' => array(
					'AccessKeyId' => input('AccessKeyId'),
					'accessKeySecret' => input('accessKeySecret'),
					'sms_open' => input('sms_open'),
					'sms_time_out' => input('sms_time_out'),
				)
			);
			$rst=sys_config_setbyarr($data);
			if($rst!==false){
				Cache::clear();
				$this->success('设置保存成功',url('admin/Sys/smssys'));
			}else{
				$this->error('设置保存失败',url('admin/Sys/smssys'));
			}
		}
	}

	/*
	 * 保存短信模板
	 */
	public function sms_template_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/smssys'));
		}else{
			$sl_data=array(
				'send_scene'=>input('send_scene'),
				'sms_sign'=>input('sms_sign'),
				'tpl_content'=>input('tpl_content'),
				'scene_code'=>input('scene_code'),
				'add_time'=>time(),
			);
			$rst=Db::name('sms_template')->insert($sl_data);
			if($rst!==false){
				$this->success('添加短信模板成功',url('admin/Sys/smssys'));
			}else{
				$this->error('添加短信模板失败',url('admin/Sys/smssys'));
			}
		}
	}
	/*
	 * 删除短信模板
	 */
	public function sms_template_del()
	{
		$rst=Db::name('sms_template')->where(array('tpl_id'=>input('tpl_id')))->delete();
		if($rst!==false){
			$this->success('短信模板删除成功',url('admin/Sys/smssys'));
		}else{
			$this->error('短信模板删除失败',url('admin/Sys/smssys'));
		}
	}
	/*
	 * 返回短信模板值
	 */
	public function sms_template_edit()
	{
		$tpl_id=input('tpl_id');
		$sms_template=Db::name('sms_template')->where(array('tpl_id'=>$tpl_id))->find();
		$sl_data['tpl_id']=$sms_template['tpl_id'];
		$sl_data['send_scene']=$sms_template['send_scene'];
		$sl_data['sms_sign']=$sms_template['sms_sign'];
		$sl_data['tpl_content']=$sms_template['tpl_content'];
		$sl_data['scene_code']=$sms_template['scene_code'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改短信模板
	 */
	public function sms_template_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/smssys'));
		}else{
			$sl_data=array(
				'tpl_id'=>input('tpl_id'),
				'send_scene'=>input('send_scene'),
				'sms_sign'=>input('sms_sign'),
				'tpl_content'=>input('tpl_content'),
				'scene_code'=>input('scene_code'),
			);
			$rst=Db::name('sms_template')->update($sl_data);
			if($rst!==false){
				$this->success('短信模板修改成功',url('admin/Sys/smssys'));
			}else{
				$this->error('短信模板修改失败',url('admin/Sys/smssys'));
			}
		}
	}	
	/**
	 * 检验场景代码是否重复
	 */
	public function issetSceneCode()
	{
	  $code = input("code",'0');  
	  $codes = Db::name("sms_template")->where(array("scene_code"=>$code))->find();
	  if($codes)
		  return 1;
	  else 
		  return 0; 
	}
	/**
	 * 显示支付设置
	 */
	public function paysys()
	{
		$payment=sys_config_get('payment');
		$this->assign('payment',$payment);
		return $this->fetch();
	}
	/**
	 * 保存支付设置
	 */
	public function runpaysys()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/paysys'));
		}else{
			$config = input('config/a');
			$rst=sys_config_setbyarr(['payment'=>$config]);
			if($rst!==false){
				Cache::clear();
				$this->success('设置保存成功',url('admin/Sys/paysys'));
			}else{
				$this->error('设置保存失败',url('admin/Sys/paysys'));
			}
		}
	}
	//显示微信设置
	public function wesys()
    {
    	$config=config('we_options');
		$this->assign('sys',$config);
		return $this->fetch();
	}

	//保存微信设置
	public function runwesys()
    {
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/wesys'));
		}else{
			$we_options=input('post.');
			$rst=sys_config_setbyarr($we_options);
			if($rst!==false){
				$this->success('微信设置保存成功',url('admin/Sys/wesys'));
			}else{
				$this->error('提交参数不正确',url('admin/Sys/wesys'));
			}
		}
	}

	/**
	 * 显示第三方登录设置
	 */
	public function oauthsys()
	{
		$oauth_qq=sys_config_get('think_sdk_qq');
		$oauth_sina=sys_config_get('think_sdk_sina');
		$oauth_weixin=sys_config_get('think_sdk_weixin');
		$oauth_wechat=sys_config_get('think_sdk_wechat');
		$oauth_facebook=sys_config_get('think_sdk_facebook');
		$oauth_google=sys_config_get('think_sdk_google');
		$this->assign('oauth_qq',$oauth_qq);
		$this->assign('oauth_sina',$oauth_sina);
		$this->assign('oauth_wechat',$oauth_wechat);
		$this->assign('oauth_weixin',$oauth_weixin);
		return $this->fetch();
	}
	/**
	 * 保存第三方登录设置
	 */
	public function runoauthsys()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/oauthsys'));
		}else{
			$host=get_host();
			$data = array(
				'think_sdk_qq' => array(
					'app_key'	=> input('qq_appid'),
					'app_secret' => input('qq_appkey'),
					'display' => input('qq_display',0,'intval')?true:false,
					'callback'   => $host.url('home/Oauth/callback','type=qq'),
				),
				'think_sdk_weixin' => array(
					'app_key'	=> input('weixin_appid'),
					'app_secret' => input('weixin_appkey'),
					'display' => input('weixin_display',0,'intval')?true:false,
					'callback'   => $host.url('home/Oauth/callback','type=weixin'),
				),
				'think_sdk_wechat' => array(
					'app_key'	=> input('wechat_appid'),
					'app_secret' => input('wechat_appkey'),
					'display' => input('wechat_display',0,'intval')?true:false,
					'callback'   => $host.url('home/Oauth/callback','type=wechat'),
				),
				'think_sdk_sina' => array(
					'app_key'	=> input('sina_appid'),
					'app_secret' => input('sina_appkey'),
					'display' => input('sina_display',0,'intval')?true:false,
					'callback'   => $host.url('home/Oauth/callback','type=sina'),
				),
			);
			$rst=sys_config_setbyarr($data);
			if($rst){
				Cache::clear();
				$this->success('设置保存成功',url('admin/Sys/oauthsys'));
			}else{
				$this->error('设置保存失败',url('admin/Sys/oauthsys'));
			}
		}
	}
	/**
	 * 权限(后台菜单)列表
	 */
	public function admin_rule_list()
	{
		$pid=input('pid',0);
		$level=input('level',0);
		$id_str=input('id','pid');
		$admin_rule=Db::name('auth_rule')->where('pid',$pid)->order('sort')->select();
		$admin_rule_all=Db::name('auth_rule')->order('sort')->select();
		$arr = menu_left($admin_rule,'id','pid','─',$pid,$level,$level*20);
		$arr_all = menu_left($admin_rule_all,'id','pid','─',0,$level,$level*20);
		$this->assign('admin_rule',$arr);
		$this->assign('admin_rule_all',$arr_all);
		$this->assign('pid',$id_str);
		if(request()->isAjax()){
			return $this->fetch('ajax_admin_rule_list');
		}else{
			return $this->fetch();
		}
	}
	/**
	 * 显示添加权限(后台菜单)
	 */
	public function admin_rule_add()
	{
		$pid=input('pid',0);
		//全部规则
		$admin_rule_all=Db::name('auth_rule')->order('sort')->select();
		$arr = menu_left($admin_rule_all);
		$this->assign('admin_rule',$arr);
		$this->assign('pid',$pid);
		return $this->fetch();
	}
	/**
	 * 添加权限(后台菜单)
	 */
	public function admin_rule_runadd()
	{
		if(!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/admin_rule_list'));
		}else{
			$pid=Db::name('auth_rule')->where(array('id'=>input('pid')))->field('level')->find();
			$level=$pid['level']+1;
			$name=input('name');
			$name=AuthRule::check_name($name,$level);
			if($name){
				$sldata=array(
					'name'=>$name,
					'title'=>input('title'),
					'status'=>input('status',0,'intval'),
					'sort'=>input('sort',50,'intval'),
					'pid'=>input('pid'),
					'notcheck'=>input('notcheck',0,'intval'),
					'addtime'=>time(),
					'css'=>input('css',''),
					'level'=>$level,
				);
				Db::name('auth_rule')->insert($sldata);
				Cache::clear();
				$this->success('权限添加成功',url('admin/Sys/admin_rule_list'),1);
			}else{
				$this->error('控制器或方法不存在,或提交格式不规范',url('admin/Sys/admin_rule_list'));
			}
		}
	}
	/**
	 * 权限(后台菜单)显示/隐藏
	 */
	public function admin_rule_state()
	{
		$id=input('x');
		$statusone=Db::name('auth_rule')->where(array('id'=>$id))->value('status');//判断当前状态情况
		if($statusone==1){
			$statedata = array('status'=>0);
			Db::name('auth_rule')->where(array('id'=>$id))->setField($statedata);
			Cache::clear();
			$this->success('状态禁止');
		}else{
			$statedata = array('status'=>1);
			Db::name('auth_rule')->where(array('id'=>$id))->setField($statedata);
			Cache::clear();
			$this->success('状态开启');
		}
	}
	/**
	 * 权限(后台菜单)检测/不检测
	 */
	public function admin_rule_notcheck()
	{
		$id=input('x');
		$statusone=Db::name('auth_rule')->where(array('id'=>$id))->value('notcheck');//判断当前状态情况
		if($statusone==1){
			$statedata = array('notcheck'=>0);
			Db::name('auth_rule')->where(array('id'=>$id))->setField($statedata);
			Cache::clear();
			$this->success('检测');
		}else{
			$statedata = array('notcheck'=>1);
			Db::name('auth_rule')->where(array('id'=>$id))->setField($statedata);
			Cache::clear();
			$this->success('不检测');
		}
	}
	/**
	 * 权限(后台菜单)排序
	 */
	public function admin_rule_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/admin_rule_list'));
		}else{
			foreach ($_POST as $id => $sort){
				Db::name('auth_rule')->where(array('id' => $id ))->setField('sort' , $sort);
			}
			Cache::clear();
			$this->success('排序更新成功',url('admin/Sys/admin_rule_list'));
		}
	}
	/**
	 * 复制权限(后台菜单)
	 */
	public function admin_rule_copy()
	{
		//全部规则
		$admin_rule_all=Db::name('auth_rule')->order('sort')->select();
		$arr = menu_left($admin_rule_all);
		$this->assign('admin_rule',$arr);
		//待编辑规则
		$admin_rule=Db::name('auth_rule')->where(array('id'=>input('id')))->find();
		$this->assign('rule',$admin_rule);
		return $this->fetch();
	}
	/**
	 * 编辑权限(后台菜单)
	 */
	public function admin_rule_edit()
	{
		//全部规则
		$admin_rule_all=Db::name('auth_rule')->order('sort')->select();
		$arr = menu_left($admin_rule_all);
		$this->assign('admin_rule',$arr);
		//待编辑规则
		$admin_rule=Db::name('auth_rule')->where(array('id'=>input('id')))->find();
		$this->assign('rule',$admin_rule);
		return $this->fetch();
	}
	/**
	 * 保存编辑权限(后台菜单)
	 */
	public function admin_rule_runedit()
	{
		if(!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/admin_rule_list'));
		}else{
			$name=input('name');
			$old_pid=input('old_pid');
			$old_level=input('old_level',0,'intval');
			$pid=input('pid');
			$level_diff=0;
			//判断是否更改了pid
			if($pid!=$old_pid){
				$level=Db::name('auth_rule')->where('id',$pid)->value('level')+1;
				$level_diff=($level>$old_level)?($level-$old_level):($old_level-$level);
			}else{
				$level=$old_level;
			}
			$name=AuthRule::check_name($name,$level);
			if($name){
				$sldata=array(
					'id'=>input('id',1,'intval'),
					'name'=>$name,
					'title'=>input('title'),
					'status'=>input('status',0,'intval'),
					'notcheck'=>input('notcheck',0,'intval'),
					'pid'=>input('pid',0,'intval'),
					'css'=>input('css'),
					'sort'=>input('sort'),
					'level'=>$level
				);
				$rst=Db::name('auth_rule')->update($sldata);
				if($rst!==false){
					if($pid!=$old_pid){
						//更新子级菜单的level
						$auth_rule=Db::name('auth_rule')->order('sort')->select();
						$tree=new \Tree();
						$tree->init($auth_rule,['parentid'=>'pid']);
						$ids=$tree->get_childs($auth_rule,$sldata['id'],true,false);
						if($ids){
							if($level>$old_level){
								Db::name('auth_rule')->where('id','in',$ids)->setInc('level',$level_diff);
							}else{
								Db::name('auth_rule')->where('id','in',$ids)->setDec('level',$level_diff);
							}
						}
					}
					Cache::clear();
					$this->success('权限修改成功',url('admin/Sys/admin_rule_list'));
				}else{
					$this->error('权限修改失败',url('admin/Sys/admin_rule_list'));
				}
			}else{
				$this->error('控制器或方法不存在,或提交格式不规范',url('admin/Sys/admin_rule_list'));
			}
		}
	}
	/**
	 * 删除权限(后台菜单)
	 */
	public function admin_rule_del()
	{
		$pid=input('id');
		$arr=Db::name('auth_rule')->select();
		$tree=new \Tree();
		$tree->init($arr,['parentid'=>'pid']);
		$arrTree=$tree->get_childs($arr,$pid,true,true);
		if($arrTree){
			$rst=Db::name('auth_rule')->where('id','in',$arrTree)->delete();
			if($rst!==false){
				Cache::clear();
				$this->success('权限删除成功',url('admin/Sys/admin_rule_list'));
			}else{
				$this->error('权限删除失败',url('admin/Sys/admin_rule_list'));
			}
		}else{
			$this->error('权限删除失败',url('admin/Sys/admin_rule_list'));
		}
	}
	/*
	 * 显示备份页面
	 */
	public function database($type = null)
	{
		if(empty($type)){
			$type='export';
		}
		$title='';
		$list=array();
		switch ($type) {
			/* 数据还原 */
			case 'import':
				//列出备份文件列表
				$path=config('db_path');
				if (!is_dir($path)) {
					mkdir($path, 0755, true);
				}
				$path = realpath($path);
				$flag = \FilesystemIterator::KEY_AS_FILENAME;
				$glob = new \FilesystemIterator($path,  $flag);
				$list = array();
				foreach ($glob as $name => $file) {
					if(preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)){
						$name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');

						$date = "{$name[0]}-{$name[1]}-{$name[2]}";
						$time = "{$name[3]}:{$name[4]}:{$name[5]}";
						$part = $name[6];

						if(isset($list["{$date} {$time}"])){
							$info = $list["{$date} {$time}"];
							$info['part'] = max($info['part'], $part);
							$info['size'] = $info['size'] + $file->getSize();
						} else {
							$info['part'] = $part;
							$info['size'] = $file->getSize();
						}
						$extension		= strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
						$info['compress'] = ($extension === 'SQL') ? '-' : $extension;
						$info['time']	 = strtotime("{$date} {$time}");
						$list["{$date} {$time}"] = $info;
					}
				}
				$title = '数据还原';
				break;

			/* 数据备份 */
			case 'export':
				$list  = Db::query('SHOW TABLE STATUS FROM '.config('database.database'));
				$list  = array_map('array_change_key_case', $list);
				//过滤非本项目前缀的表
				foreach($list as $k=>$v){
					if(stripos($v['name'],strtolower(config('database.prefix')))!==0){
						unset($list[$k]);
					}
				}
				$title = '数据备份';
				break;
			default:
				$this->error('参数错误！');
		}
		//渲染模板
		$this->assign('meta_title', $title);
		$this->assign('data_list', $list);
		return $this->fetch($type);
	}
	/**
	 * 备份单表
	 * @param  String $table 不含前缀表名
	 */
	public function exportsql($table = null)
	{
		if($table){
			if(stripos($table,config('database.prefix'))==0){
				//含前缀的表,去除表前缀
				$table=str_replace(config('database.prefix'),"",$table);
			}
			if (!db_is_valid_table_name($table)) {
				$this->error("不存在表" . ' ' . $table);
			}
			force_download_content(date('Ymd') . '_' . config('database.prefix') . $table . '.sql', db_get_insert_sqls($table));
		}else{
			$this->error('未指定需备份的表');
		}
	}
	/*
	 * 备份数据
	 */
	public function export($tables = null, $id = null, $start = null)
	{
		if(request()->isPost() && !empty($tables) && is_array($tables)){ //初始化
			//读取备份配置
			$config = array(
				'path'	 => realpath(config('db_path')) . DS,
				'part'	 => config('db_part'),
				'compress' => config('db_compress'),
				'level'	=> config('db_level'),
			);
			//检查是否有正在执行的任务
			$lock = "{$config['path']}backup.lock";
			if(is_file($lock)){
				$this->error('检测到有一个备份任务正在执行，请稍后再试！');
			} else {
				//创建锁文件
				file_put_contents($lock, time());
			}
			//检查备份目录是否可写
			is_writeable($config['path']) || $this->error('备份目录不存在或不可写，请检查后重试！');
			session('backup_config', $config);
			//生成备份文件信息
			$file = array(
				'name' => date('Ymd-His', time()),
				'part' => 1,
			);
			session('backup_file', $file);
			//缓存要备份的表
			session('backup_tables', $tables);
			//创建备份文件
			$Database = new \Database($file, $config);
			if(false !== $Database->create()){
				$tab = array('id' => 0, 'start' => 0);
				return json(array('code'=>1,'tab' => $tab,'tables' => $tables,'msg'=>'初始化成功！'));
			} else {
				$this->error('初始化失败，备份文件创建失败！');
			}
		} elseif (request()->isGet() && is_numeric($id) && is_numeric($start)) { //备份数据
			$tables = session('backup_tables');
			//备份指定表
			$Database = new \Database(session('backup_file'), session('backup_config'));
			$start  = $Database->backup($tables[$id], $start);
			if(false === $start){ //出错
				$this->error('备份出错！');
			} elseif (0 === $start) { //下一表
				if(isset($tables[++$id])){
					$tab = array('id' => $id, 'start' => 0);
					return json(array('code'=>1,'tab' => $tab,'msg'=>'备份完成！'));
				} else { //备份完成，清空缓存
					unlink(session('backup_config.path') . 'backup.lock');
					session('backup_tables', null);
					session('backup_file', null);
					session('backup_config', null);
					return json(array('code'=>1,'msg'=>'备份完成！'));
				}
			} else {
				$tab  = array('id' => $id, 'start' => $start[0]);
				$rate = floor(100 * ($start[0] / $start[1]));
				return json(array('code'=>1,'tab' => $tab,'msg'=>"正在备份...({$rate}%)"));
			}
		} else { //出错
			$this->error('参数错误！');
		}
	}
	/**
	 * 优化表
	 * @param  String $tables 表名
	 */
	public function optimize($tables = null)
	{
		if($tables) {
			if(is_array($tables)){
				$tables = implode('`,`', $tables);
				$list = Db::query("OPTIMIZE TABLE `{$tables}`");
				if($list){
					$this->success("数据表优化完成！");
				} else {
					$this->error("数据表优化出错请重试！");
				}
			} else {
				$list = Db::query("OPTIMIZE TABLE `{$tables}`");
				if($list){
					$this->success("数据表'{$tables}'优化完成！");
				} else {
					$this->error("数据表'{$tables}'优化出错请重试！");
				}
			}
		} else {
			$this->error("请指定要优化的表！");
		}
	}
	/**
	 * 修复表
	 * @param  String $tables 表名
	 */
	public function repair($tables = null)
	{
		if($tables) {
			if(is_array($tables)){
				$tables = implode('`,`', $tables);
				$list = Db::query("REPAIR TABLE `{$tables}`");
				if($list){
					$this->success("数据表修复完成！");
				} else {
					$this->error("数据表修复出错请重试！");
				}
			} else {
				$list = Db::query("REPAIR TABLE `{$tables}`");
				if($list){
					$this->success("数据表'{$tables}'修复完成！");
				} else {
					$this->error("数据表'{$tables}'修复出错请重试！");
				}
			}
		} else {
			$this->error("请指定要修复的表！");
		}
	}
	/*
	 * 显示数据还原
	 */
	public function import()
	{
		$path=config('db_path');
		if (!is_dir($path)) {
			mkdir($path, 0755, true);
		}
		$path = realpath($path);
		$flag = \FilesystemIterator::KEY_AS_FILENAME;
		$glob = new \FilesystemIterator($path,$flag);

		$list = array();
		foreach ($glob as $name => $file) {
			if(preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)){
				$name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');

				$date = "{$name[0]}-{$name[1]}-{$name[2]}";
				$time = "{$name[3]}:{$name[4]}:{$name[5]}";
				$part = $name[6];

				if(isset($list["{$date} {$time}"])){
					$info = $list["{$date} {$time}"];
					$info['part'] = max($info['part'], $part);
					$info['size'] = $info['size'] + $file->getSize();
				} else {
					$info['part'] = $part;
					$info['size'] = $file->getSize();
				}
				$extension		= strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
				$info['compress'] = ($extension === 'SQL') ? '-' : $extension;
				$info['time']	 = strtotime("{$date} {$time}");

				$list["{$date} {$time}"] = $info;
			}
		}
		//渲染模板
		$this->assign('data_list', $list);
		return $this->fetch();
	}
	/**
	 * 删除备份文件
	 * @param  Integer $time 备份时间
	 */
	public function del($time = 0)
	{
		if($time){
			$name  = date('Ymd-His', $time) . '-*.sql*';
			$path  = realpath(config('db_path')) . DS . $name;
			array_map("unlink", glob($path));
			if(count(glob($path))){
				$this->error('备份文件删除失败，请检查权限！',url('admin/Sys/import'));
			} else {
				$this->success('备份文件删除成功！',url('admin/Sys/import'));
			}
		} else {
			$this->error('参数错误！',url('admin/Sys/import'));
		}
	}
	/*
	 * 还原数据
	 */
	public function restore($time = 0, $part = null, $start = null)
	{
		//读取备份配置
		$config = array(
			'path'	 => realpath(config('db_path')) . DS,
			'part'	 => config('db_part'),
			'compress' => config('db_compress'),
			'level'	=> config('db_level'),
		);
		if(is_numeric($time) && is_null($part) && is_null($start)){ //初始化
			//获取备份文件信息
			$name  = date('Ymd-His', $time) . '-*.sql*';
			$path  = realpath(config('db_path')) . DS . $name;
			$files = glob($path);
			$list  = array();
			foreach($files as $name){
				$basename = basename($name);
				$match	= sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
				$gz	   = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
				$list[$match[6]] = array($match[6], $name, $gz);
			}
			ksort($list);
			//检测文件正确性
			$last = end($list);
			if(count($list) === $last[0]){
				session('backup_list', $list); //缓存备份列表
				$this->restore(0,1,0);
			} else {
				$this->error('备份文件可能已经损坏，请检查！');
			}
		} elseif(is_numeric($part) && is_numeric($start)) {
			$list  = session('backup_list');
			$db = new \Database($list[$part],$config);
			$start = $db->import($start);
			if(false === $start){
				$this->error('还原数据出错！');
			} elseif(0 === $start) { //下一卷
				if(isset($list[++$part])){
					//$data = array('part' => $part, 'start' => 0);
					$this->restore(0,$part,0);
				} else {
					session('backup_list', null);
					$this->success('还原完成！',url('admin/Sys/import'));
				}
			} else {
				$data = array('part' => $part, 'start' => $start[0]);
				if($start[1]){
					$this->restore(0,$part, $start[0]);
				} else {
					$data['gz'] = 1;
					$this->restore(0,$part, $start[0]);
				}
			}
		} else {
			$this->error('参数错误！');
		}
	}
	/*
	 * 数据导出Excel
	 */
	public function excel_runexport($table)
	{
		export2excel($table);
	}
	/**
	 * workerman设置
	 */
	public function workerman()
	{
		$workerman=sys_config_get('workerman');
		$this->assign('workerman',$workerman);
		return $this->fetch();
	}
	/**
	 * workerman设置保存
	 */
	public function saveworkerman()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Sys/workerman'));
		}else{
			$config = input('config/a');
			//客服csport
			$config['csport']['to'] = $config['csport']['protocol'].'://'.$config['csport']['ip'].':'.$config['csport']['port'];
			//广告adpush
			$config['adpush']['to'] = $config['adpush']['protocol'].'://'.$config['adpush']['ip'].':'.$config['adpush']['port'];
			//前端webmsg
			$config['webmsg']['to'] = $config['webmsg']['protocol'].'://'.$config['webmsg']['ip'].':'.$config['webmsg']['port'];
			//APP端appmsg
			$config['appmsg']['to'] = $config['appmsg']['protocol'].'://'.$config['appmsg']['ip'].':'.$config['appmsg']['port'];
			//Register服务register
			$config['register']['to'] = $config['register']['protocol'].'://'.$config['register']['ip'].':'.$config['register']['port'];
			//businessworker服务对接register端口
			if($config['register']['ip']=='0.0.0.0'){
				$config['businessworker']['register'] = '127.0.0.1:'.$config['register']['port'];
			}else{
				$config['businessworker']['register'] = $config['register']['ip'].':'.$config['register']['port'];
			}
			
			//gateway服务gateway
			$config['gateway']['to'] = $config['gateway']['protocol'].'://'.$config['gateway']['ip'].':'.$config['gateway']['port'];
			//gateway服务对接register端口
			if($config['register']['ip']=='0.0.0.0'){
				$config['gateway']['register'] = '127.0.0.1:'.$config['register']['port'];
			}else{
				$config['gateway']['register'] = $config['register']['ip'].':'.$config['register']['port'];
			}
			$rst=sys_config_setbyarr(['workerman'=>$config]);
			if($rst!==false){
				Cache::clear();
				$this->success('设置保存成功',url('admin/Sys/workerman'));
			}else{
				$this->error('设置保存失败',url('admin/Sys/workerman'));
			}
		}
	}
	/*
	 * 清理缓存
	 */
	public function clear()
	{
		Cache::clear();
		$this->success ('清理缓存成功');
	}
 

//结束 
}