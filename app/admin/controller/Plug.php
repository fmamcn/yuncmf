<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 扩展管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use think\Validate;

class Plug extends Base
{
	protected $files_res_exists;
	protected $files_res_used;
	protected $files_unused;
	/*
	 * 省份列表
	 */
	public function region()
	{
		$pagesize=38;
		$region=Db::name('region')->where('type','1')->order('id asc')->paginate($pagesize);
		$page = $region->render();
		$this->assign('region',$region);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加省份
	 */
	public function province_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/region'));
		}else{
			$data=input('post.');
			Db::name('region')->insert($data);
			$this->success('添加省份成功',url('admin/Plug/region'));
		}
	}
	/*
	 * 返回省份值
	 */
	public function province_edit()
	{
		$id=input('id');
		$province=Db::name('region')->where(array('id'=>$id))->find();
		$sl_data['id']=$province['id'];
		$sl_data['pid']=$province['pid'];
		$sl_data['name']=$province['name'];
		$sl_data['short']=$province['short'];
		$sl_data['center']=$province['center'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改省份
	 */
	public function province_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/region'));
		}else{
			$sl_data=array(
				'id'=>input('id'),
				'pid'=>input('pid'),
				'name'=>input('name'),
				'short'=>input('short'),
				'center'=>input('center'),
			);
			$rst=Db::name('region')->update($sl_data);
			if($rst!==false){
				$this->success('修改省份成功',url('admin/Plug/region'));
			}else{
				$this->error('修改省份失败',url('admin/Plug/region'));
			}
		}
	}
	/*
	 * 删除省份
	 */
	public function province_del()
	{
		//删除省份
		$pvrst=Db::name('region')->where(array('id'=>input('id')))->delete();
		//获取省份下所有城市id
		$city_id=Db::name('region')->where(array('pid'=>input('id')))->column('id');
		//删除省份下所有城市的县区
		for($i=0;$i<count($city_id);$i++){
			$corst=Db::name('region')->where(array('pid'=>$city_id[$i]))->delete();
		}
		//删除省份下城市
		$ctrst=Db::name('region')->where(array('pid'=>input('id')))->delete();
		if($pvrst!==false || $corst!==false || $ctrst!==false){
			$this->success('删除成功',url('admin/Plug/region'));
		}else{
			$this->error('删除失败',url('admin/Plug/region'));
		}
	}
	/*
	 * 城市列表
	 */
	public function city_list()
	{
		$pagesize=100;
		$pid=input('id');
		$city=Db::name('region')->where(array('pid'=>input('id')))->order('id asc')->paginate($pagesize);
		$ppname=Db::name('region')->where(array('id'=>input('id')))->value('name');
		$this->assign('ppname',$ppname);
		$page = $city->render();
		$this->assign('pid',$pid);
		$this->assign('city',$city);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加城市
	 */
	public function city_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/city_list',array('id' =>input('pid'))));
		}else{
			$data=input('post.');
			Db::name('region')->insert($data);
			$this->success('添加城市成功',url('admin/Plug/city_list',array('id' =>input('pid'))));
		}
	}
	/*
	 * 返回城市值
	 */
	public function city_edit()
	{
		$id=input('id');
		$city=Db::name('region')->where(array('id'=>$id))->find();
		$sl_data['id']=$city['id'];
		$sl_data['pid']=$city['pid'];
		$sl_data['name']=$city['name'];
		$sl_data['area_code']=$city['area_code'];
		$sl_data['zip_code']=$city['zip_code'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改城市
	 */
	public function city_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/city_list',array('id' =>input('pid'))));
		}else{
			$sl_data=array(
				'id'=>input('id'),
				'pid'=>input('pid'),
				'name'=>input('name'),
				'area_code'=>input('area_code'),
				'zip_code'=>input('zip_code'),
			);
			$rst=Db::name('region')->update($sl_data);
			if($rst!==false){
				$this->success('修改城市成功',url('admin/Plug/city_list',array('id' =>input('pid'))));
			}else{
				$this->error('修改城市失败',url('admin/Plug/city_list',array('id' =>input('pid'))));
			}
		}
	}
	/*
	 * 删除城市
	 */
	public function city_del()
	{
		//删除城市
		$rst=Db::name('region')->where(array('id'=>input('id')))->delete();
		//删除城市下县区
		$prst=Db::name('region')->where(array('pid'=>input('id')))->delete();
		if($rst!==false && $prst!==false){
			$this->success('删除成功',url('admin/Plug/city_list',array('id' =>input('pid'))));
		}else{
			$this->error('删除失败',url('admin/Plug/city_list',array('id' =>input('pid'))));
		}
	}
	/*
	 * 县区列表
	 */
	public function county_list()
	{
		$pagesize=100;
		$pid=input('id');
		$county=Db::name('region')->where(array('pid'=>input('id')))->order('id asc')->paginate($pagesize);
		$ppid=Db::name('region')->where(array('id'=>input('id')))->value('pid');
		//上级名称
		$ppname=Db::name('region')->where(array('id'=>input('id')))->value('name');
		$this->assign('ppname',$ppname);
		//上上级ID
		$pppid=Db::name('region')->where(array('id'=>$ppid))->value('pid');
		$this->assign('pppid',$pppid);
		//上上级名称
		$pppname=Db::name('region')->where(array('id'=>$ppid))->value('name');
		$this->assign('pppname',$pppname);
		$page = $county->render();
		$this->assign('ppid',$ppid);
		$this->assign('pid',$pid);
		$this->assign('county',$county);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加县区
	 */
	public function county_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/county_list',array('id' =>input('pid'))));
		}else{
			$data=input('post.');
			Db::name('region')->insert($data);
			$this->success('添加县区成功',url('admin/Plug/county_list',array('id' =>input('pid'))));
		}
	}
	/*
	 * 返回县区值
	 */
	public function county_edit()
	{
		$id=input('id');
		$county=Db::name('region')->where(array('id'=>$id))->find();
		$sl_data['id']=$county['id'];
		$sl_data['pid']=$county['pid'];
		$sl_data['name']=$county['name'];
		$sl_data['area_code']=$county['area_code'];
		$sl_data['zip_code']=$county['zip_code'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改县区
	 */
	public function county_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/county_list',array('id' =>input('pid'))));
		}else{
			$sl_data=array(
				'id'=>input('id'),
				'pid'=>input('pid'),
				'name'=>input('name'),
				'area_code'=>input('area_code'),
				'zip_code'=>input('zip_code'),
			);
			$rst=Db::name('region')->update($sl_data);
			if($rst!==false){
				$this->success('修改县区成功',url('admin/Plug/county_list',array('id' =>input('pid'))));
			}else{
				$this->error('修改县区失败',url('admin/Plug/county_list',array('id' =>input('pid'))));
			}
		}
	}
	/*
	 * 删除县区
	 */
	public function county_del()
	{
		$rst=Db::name('region')->where(array('id'=>input('id')))->delete();
		if($rst!==false){
			$this->success('删除成功',url('admin/Plug/county_list',array('id' =>input('pid'))));
		}else{
			$this->error('删除失败',url('admin/Plug/county_list',array('id' =>input('pid'))));
		}
	}
	/*
	 * 显示标签列表
	 */
	public function flag_list()
	{
		$flag=Db::name('plug_flag')->order('flag_order,flag_id desc')->paginate(config('paginate.list_rows'));
		$page = $flag->render();
		$this->assign('flag',$flag);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加标签
	 */
	public function flag_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/flag_list'));
		}else{
			$data=input('post.');
			Db::name('plug_flag')->insert($data);
			$this->success('添加标签成功',url('admin/Plug/flag_list'));
		}
	}
	/*
	 * 删除标签
	 */
	public function flag_del()
	{
		$p=input('p');
		$rst=Db::name('plug_flag')->where(array('flag_id'=>input('flag_id')))->delete();
		if($rst!==false){
			$this->success('删除标签成功',url('admin/Plug/flag_list',array('p' => $p)));
		}else{
			$this->error('删除标签失败',url('admin/Plug/flag_list',array('p' => $p)));
		}
	}
	/*
	 * 返回标签值
	 */
	public function flag_edit()
	{
		$flag_id=input('flag_id');
		$flag=Db::name('plug_flag')->where(array('flag_id'=>$flag_id))->find();
		$sl_data['flag_id']=$flag['flag_id'];
		$sl_data['flag_name']=$flag['flag_name'];
		$sl_data['flag_type']=$flag['flag_type'];
		$sl_data['flag_order']=$flag['flag_order'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改标签
	 */
	public function flag_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/flag_list'));
		}else{
			$sl_data=array(
				'flag_id'=>input('flag_id'),
				'flag_name'=>input('flag_name'),
				'flag_type'=>input('flag_type'),
				'flag_order'=>input('flag_order'),
			);
			$rst=Db::name('plug_flag')->update($sl_data);
			if($rst!==false){
				$this->success('修改标签成功',url('admin/Plug/flag_list'));
			}else{
				$this->error('修改标签失败',url('admin/Plug/flag_list'));
			}
		}
	}
	/*
	 * 标签排序
	 */
	public function flag_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/flag_list'));
		}else{
			foreach (input('post.') as $flag_id => $flag_order){
				Db::name('plug_flag')->where(array('flag_id' => $flag_id ))->setField('flag_order' , $flag_order);
			}
			$this->success('排序更新成功',url('admin/Plug/flag_list'));
		}
	}
	/*
	 * 显示来源列表
	 */
	public function source_list()
	{
		$source=Db::name('plug_source')->order('source_order,source_id desc')->paginate(config('paginate.list_rows'));
		$page = $source->render();
		$this->assign('source',$source);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加来源
	 */
	public function source_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/source_list'));
		}else{
			$data=input('post.');
			Db::name('plug_source')->insert($data);
			$this->success('添加来源成功',url('admin/Plug/source_list'));
		}
	}
	/*
	 * 删除来源
	 */
	public function source_del()
	{
		$p=input('p');
		$rst=Db::name('plug_source')->where(array('source_id'=>input('source_id')))->delete();
		if($rst!==false){
			$this->success('删除来源成功',url('admin/Plug/source_list',array('p' => $p)));
		}else{
			$this->error('删除来源失败',url('admin/Plug/source_list',array('p' => $p)));
		}
	}
	/*
	 * 返回来源值
	 */
	public function source_edit()
	{
		$source_id=input('source_id');
		$source=Db::name('plug_source')->where(array('source_id'=>$source_id))->find();
		$sl_data['source_id']=$source['source_id'];
		$sl_data['source_name']=$source['source_name'];
		$sl_data['source_order']=$source['source_order'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改来源
	 */
	public function source_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/source_list'));
		}else{
			$sl_data=array(
				'source_id'=>input('source_id'),
				'source_name'=>input('source_name'),
				'source_order'=>input('source_order'),
			);
			$rst=Db::name('plug_source')->update($sl_data);
			if($rst!==false){
				$this->success('修改来源成功',url('admin/Plug/source_list'));
			}else{
				$this->error('修改来源失败',url('admin/Plug/source_list'));
			}
		}
	}
	/*
	 * 来源排序
	 */
	public function source_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/source_list'));
		}else{
			foreach (input('post.') as $source_id => $source_order){
				Db::name('plug_source')->where(array('source_id' => $source_id ))->setField('source_order' , $source_order);
			}
			$this->success('排序更新成功',url('admin/Plug/source_list'));
		}
	}

	/*
	 * 友情链接列表
	 */
	public function plug_link_list()
	{
		$type=input('type');
		$val=input('val');
		$map=array();
		if (!empty($type)){
			$map['plug_link_typeid']=  array('eq',input('type'));
		}
		if (!empty($val)){
			$map['plug_link_name|plug_link_url'] = array('like',"%".$val."%");
		}
		$link_type=Db::name('plug_linktype')->select();
		$plug_link=Db::name('Plug_link')->alias("a")
					->join(config('database.prefix').'plug_linktype b','a.plug_link_typeid =b.plug_linktype_id')
					->where($map)->order('plug_link_addtime desc')->paginate(20,false,['query'=>get_query()]);
		$show = $plug_link->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('plug_link',$plug_link);
		$this->assign('link_type',$link_type);
		$this->assign('val',$val);
		$this->assign('type',$type);
		$this->assign('page',$show);
		if(request()->isAjax()){
			return $this->fetch('ajax_plug_link_list');
		}else{
			return $this->fetch();
		}	
	}

	/*
	 * 添加友情链接
	 */
	public function plug_link_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/plug_link_list'));
		}else{
			$sl_data=array(
				'plug_link_name'=>input('plug_link_name'),
				'plug_link_url'=>input('plug_link_url'),
				'plug_link_target'=>input('plug_link_target'),
				'plug_link_typeid'=>input('plug_link_typeid'),
				'plug_link_qq'=>input('plug_link_qq'),
				'plug_link_order'=>input('plug_link_order'),
				'plug_link_addtime'=>time(),
				'plug_link_open'=>input('plug_link_open',0),
			);
			$rst=Db::name('plug_link')->insert($sl_data);
			if($rst!==false){
				$this->success('添加链接成功',url('admin/Plug/plug_link_list'));
			}else{
				$this->error('添加链接失败',url('admin/Plug/plug_link_list'));
			}
		}
	}

	/*
	 * 删除友情链接
	 */
	public function plug_link_del()
	{
		$p=input('p');
		$rst=Db::name('plug_link')->where(array('plug_link_id'=>input('plug_link_id')))->delete();
		if($rst!==false){
			$this->success('删除链接成功',url('admin/Plug/plug_link_list',array('p' => $p)));
		}else{
			$this->error('删除链接失败',url('admin/Plug/plug_link_list',array('p' => $p)));
		}
	}

	/*
	 * 友情链接审核
	 */
	public function plug_link_state()
	{
		$id=input('x');
		$status=Db::name('plug_link')->where(array('plug_link_id'=>$id))->value('plug_link_open');//判断当前状态情况
		if($status==1){
			$statedata = array('plug_link_open'=>0);
			Db::name('plug_link')->where(array('plug_link_id'=>$id))->setField($statedata);
			$this->success('状态禁止');
		}else{
			$statedata = array('plug_link_open'=>1);
			Db::name('plug_link')->where(array('plug_link_id'=>$id))->setField($statedata);
			$this->success('状态开启');
		}
	}

	/*
	 * 返回友情链接值
	 */
	public function plug_link_edit()
	{
		$plug_link_id=input('plug_link_id');
		$plug_link=Db::name('plug_link')->where(array('plug_link_id'=>$plug_link_id))->find();
		$sl_data['plug_link_id']=$plug_link['plug_link_id'];
		$sl_data['plug_link_name']=$plug_link['plug_link_name'];
		$sl_data['plug_link_url']=$plug_link['plug_link_url'];
		$sl_data['plug_link_qq']=$plug_link['plug_link_qq'];
		$sl_data['plug_link_target']=$plug_link['plug_link_target'];
		$sl_data['plug_link_order']=$plug_link['plug_link_order'];
		$sl_data['plug_link_open']=$plug_link['plug_link_open'];
		$sl_data['plug_link_typeid']=$plug_link['plug_link_typeid'];
		$sl_data['code']=1;
		return json($sl_data);
	}

	/*
	 * 修改友情链接
	 */
	public function plug_link_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/plug_link_list'));
		}else{
			$sl_data=array(
				'plug_link_id'=>input('plug_link_id'),
				'plug_link_name'=>input('plug_link_name'),
				'plug_link_url'=>input('plug_link_url'),
				'plug_link_target'=>input('plug_link_target'),
				'plug_link_typeid'=>input('plug_link_typeid'),
				'plug_link_qq'=>input('plug_link_qq'),
				'plug_link_order'=>input('plug_link_order'),

			);
			Db::name('plug_link')->update($sl_data);
			$this->success('修改链接成功',url('admin/Plug/plug_link_list'));
		}
	}

	/*
	 * 友情链接类型列表
	 */
	public function plug_linktype_list()
	{
		$link_type=Db::name('plug_linktype')->order('plug_linktype_order')->select();
		$this->assign('link_type',$link_type);
		return $this->fetch();
	}
	/*
	 * 删除友情链接类型
	 */
	public function plug_linktype_del()
	{
		$rst=Db::name('plug_linktype')->where(array('plug_linktype_id'=>input('plug_linktype_id')))->delete();
		if($rst!==false){
			$this->success('友链类型删除成功',url('admin/Plug/plug_linktype_list'));
		}else{
			$this->error('友链类型删除失败',url('admin/Plug/plug_linktype_list'));
		}
	}

	/*
	 * 添加友情链接类型
	 */
	public function plug_linktype_runadd()
	{
		Db::name('plug_linktype')->insert(input('post.'));
		$this->success('栏目添加成功',url('admin/Plug/plug_linktype_list'));
	}

	/*
	 * 修改友情链接类型
	 * @Author: LuckyZhaiZhai <979113800@qq.com>
	 */
	public function plug_linktype_runedit()
	{
		$sl_data=array(
			'plug_linktype_id'=>input('plug_linktype_id'),
			'plug_linktype_name'=>input('plug_linktype_name'),
			'plug_linktype_order'=>input('plug_linktype_order'),
		);
		Db::name('plug_linktype')->update($sl_data);
		$this->success('修改成功',url('admin/Plug/plug_linktype_list'));
	}

	/*
	 * 友情链接类型排序
	 */
	public function plug_linktype_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Plug/plug_linktype_list'));
		}else{
			$post=input('post.');
			foreach ($post as $plug_linktype_id => $plug_linktype_order){
				Db::name('plug_linktype')->where(array('plug_linktype_id' => $plug_linktype_id ))->setField('plug_linktype_order' , $plug_linktype_order);
			}
			$this->success('排序更新成功',url('admin/Plug/plug_linktype_list'));
		}
	}
	/*
	* 本地文件列表
	*/
	public function plug_file_list()
	{
		$map=array();
		//查询：时间格式过滤
		$sldate=input('reservation','');//获取格式 2015-11-12 - 2015-11-18
		$arr = explode(" - ",$sldate);//转换成数组
		if(count($arr)==2){
			$arrdateone=strtotime($arr[0]);
			$arrdatetwo=strtotime($arr[1].' 23:59:59');
			$map['uptime'] = array(array('egt',$arrdateone),array('elt',$arrdatetwo),'AND');
		}
		//查询文件路径
		$val=input('val');
		if(!empty($val)){
			$map['path']= array('like',"%".$val."%");
		}
		$plug_files=Db::name('plug_files')->where($map)->order('id desc')->paginate(config('paginate.list_rows'),false,['query'=>get_query()]);
		$show = $plug_files->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('page',$show);
		$this->assign('plug_files',$plug_files);
		$this->assign('sldate',$sldate);
		$this->assign('val',$val);
		if(request()->isAjax()){
			return $this->fetch('ajax_plug_file_list');
		}else{
			return $this->fetch();
		}
	}
	/**
	 * 文件过滤
	 */
	public function plug_file_filter()
	{
		//获取本地文件数组
		$file_list=list_file('data/upload');
		$path="/data/upload/";
		$this->files_res_exists=array();
		foreach ($file_list as $a){
			if ($a ['isDir']) {
				foreach (list_file($a ['pathname'] . '/') as $d) {
					if (!$d ['isDir']) {
						//文件
						if($d['ext']!='html' && $d['ext']!='lock'){
							$this->files_res_exists [$path . $a ['filename'] . '/' . $d ['filename']] = $d ['size'];
						}
					}
				}
			}
		}
		//获取数据表datafile已存记录，并删除资源数组里的成员，完毕后得到未存入数据表datafile的资源文件
		$datas = Db::name('plug_files')->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				$f = $d ['path'];
				if (isset ($this->files_res_exists [$f])) {
					unset ($this->files_res_exists [$f]);
				}
			}
		}
		//未存入数据表的数据写入数据表
		$time=time();
		foreach ($this->files_res_exists as $d => $v) {
			Db::name('plug_files')->insert(array(
				'path' => $d,
				'uptime' => $time,
				'filesize' => $v
			));
		}
		//获取利用到的资源文件
		$this->files_res_used=array();
		//avatar,涉及表admin里字段admin_avatar，member里member_headpic,头像只保存头像图片名
		$datas = Db::name('admin')->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				if($d['admin_avatar']){
					if(stripos($d['admin_avatar'],'http')===false){
						//本地头像
						$this->files_res_used['/data/upload/avatar/' . $d['admin_avatar']]=true;
					}
				}
			}
		}
		$datas = Db::name('member')->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				if($d['member_headpic']){
					if(stripos($d['member_headpic'],'http')===false){
						//本地头像
						$this->files_res_used['/data/upload/avatar/' . $d['member_headpic']]=true;
					}
				}
			}
		}
		//article里的img,img_w,pic_allurl,content
		$datas = Db::name('article')->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				//字段img
				if($d['img']){
					if(stripos($d['img'],'http')===false){
						$this->files_res_used[$d['img']]=true;
					}
				}
				//字段img
				if($d['img_w']){
					if(stripos($d['img_w'],'http')===false){
						$this->files_res_used[$d['img_w']]=true;
					}
				}
				//字段pic_allurl
				if($d['pic_allurl']){
					$imgs=array_filter(explode(",",$d['pic_allurl']));
					foreach ($imgs as &$f) {
						if(stripos($f,'http')===false && !empty($f)){
							$this->files_res_used[$f]=true;
						}
					}
				}
				if($d['content']){
					//匹配'/网站目录/data/....'
					$preg_match=__ROOT__.'\/data\/upload\/([0-9]{4}[-][0-9]{2}[-][0-9]{2}\/[a-z0-9]{13}\.[a-z0-9]+)/i';
					@preg_match_all($preg_match, $d['content'], $mat);
					if(!empty($mat [1])){
						foreach ($mat [1] as &$f) {
							$this->files_res_used['/data/upload/'.$f]=true;
						}
					}

					//匹配'./data/....'
					$preg_match='/\.\/data\/upload\/([0-9]{4}[-][0-9]{2}[-][0-9]{2}\/[a-z0-9]{13}\.[a-z0-9]+)/i';
					@preg_match_all($preg_match, $d['content'], $mat);
					if(!empty($mat [1])){
						foreach ($mat [1] as &$f) {
							$this->files_res_used['/data/upload/'.$f]=true;
						}
					}
				}
			}
		}
		//video里的img,img_w,pic_allurl,content
		$datas = Db::name('video')->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				//字段img
				if($d['img']){
					if(stripos($d['img'],'http')===false){
						$this->files_res_used[$d['img']]=true;
					}
				}
				//字段img
				if($d['img_w']){
					if(stripos($d['img_w'],'http')===false){
						$this->files_res_used[$d['img_w']]=true;
					}
				}
				//字段pic_allurl
				if($d['pic_allurl']){
					$imgs=array_filter(explode(",",$d['pic_allurl']));
					foreach ($imgs as &$f) {
						if(stripos($f,'http')===false && !empty($f)){
							$this->files_res_used[$f]=true;
						}
					}
				}
				if($d['content']){
					//匹配'/网站目录/data/....'
					$preg_match=__ROOT__.'\/data\/upload\/([0-9]{4}[-][0-9]{2}[-][0-9]{2}\/[a-z0-9]{13}\.[a-z0-9]+)/i';
					@preg_match_all($preg_match, $d['content'], $mat);
					if(!empty($mat [1])){
						foreach ($mat [1] as &$f) {
							$this->files_res_used['/data/upload/'.$f]=true;
						}
					}

					//匹配'./data/....'
					$preg_match='/\.\/data\/upload\/([0-9]{4}[-][0-9]{2}[-][0-9]{2}\/[a-z0-9]{13}\.[a-z0-9]+)/i';
					@preg_match_all($preg_match, $d['content'], $mat);
					if(!empty($mat [1])){
						foreach ($mat [1] as &$f) {
							$this->files_res_used['/data/upload/'.$f]=true;
						}
					}
				}
			}
		}
		//product里的img,img_w,pic_allurl,content
		$datas = Db::name('product')->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				//字段img
				if($d['img']){
					if(stripos($d['img'],'http')===false){
						$this->files_res_used[$d['img']]=true;
					}
				}
				//字段img
				if($d['img_w']){
					if(stripos($d['img_w'],'http')===false){
						$this->files_res_used[$d['img_w']]=true;
					}
				}
				//字段pic_allurl
				if($d['pic_allurl']){
					$imgs=array_filter(explode(",",$d['pic_allurl']));
					foreach ($imgs as &$f) {
						if(stripos($f,'http')===false && !empty($f)){
							$this->files_res_used[$f]=true;
						}
					}
				}
				if($d['intro']){
					//匹配'/网站目录/data/....'
					$preg_match=__ROOT__.'\/data\/upload\/([0-9]{4}[-][0-9]{2}[-][0-9]{2}\/[a-z0-9]{13}\.[a-z0-9]+)/i';
					@preg_match_all($preg_match, $d['content'], $mat);
					if(!empty($mat [1])){
						foreach ($mat [1] as &$f) {
							$this->files_res_used['/data/upload/'.$f]=true;
						}
					}

					//匹配'./data/....'
					$preg_match='/\.\/data\/upload\/([0-9]{4}[-][0-9]{2}[-][0-9]{2}\/[a-z0-9]{13}\.[a-z0-9]+)/i';
					@preg_match_all($preg_match, $d['content'], $mat);
					if(!empty($mat [1])){
						foreach ($mat [1] as &$f) {
							$this->files_res_used['/data/upload/'.$f]=true;
						}
					}
				}
			}
		}
		//options里'option_name'=>'site_options'的site_logo、site_qr,字段保存'/data/....'
		$datas = Db::name('options')->where(array('option_name'=>'site_options'))->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				if($d['option_value']){
					$smeta=json_decode($d['option_value'],true);
					if($smeta['site_logo'] && stripos($smeta['site_logo'],'http')===false){
						$this->files_res_used[$smeta['site_logo']]=true;
					}
					if(!empty($smeta['site_qr']) && stripos($smeta['site_qr'],'http')===false){
						$this->files_res_used[$smeta['site_qr']]=true;
					}
				}
			}
		}
		//ad里ad_pic,字段保存'/data/....'
		$datas = Db::name('ad')->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				if($d['ad_pic']){
					if(stripos($d['ad_pic'],'http')===false){
						//本地图片
						$this->files_res_used[$d['ad_pic']]=true;
					}
				}
			}
		}
		//menu里menu_img,字段保存'/data/....'
		$datas = Db::name('menu')->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				if($d['menu_img']){
					if(stripos($d['menu_img'],'http')===false){
						//本地图片
						$this->files_res_used[$d['menu_img']]=true;
					}
				}
			}
		}
		//找出未使用的资源文件
		$this->files_unused=array();
		$ids=array();
		$datas = Db::name('plug_files')->select();
		if (is_array($datas)) {
			foreach ($datas as &$d) {
				$f = $d ['path'];
				if (isset ($this->files_res_used[$f])) {
					unset ($this->files_res_used[$f]);
				} else {
					$ids[]=$d ['id'];
					$this->files_unused [] = array(
						'id' => $d ['id'],
						'filesize' =>$d['filesize'],
						'path' => $f,
						'uptime' => $d ['uptime']
					);
				}
			}
		}
		//数据库
		$where=array();
		$plug_files=array();
		$show='';
		if(!empty($ids)){
			$where['id']=array('in',$ids);
			$plug_files=Db::name('plug_files')->where($where)->order('id desc')->paginate(config('paginate.list_rows'));
			$show = $plug_files->render();
			$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		}
		$this->assign('plug_files',$plug_files);
		$this->assign('page',$show);
		if(request()->isAjax()){
			return $this->fetch('ajax_plug_file_filter');
		}else{
			return $this->fetch();
		}
	}
	/**
	 * 删除文件(全选)
	 */
	public function plug_file_alldel(){
		$p = input('p');
		$ids = input('id/a');
		if(empty($ids)){
			$this -> error("请选择要删除的文件",url('admin/Plug/plug_file_filter',array('p'=>$p)));
		}
		if(is_array($ids)){
			$where = 'id in('.implode(',',$ids).')';
			foreach (Db::name('plug_files')->field('path')->where($where)->select() as $r) {
				$file = $r ['path'];
				if(stripos($file, "/")===0){
					$file=substr($file,1);
				}
				if (file_exists($file)) {
					unlink($file);
				}
			}
			if (Db::name('plug_files')->where($where)->delete()!==false) {
				$this->success("删除文件成功！",url('admin/Plug/plug_file_filter',array('p'=>$p)));
			} else {
				$this->error("删除文件失败！",url('admin/Plug/plug_file_filter',array('p'=>$p)));
			}
		}else{
			$r=Db::name('plug_files')->find($ids);
			if($r){
				$file=$r['path'];
				if(stripos($file, "/")===0){
					$file=substr($file,1);
				}
				if (file_exists($file)) {
					unlink($file);
				}
				if (Db::name('plug_files')->delete($ids)!==false) {
					$this->success("删除文件成功！",url('admin/Plug/plug_file_filter',array('p'=>$p)));
				}else{
					$this->error("删除文件失败！",url('admin/Plug/plug_file_filter',array('p'=>$p)));
				}
			}else{
				$this->error("删除文件失败！",url('admin/Plug/plug_file_filter',array('p'=>$p)));
			}
		}
	}
	/**
	 * 删除文件(单个)
	 */
	public function plug_file_del()
	{
		$id=input('id');
		$p = input('p');
		if (empty($id)){
			$this->error('参数错误',url('admin/Plug/plug_file_filter',array('p'=>$p)));
		}else{
			$r=Db::name('plug_files')->find($id);
			if($r){
				$file=$r['path'];
				if(stripos($file, "/")===0){
					$file=substr($file,1);
				}
				if (file_exists($file)) {
					unlink($file);
				}
				if (Db::name('plug_files')->delete($id)!==false) {
					$this->success("删除文件成功！",url('admin/Plug/plug_file_filter',array('p'=>$p)));
				}else{
					$this->error("删除文件失败！",url('admin/Plug/plug_file_filter',array('p'=>$p)));
				}
			}else{
				$this->error("删除文件失败！",url('admin/Plug/plug_file_filter',array('p'=>$p)));
			}
		}
	} 

// 结束
}