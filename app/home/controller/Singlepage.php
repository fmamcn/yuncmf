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
/**
 * 单页显示
*/
class Singlepage extends Base
{

	public function index() {
		$list_id=input('id');
		$menu=Db::name('menu')->find(input('id'));
		//获取同级菜单
		$menu_pid=$menu['parentid'];
		$menu_sibling=Db::name('menu')->where(array('parentid'=>$menu_pid,'menu_open'=>1))->select();
		$this->assign('menu_sibling',$menu_sibling);
		$this->assign('menu',$menu);
		//多图路径转换
		$allurl_text = $menu['pic_allurl'];
		$pic_list = array_filter(explode(",", $allurl_text));
		//多图说明转换
		$content_text = $menu['pic_content'];
		$pic_content_list = array_filter(explode(",", $content_text));
		$pic_content_url=array();
		if(!empty($pic_list)){
			foreach ($pic_list as $k=>$v){
				$pic_content_url[$k]['pic_url']=$v;
				$pic_content_url[$k]['pic_content']=$pic_content_list[$k];
			}
		}
//		return json($pic_content_url);
		$this->assign('pic_content_url',$pic_content_url);
		if(empty($menu)){
			$this->error('无效操作');
		}
		$tplname=$menu['menu_newstpl'];
		$tplname=$tplname?:'page';
    	$linktype=Db::name("plug_linktype")->order("plug_linktype_order asc")->select();
		$this->assign('linktype',$linktype);
		return $this->view->fetch(":$tplname");
	}
}
