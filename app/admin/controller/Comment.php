<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 评论管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Cache;
use think\Db;

class Comment extends Base
{
	/*
	 * 显示评论列表
	 */
	public function comment_list()
	{
		$pagesize=input('pagesize',20);
		$comments=Db::name('comments')->alias("a")->join(config('database.prefix').'member b','a.uid =b.member_id')->order('createtime desc')->paginate($pagesize);
		$show=$comments->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('comments',$comments);
		$this->assign('page',$show);
		if(request()->isAjax()){
			return $this->fetch('ajax_comment_list');
		}else{
			return $this->fetch();
		}
	}
	/*
	 * 删除评论(单个)
	 */
	public function comment_del()
	{
		$p=input('p');
		$c_id=input('c_id');
		$rst=Db::name('comments')->where(array('c_id'=>$c_id))->find();
		if($rst){
			$path=$rst['path'];
			//所有以$path开头的都删除
			$rst=Db::name('comments')->where(array('path'=>array('like',$path.'%')))->delete();
			if($rst!==false){
				$this->success('删除评论成功',url('admin/Comment/comment_list',array('p'=>$p)));
			}else{
				$this->error('删除评论失败',url('admin/Comment/comment_list',array('p'=>$p)));
			}
		}else{
			$this->error('评论不存在',url('admin/Comment/comment_list',array('p'=>$p)));
		}
	}
	/*
	 * 删除评论(全选)
	 */
	public function comment_alldel()
	{
		$p = input('p');
		$ids = input('c_id/a');
		if(empty($ids)){
			$this -> error("请选择需要删除的评论",url('admin/Comment/comment_list',array('p'=>$p)));
		}
		if(!is_array($ids)){
			$ids[]=$ids;
		}
		foreach($ids as $c_id){
			$rst=Db::name('comments')->where(array('c_id'=>$c_id))->find();
			if($rst){
				$path=$rst['path'];
				Db::name('comments')->where(array('path'=>array('like',$path.'%')))->delete();
			}
		}
		$this->success("删除评论成功",url('admin/Comment/comment_list',array('p'=>$p)));
	}
	/*
	 * 评论审核
	 */
	public function comment_state()
	{
		$id=input('x');
		$status=Db::name('comments')->where(array('c_id'=>$id))->value('c_status');//判断当前状态情况
		if($status==1){
			$statedata = array('c_status'=>0);
			Db::name('comments')->where(array('c_id'=>$id))->setField($statedata);
			$this->success('未审');
		}else{
			$statedata = array('c_status'=>1);
			Db::name('comments')->where(array('c_id'=>$id))->setField($statedata);
			$this->success('已审');
		}
	}
	/*
	 * 显示设置页面
	 */
	public function comment_setting()
	{
		$csys=config('comment');
		$this->assign('csys',$csys);
		return $this->fetch();
	}
	/*
	 * 保存评论设置
	 */
	public function runcsys()
	{
		$t_open=input('t_open',0)?true:false;
		$t_limit=input('t_limit',60,'intval');
		$data = array(
			'comment' => array(
				't_open'=> $t_open,
				't_limit'=> $t_limit,
			),
		);
		$rst=sys_config_setbyarr($data);
		Cache::clear();
		if($rst){
			$this->success('评论设置成功',url('admin/Comment/comment_setting'));
		}else{
			$this->error('评论设置失败',url('admin/Comment/comment_setting'));
		}
	}
}