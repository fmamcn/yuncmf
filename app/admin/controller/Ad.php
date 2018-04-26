<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 广告管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use think\Validate;

class Ad extends Base
{
	/*
	 * 显示广告列表
	 */
	public function ad_list()
	{
		$key=input('key');
		$map['ad_name'] = array('like',"%".$key."%");
		$ad_type_list=Db::name('ad_type')->order('ad_type_order')->select();//获取所有广告位
		$ad_list=Db::name('ad')->alias("a")->join(config('database.prefix').'ad_type b','a.ad_typeid =b.ad_type_id')->where($map)->order('ad_order')->paginate(config('paginate.list_rows'),false,['query'=>get_query()]);
		$show = $ad_list->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('ad_list',$ad_list);
		$this->assign('ad_type_list',$ad_type_list);
		$this->assign('page',$show);
		$this->assign('val',$key);
		if(request()->isAjax()){
			return $this->fetch('ajax_ad_list');
		}else{
			return $this->fetch();
		}	
	}
	/*
	 * 添加广告
	 */
	public function ad_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Ad/ad_list'));
		}else{
			//构建数组
			$sl_data=array(
				'ad_typeid'=>input('ad_typeid'),
				'ad_name'=>input('ad_name'),
				'ad_pic'=>input('img_w',''),
				'ad_pic_h'=>input('img_h',''),
				'ad_url'=>input('ad_url',''),
				'ad_checkid'=>input('ad_checkid'),
				'ad_js'=>input('ad_js'),
				'ad_open'=>input('ad_open',0),
				'ad_order'=>input('ad_order',50,'intval'),
				'ad_content'=>input('ad_content',''),
				'ad_depid'=>input('ad_depid',''),
				'ad_butt'=>input('ad_butt',''),
				'ad_addtime'=>time(),
			);
			$rst=Db::name('ad')->insert($sl_data);
			if($rst!==false){
				$this->success('广告添加成功',url('admin/Ad/ad_list'));
			}else{
				$this->error('广告添加失败',url('admin/Ad/ad_list'));
			}
		}
	}

	/*
	 * 删除广告
	 */
	public function ad_del()
	{
		$ad_id=input('ad_id');
		$rst=Db::name('ad')->where(array('ad_id'=>$ad_id))->delete();
		if($rst!==false){
			$this->success('删除广告成功',url('admin/Ad/ad_list'));
		}else{
			$this->error('删除广告失败',url('admin/Ad/ad_list'));
		}
	}

	/*
	 * 广告排序
	 */
	public function ad_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Ad/ad_list'));
		}else{
			$post=input('post.');
			foreach ($post as $id => $sort){
				Db::name('ad')->where(array('ad_id' => $id ))->setField('ad_order' , $sort);
			}
			$this->success('广告排序更新成功',url('admin/Ad/ad_list'));
		}
	}

	/*
	 * 广告审核
	 */
	public function ad_state()
	{
		$id=input('x');
		$status=Db::name('ad')->where(array('ad_id'=>$id))->value('ad_open');//判断当前状态情况
		if($status==1){
			$statedata = array('ad_open'=>0);
			Db::name('ad')->where(array('ad_id'=>$id))->setField($statedata);
			$this->success('状态禁止');
		}else{
			$statedata = array('ad_open'=>1);
			Db::name('ad')->where(array('ad_id'=>$id))->setField($statedata);
			$this->success('状态开启');
		}
	}

	/*
	 * 显示修改页面
	 */
	public function ad_edit()
	{
		$ad_type_list=Db::name('ad_type')->select();
		$ad_id=input('ad_id');
		$ad=Db::name('ad')->where(array('ad_id'=>$ad_id))->find();
		$this->assign('ad_type_list',$ad_type_list);
		$this->assign('ad',$ad);
		return $this->fetch();
	}

	/*
	 * 修改广告
	 */
	public function ad_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Ad/ad_list'));
		}else{
			$sl_data=array(
				'ad_id'=>input('ad_id'),
				'ad_typeid'=>input('ad_typeid'),
				'ad_name'=>input('ad_name'),
				'ad_url'=>input('ad_url'),
				'ad_pic'=>input('img_w',''),
				'ad_pic_h'=>input('img_h',''),
				'ad_order'=>input('ad_order'),
				'ad_checkid'=>input('ad_checkid'),
				'ad_content'=>input('ad_content'),
				'ad_depid'=>input('ad_depid'),
				'ad_butt'=>input('ad_butt'),
				'ad_js'=>input('ad_js'),
			);
			$rst=Db::name('ad')->update($sl_data);
			if($rst!==false){
				$this->success('修改广告成功',url('admin/Ad/ad_list'));
			}else{
				$this->error('修改广告失败',url('admin/Ad/ad_list'));
			}
		}
	}
	/*
	 * 广告位列表
	 */
	public function ad_type_list()
	{
		$key=input('key');
		$map['ad_type_name '] = array('like',"%".$key."%");
		$ad_type_list=Db::name('ad_type')->where($map)->order('ad_type_order')->paginate(config('paginate.list_rows'),false,['query'=>get_query()]);
		$show = $ad_type_list->render();
		$this->assign('ad_type_list',$ad_type_list);
		$this->assign('page',$show);
		$this->assign('val',$key);
		return $this->fetch();
	}
	/*
	 * 添加广告位
	 */
	public function ad_type_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Ad/ad_type_list'));
		}else{
			$rst=Db::name('ad_type')->insert(input('post.'));
			if($rst!==false){
				$this->success('添加广告位成功',url('admin/Ad/ad_type_list'));			
			}else{
				$this->error('添加广告位失败',url('admin/Ad/ad_type_list'));	
			}
		}
	}

	/*
	 * 返回广告位值
	 */
	public function ad_type_edit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Ad/ad_type_list'));
		}else{
			$ad_type_id=input('ad_type_id');
			$ad_type=Db::name('ad_type')->where(array('ad_type_id'=>$ad_type_id))->find();
			$sl_data['ad_type_id']=$ad_type['ad_type_id'];
			$sl_data['ad_type_name']=$ad_type['ad_type_name'];
			$sl_data['css']=$ad_type['css'];
			$sl_data['js']=$ad_type['js'];
			$sl_data['code']=1;
			return json($sl_data);
		}
	}

	/*
	 * 修改广告位
	 */
	public function ad_type_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Ad/ad_type_list'));
		}else{
			$rst=Db::name('ad_type')->update(input('post.'));
			if($rst!==false){
				$this->success('广告位修改成功',url('admin/Ad/ad_type_list'));
			}else{
				$this->error('广告位修改失败',url('admin/Ad/ad_type_list'));
			}
			
		}
	}

	/*
	 * 删除广告位
	 */
	public function ad_type_del()
	{
		$p = input('p');
		$rst=Db::name('ad')->where(array('ad_typeid'=>input('ad_type_id')))->delete();//删除该广告位所有广告
		if($rst!==false){
			$rst=Db::name('ad_type')->where(array('ad_type_id'=>input('ad_type_id')))->delete();//删除广告位
			if($rst!==false){
				$this->success('删除广告位成功',url('admin/Ad/ad_type_list', array('p' => $p)));
			}else{
				$this->error('删除广告位失败',url('admin/Ad/ad_type_list', array('p' => $p)));
			}
		}else{
			$this->error('删除广告位失败',url('admin/Ad/ad_type_list', array('p' => $p)));
		}
	}

	/*
	 * 广告位排序
	 */
	public function ad_type_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Ad/ad_type_list'));
		}else{
			$post=input('post.');
			foreach ($post as $id => $sort){
				Db::name('ad_type')->where(array('ad_type_id' => $id ))->setField('ad_type_order' , $sort);
			}
			$this->success('排序更新成功',url('admin/Ad/ad_type_list'));
		}
	}

// 结束
}