<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 前台菜单管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use app\admin\model\Menu as MenuModel;
use app\admin\model\Options as OptionsModel;

class Menu extends Base
{
	public function _initialize()
	{
		parent::_initialize();
	}
	/**
	 * 显示菜单列表
	 */
	public function menu_list()
	{
		$menu_model=new MenuModel;
		$menus=$menu_model->order('listorder')->select();
		$show='';
		$arr = menu_left($menus,'id','parentid');
		$this->assign('arr',$arr);
		$this->assign('page',$show);
		if(request()->isAjax()){
			return $this->fetch('ajax_menu_list');
		}else{
			return $this->fetch();
		}
	}
	/**
	 * 菜单排序
	 */
	public function menu_order(){
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Menu/menu_list'));
		}else{
			$list=[];
			foreach (input('post.') as $id => $sort){
				$list[]=['id'=>$id,'listorder'=>$sort];
			}
			$menu_model=new MenuModel;
			$menu_model->saveAll($list);
			cache('site_nav_main',null);
			$this->success('排序更新成功',url('admin/Menu/menu_list'));
		}
	}
	/**
	 * 菜单开启状态
	 */
	public function menu_state()
	{
		$id=input('x');
		$menu_model=new MenuModel;
		$status=$menu_model->where(array('id'=>$id))->value('menu_open');//判断当前状态情况
		if($status==1){
			$statedata = array('menu_open'=>0);
			$menu_model->where(array('id'=>$id))->setField($statedata);
			cache('site_nav_main',null);
			$this->success('状态禁止');
		}else{
			$statedata = array('menu_open'=>1);
			$menu_model->where(array('id'=>$id))->setField($statedata);
			cache('site_nav_main',null);
			$this->success('状态开启');
		}
	}
	/**
	 * 删除菜单
	 */
	public function menu_del()
	{
		$id=input('id');
		$arr=MenuModel::get($id);
		$parentid=$arr['parentid'];
		$parent_arr=MenuModel::get($parentid);
		$ids=get_menu_byid($id,1,2);//返回含自身id及子菜单id数组
		$rst=MenuModel::destroy($ids);
		$menu_model=new MenuModel;
		//处理父级
		if($rst!==false){
			//判断其父菜单是否还存在子菜单，如无子菜单，且父菜单类型为1
			if($parentid && $parent_arr['menu_type']==1){
				$child=$menu_model->where(array('parentid'=>$parentid))->select();
				if(empty($child)){
					$menu_model->where(array('id'=>$parentid))->update(['menu_type'=>3]);
				}
			}
			cache('site_nav_main',null);
			$this->success('菜单删除成功',url('admin/Menu/menu_list'));
		}else{
			$this -> error("菜单删除失败！",url('admin/Menu/menu_list'));
		}
	}
	/**
	 * 显示添加页面
	 */
	public function menu_add()
	{
		$parentid=input('id',0);
		$menu_model=new MenuModel;
		$options_model=new OptionsModel;
		$tpls=$options_model->tpls();
		$this->assign('parentid',$parentid);
		$this->assign('tpls',$tpls);
		//产品分类列表
		$product_classify=Db::name('product_classify')->where('type','3')->order('id asc')->select();
		$this->assign('product_classify',$product_classify);
		//视频分类列表
		$video_classify=Db::name('video_classify')->order('classify_id asc')->select();
		$this->assign('video_classify',$video_classify);
		//文章分类列表
		$article_classify=Db::name('article_classify')->order('classify_id asc')->select();
		$this->assign('article_classify',$article_classify);
		return $this->fetch();
	}
	/**
	 * 添加前台菜单
	 */
	public function menu_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Menu/menu_list'));
		}else{
			//上传图片部分
			$img_w='';
			$img_h='';
			$picall_url='';
			$file_w = input('pic_w','');
			$file_h = input('pic_h','');
			$files = input('pic_all','');
			if($file_w || $file_h || $files) {
				$validate = config('upload_validate');
				//单图
				if ($file_w || $file_h) {
					$img_w=$file_w;
					$img_h=$file_h;
				}
				//多图
				if ($files) {
					$picall_url=$files;
				}
			}
			//处理分类信息
			switch(input('menu_type')){
				//文章
				case '3':
					//获取文章分类
					$article_classify=input('post.article_classify/a');
					$classifydata=array();
					if(!empty($article_classify)){
						foreach ($article_classify as $v){
							$classifydata[]=$v;
						}
					}
					$classify=implode(',',$classifydata);
					break;
				//产品
				case '5':
					//获取产品分类
					$product_classify=input('post.product_classify/a');
					$classifydata=array();
					if(!empty($product_classify)){
						foreach ($product_classify as $v){
							$classifydata[]=$v;
						}
					}
					$classify=implode(',',$classifydata);
					break;
				//视频
				case '6':
					//获取视频分类
					$video_classify=input('post.video_classify/a');
					$classifydata=array();
					if(!empty($video_classify)){
						foreach ($video_classify as $v){
							$classifydata[]=$v;
						}
					}
					$classify=implode(',',$classifydata);
					break;
				default:
					$classify='';
			}
			//构建数组
			$data=array(
				'menu_name'=>input('menu_name'),
				'menu_type'=>input('menu_type'),
				'parentid'=>input('parentid'),
				'classifyid'=>$classify,
				'menu_listtpl'=>input('menu_listtpl'),
				'menu_newstpl'=>input('menu_newstpl'),
				'menu_address'=>input('menu_address'),
				'menu_open'=>input('menu_open',0),
				'listorder'=>input('listorder'),
				'menu_seo_title'=>input('menu_seo_title'),
				'menu_seo_key'=>input('menu_seo_key'),
				'menu_seo_des'=>input('menu_seo_des'),
				'menu_content'=>htmlspecialchars_decode(input('menu_content')),
				'img_w'=>$img_w,//封面图片(横屏)路径
				'img'=>$img_h,//封面图片(竖屏)路径
				'pic_allurl'=>$picall_url,//多图路径
				'pic_content'=>input('pic_content',''),
			);
			$rst=MenuModel::create($data);
			$menu_model=new MenuModel;
			if($rst!==false){
				$arr=MenuModel::get(input('parentid'));
				if(input('menu_type')==3 && $arr['menu_type']==3){
					$menu_model->where(array('id'=>input('parentid')))->setField('menu_type',1);
				}
				cache('site_nav_main',null);
				$this->success('菜单添加成功',url('admin/Menu/menu_list'));
			}else{
				$this->error('菜单添加失败',url('admin/Menu/menu_list'));
			}
		}
	}
	/**
	 * 显示编辑页面
	 */
	public function menu_edit()
	{
		$menu=MenuModel::get(input('id'));
		$options_model=new OptionsModel;
		$tpls=$options_model->tpls();
		$this->assign('menu',$menu);
		$this->assign('tpls',$tpls);
		//多图路径转换
		$allurl_text = $menu['pic_allurl'];
		$this->assign('pic_list',$allurl_text);
		//多图说明转换
		$content_text = $menu['pic_content'];
		$this->assign('pic_content_list',$content_text);
		//产品分类列表
		$product_classify=Db::name('product_classify')->where('type','3')->order('id asc')->select();
		$this->assign('product_classify',$product_classify);
		//视频分类列表
		$video_classify=Db::name('video_classify')->order('classify_id asc')->select();
		$this->assign('video_classify',$video_classify);
		//文章分类列表
		$article_classify=Db::name('article_classify')->order('classify_id asc')->select();
		$this->assign('article_classify',$article_classify);
		return $this->fetch();
	}
	/**
	 * 编辑前台菜单
	 */
	public function menu_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Menu/menu_list'));
		}else{
			//上传图片部分
			$img_w='';
			$img_h='';
			$picall_url='';
			$file_w = input('pic_w','');
			$file_h = input('pic_h','');
			$files = input('pic_all','');
			if($file_w || $file_h || $files) {
				$validate = config('upload_validate');
				//单图
				if ($file_w || $file_h) {
					$img_w=$file_w;
					$img_h=$file_h;
				}
				//多图
				if ($files) {
					$picall_url=$files;
				}
			}
			//处理分类信息
			switch(input('menu_type')){
				//文章
				case '3':
					//获取文章分类
					$article_classify=input('post.article_classify/a');
					$classifydata=array();
					if(!empty($article_classify)){
						foreach ($article_classify as $v){
							$classifydata[]=$v;
						}
					}
					$classify=implode(',',$classifydata);
					break;
				//产品
				case '5':
					//获取产品分类
					$product_classify=input('post.product_classify/a');
					$classifydata=array();
					if(!empty($product_classify)){
						foreach ($product_classify as $v){
							$classifydata[]=$v;
						}
					}
					$classify=implode(',',$classifydata);
					break;
				//视频
				case '6':
					//获取视频分类
					$video_classify=input('post.video_classify/a');
					$classifydata=array();
					if(!empty($video_classify)){
						foreach ($video_classify as $v){
							$classifydata[]=$v;
						}
					}
					$classify=implode(',',$classifydata);
					break;
				default:
					$classify='';
			}
			
			$data=array(
				'id'=>input('id'),
				'menu_name'=>input('menu_name'),
				'menu_type'=>input('menu_type'),
				'parentid'=>input('parentid'),
				'classifyid'=>$classify,
				'menu_listtpl'=>input('menu_listtpl'),
				'menu_newstpl'=>input('menu_newstpl'),
				'menu_address'=>input('menu_address'),
				'menu_open'=>input('menu_open',0),
				'listorder'=>input('listorder'),
				'menu_seo_title'=>input('menu_seo_title'),
				'menu_seo_key'=>input('menu_seo_key'),
				'menu_seo_des'=>input('menu_seo_des'),
				'menu_content'=>htmlspecialchars_decode(input('menu_content')),
				'img_w'=>$img_w,//封面图片(横屏)路径
				'img'=>$img_h,//封面图片(竖屏)路径
				'pic_allurl'=>$picall_url,//多图路径
				'pic_content'=>input('pic_content',''),
			);
			
			$rst=MenuModel::update($data);
			if($rst!==false){
				cache('site_nav_main',null);
				$this->success('菜单修改成功',url('admin/Menu/menu_list'));
			}else{
				$this->error('菜单修改失败',url('admin/Menu/menu_list'));
			}
		}
	}

//结束
}