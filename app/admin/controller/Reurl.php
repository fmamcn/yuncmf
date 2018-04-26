<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | URL跳转系统管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use think\Validate;

class Reurl extends Base
{
	/*
	 * URL跳转列表
	 */
	public function reurl_list()
	{
		$reurl_list=Db::name('reurl')->order('sort desc')->paginate(18,false,['query'=>get_query()]);
		$show = $reurl_list->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('reurl_list',$reurl_list);
		$this->assign('page',$show);
		if(request()->isAjax()){
			return $this->fetch('ajax_reurl_list');
		}else{
			return $this->fetch();
		}   
	}
	/*
	 * 添加跳转规则
	 */
	public function reurl_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Reurl/reurl_list'));
		}else{
			$sl_data=array(
				'name'=>input('name'),
				'validate_url'=>input('validate_url'),
				'wait_time'=>input('wait_time'),
				'goto_url'=>input('goto_url'),
				'sort'=>input('sort'),
				'status'=>input('status'),
				'create_time'=>time(),
			);
			$rst=Db::name('reurl')->insert($sl_data);
			if($rst!==false){
				$this->success('跳转URL添加成功',url('admin/Reurl/reurl_list'));
			}else{
				$this->error('跳转URL添加失败',url('admin/Reurl/reurl_list'));
			}
		}
	}

	/*
	 * 删除跳转规则
	 */
	public function reurl_del()
	{
		$p=input('p');
		$rst=Db::name('reurl')->where(array('id'=>input('id')))->delete();
		if($rst!==false){
			$this->success('删除成功',url('admin/Reurl/reurl_list',array('p' => $p)));
		}else{
			$this->error('删除失败',url('admin/Reurl/reurl_list',array('p' => $p)));
		}
	}

	/*
	 * 跳转规则状态
	 */
	public function state()
	{
		$id=input('x');
		$status=Db::name('reurl')->where(array('id'=>$id))->value('status');//判断当前状态情况
		if($status==1){
			$statedata = array('status'=>0);
			Db::name('reurl')->where(array('id'=>$id))->setField($statedata);
			$this->success('状态禁止');
		}else{
			$statedata = array('status'=>1);
			Db::name('reurl')->where(array('id'=>$id))->setField($statedata);
			$this->success('状态开启');
		}
	}

	/*
	 * 跳转规则排序
	 */
	public function order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Reurl/reurl_list'));
		}else{
			$post=input('post.');
			foreach ($post as $id => $sort){
				Db::name('reurl')->where(array('id' => $id ))->setField('sort' , $sort);
			}
			$this->success('排序更新成功',url('admin/Reurl/reurl_list'));
		}
	}

	/*
	 * 返回跳转规则值
	 * @Author: LuckyZhaiZhai <979113800@qq.com>
	 */
	public function reurl_edit()
	{
		$id=input('id');
		$reurl=Db::name('reurl')->where(array('id'=>$id))->find();
		$sl_data['id']=$reurl['id'];
		$sl_data['name']=$reurl['name'];
		$sl_data['validate_url']=$reurl['validate_url'];
		$sl_data['wait_time']=$reurl['wait_time'];
		$sl_data['goto_url']=$reurl['goto_url'];
		$sl_data['sort']=$reurl['sort'];
		$sl_data['status']=$reurl['status'];
		$sl_data['code']=1;
		return json($sl_data);
	}

	/*
	 * 修改跳转规则
	 */
	public function reurl_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Reurl/reurl_list'));
		}else{
			$sl_data=array(
				'id'=>input('id'),
				'name'=>input('name'),
				'validate_url'=>input('validate_url'),
				'wait_time'=>input('wait_time'),
				'goto_url'=>input('goto_url'),
				'sort'=>input('sort'),
				'status'=>input('status'),
			);
			Db::name('reurl')->update($sl_data);
			$this->success('修改成功',url('admin/Reurl/reurl_list'));
		}
	}


//结束
}
