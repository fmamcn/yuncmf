<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 会员管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use app\admin\model\Member;

class Members extends Base
{
	/*
	 * 显示会员列表
	 */
	public function member_list(){
		$key=input('key');
		$opentype_check=input('opentype_check','');
		$activetype_check=input('activetype_check','');
		// 按会员组排列
		$member_groupid=input('member_groupid','');
		$where=array();
		if($opentype_check !== ''){
			$where['member_open']=$opentype_check;
		}
		if($activetype_check !== ''){
			$where['user_status']=$activetype_check;
		}
		// 按会员组排列
		if($member_groupid !== ''){
			$where['member_groupid']=$member_groupid;
		}

		$member_model=new Member;
		$member=$member_model->alias('a')->join(config('database.prefix').'member_group b','a.member_groupid=b.member_group_id')->where($where)->where('member_username|member_email','like',"%".$key."%")->order('member_addtime desc')->paginate(config('paginate.list_rows'),false,['query'=>get_query()]);
		$show=$member->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('opentype_check',$opentype_check);
		$this->assign('activetype_check',$activetype_check);
		// 按会员组排列
		$this->assign('member_groupid',$member_groupid);
		$this->assign('member',$member);
		$this->assign('page',$show);
		$this->assign('val',$key);
		if(request()->isAjax()){
			return $this->fetch('ajax_member_list');
		}else{
			return $this->fetch();
		}
	}
	
	/*
	 * 显示添加页面
	 */
	public function member_add(){
		$province = Db::name('Region')->where ( array('pid'=>1) )->select ();
		$this->assign('province',$province);
		$member_group=Db::name('member_group')->order('member_group_order')->select();
		$this->assign('member_group',$member_group);
		return $this->fetch();
	}

	/*
	 * 添加会员
	 */
	public function member_runadd(){
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Members/member_list'));
		}else{
			$member_salt=random(10);
			$sl_data=array(
				'member_groupid'=>input('member_groupid'),
				'member_username'=>input('member_username'),
				'member_salt' => $member_salt,
				'member_pwd'=>encrypt_password(input('member_pwd'),$member_salt),
				'member_nickname'=>input('member_nickname'),
				'member_province'=>input('member_province'),
				'member_city'=>input('member_city'),
				'member_town'=>input('member_town'),
				'member_sex'=>input('member_sex'),
				'member_tel'=>input('member_tel'),
				'qq'=>input('qq'),
				'wechat'=>input('wechat'),
				'member_email'=>input('member_email'),
				'member_open'=>input('member_open',0),
				'user_url'=>input('user_url'),
				'member_addtime'=>time(),
				'user_status'=>input('user_status',0),
				'signature'=>input('signature'),
				'score'=>input('score',0,'intval'),
				'coin'=>input('coin',0,'intval'),
				'money'=>input('money',0,'intval'),
			);
			$rst=Member::create($sl_data);
			if($rst!==false){
				$this->success('会员添加成功',url('admin/Members/member_list'));
			}else{
				$this->error('会员添加失败',url('admin/Members/member_list'));
			}
		}
	}

	/*
	 * 修改会员页面
	 */
	public function member_edit(){
		$province = Db::name('Region')->where ( array('pid'=>1) )->select ();
		$member_group=Db::name('member_group')->order('member_group_order')->select();
		$member_edit=Db::name('member')->where(array('member_id'=>input('member_id')))->find();
		$city=Db::name('Region')->where ( array('pid'=>$member_edit['member_province']) )->select ();
		$town=Db::name('Region')->where ( array('pid'=>$member_edit['member_city']) )->select ();
		$this->assign('member_edit',$member_edit);
		$this->assign('province',$province);
		$this->assign('city',$city);
		$this->assign('town',$town);
		$this->assign('member_group',$member_group);
		return $this->fetch();
	}
	/*
	 * 修改会员信息
	 */
	public function member_runedit(){
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Members/member_list'));
		}else{
			$sl_data['member_id']=input('member_id');
			$sl_data['member_groupid']=input('member_groupid');
			$sl_data['member_username']=input('member_username');
			$pwd=input('member_pwd');
			if (!empty($pwd)){
				$member_salt=random(10);
				$sl_data['member_salt']=$member_salt;
				$sl_data['member_pwd']=encrypt_password($pwd,$member_salt);
			}
			$sl_data['member_nickname']=input('member_nickname');
			$sl_data['member_province']=input('member_province');
			$sl_data['member_city']=input('member_city');
			$sl_data['member_town']=input('member_town');
			$sl_data['member_sex']=input('member_sex');
			$sl_data['member_tel']=input('member_tel');
			$sl_data['member_email']=input('member_email');
			$sl_data['user_status']=input('user_status',0);
			$sl_data['member_open']=input('member_open',0);
			$sl_data['user_url']=input('user_url');
			$sl_data['signature']=input('signature');
			$sl_data['score']=input('score',0,'intval');
			$sl_data['coin']=input('coin',0,'intval');
			$sl_data['money']=input('money',0,'intval');
			$rst=Member::update($sl_data);
			if($rst!==false){
				$this->success('会员修改成功',url('admin/Members/member_list'));
			}else{
				$this->error('会员修改失败',url('admin/Members/member_list'));
			}
		}
	}
	/*
	 * 会员开启状态
	 */
	public function member_state(){
		$id=input('x');
		$member_model=new Member;
		$status=$member_model->where(array('member_id'=>$id))->value('member_open');//判断当前状态情况
		if($status==1){
			$statedata = array('member_open'=>0);
			$member_model->where(array('member_id'=>$id))->setField($statedata);
			$this->success('状态禁止');
		}else{
			$statedata = array('member_open'=>1);
			$member_model->where(array('member_id'=>$id))->setField($statedata);
			$this->success('状态开启');
		}
	}
	/*
	 * 会员激活状态
	 */
	public function member_active()
	{
		$id=input('x');
		$member_model=new Member;
		$status=$member_model->where(array('member_id'=>$id))->value('user_status');//判断当前状态情况
		if($status==1){
			$statedata = array('user_status'=>0);
			$member_model->where(array('member_id'=>$id))->setField($statedata);
			$this->success('未激活');
		}else{
			$statedata = array('user_status'=>1);
			$member_model->where(array('member_id'=>$id))->setField($statedata);
			$this->success('已激活');
		}
	}

	/*
	 * 删除会员
	 */
	public function member_del()
	{
		$p=input('p');
		$member_id=input('member_id');
		$member_model=new Member;
		$rst=Db::name('admin')->where('member_id',$member_id)->find();
		if($rst){
			$this->error('此会员已关联管理员,请从管理员处删除',url('admin/Members/member_list', array('p' => $p)));
		}else{
			$rst=$member_model->where(array('member_id'=>$member_id))->delete();
			if($rst!==false){
				$this->success('会员删除成功',url('admin/Members/member_list', array('p' => $p)));
			}else{
				$this->error('会员删除失败',url('admin/Members/member_list', array('p' => $p)));
			}
		}
	}
	/*
	 *显示会员组列表
	 */
	public function member_group_list()
	{
		$member_group=Db::name('member_group');
		$member_group_list=$member_group->order('member_group_order')->select();
		$this->assign('member_group_list',$member_group_list);
		return $this->fetch();
	}

	/*
	 * 添加会员组
	 */
	public function member_group_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Members/member_group_list'));
		}else{
			$rst=Db::name('member_group')->insert(input('post.'));
			if($rst!==false){
				$this->success('会员组添加成功',url('admin/Members/member_group_list'));
			}else{
				$this->error('会员组添加失败',url('admin/Members/member_group_list'));
			}
		}
	}

	/*
	 * 删除会员组
	 */
	public function member_group_del()
	{
		$member_group_id=input('member_group_id');
		if (empty($member_group_id)){
			$this->error('会员组ID不存在',url('admin/Members/member_group_list'));
		}
		$rst=Db::name('member_group')->where(array('member_group_id'=>input('member_group_id')))->delete();
		if($rst!==false){
			$this->success('会员组删除成功',url('admin/Members/member_group_list'));
		}else{
			$this->error('会员组删除失败',url('admin/Members/member_group_list'));
		}
	}

	/*
	 * 会员组状态
	 */
	public function member_group_state()
	{
		$member_group_id=input('x');
		if (!$member_group_id){
			$this->error('ID:'.$member_group_id.'不存在',url('admin/Members/member_group_list'));
		}
		$status=Db::name('member_group')->where(array('member_group_id'=>$member_group_id))->value('member_group_open');//判断当前状态情况
		if($status==1){
			$statedata = array('member_group_open'=>0);
			Db::name('member_group')->where(array('member_group_id'=>$member_group_id))->setField($statedata);
			$this->success('状态禁止');
		}else{
			$statedata = array('member_group_open'=>1);
			Db::name('member_group')->where(array('member_group_id'=>$member_group_id))->setField($statedata);
			$this->success('状态开启');
		}
	}

	/*
	 * 会员组排序
	 */
	public function member_group_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Members/member_group_list'));
		}else{
			$post=input('post.');
			foreach ($post as $id => $sort){
				Db::name('member_group')->where(array('member_group_id' => $id ))->setField('member_group_order' , $sort);
			}
			$this->success('排序更新成功',url('admin/Members/member_group_list'));
		}
	}

	/*
	 * 返回会员组值
	 */
	public function member_group_edit()
	{
		$member_group_id=input('member_group_id');
		$member_group=Db::name('member_group')->where(array('member_group_id'=>$member_group_id))->find();
		$sl_data['member_group_id']=$member_group['member_group_id'];
		$sl_data['member_group_name']=$member_group['member_group_name'];
		$sl_data['member_group_open']=$member_group['member_group_open'];
		$sl_data['member_group_toplimit']=$member_group['member_group_toplimit'];
		$sl_data['member_group_bomlimit']=$member_group['member_group_bomlimit'];
		$sl_data['member_group_order']=$member_group['member_group_order'];
		$sl_data['code']=1;
		return json($sl_data);
	}

	/*
	 * 修改会员组
	 */
	public function member_group_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Members/member_group_list'));
		}else{
			$sl_data=array(
				'member_group_id'=>input('member_group_id'),
				'member_group_name'=>input('member_group_name'),
				'member_group_toplimit'=>input('member_group_toplimit'),
				'member_group_bomlimit'=>input('member_group_bomlimit'),
				'member_group_order'=>input('member_group_order'),

			);
			$rst=Db::name('member_group')->update($sl_data);
			if($rst!==false){
				$this->success('会员组修改成功',url('admin/Members/member_group_list'));
			}else{
				$this->error('会员组修改失败',url('admin/Members/member_group_list'));
			}
		}
	}
}