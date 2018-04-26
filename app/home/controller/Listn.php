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
 * 文章列表
*/
class Listn extends Base
{

	public function index() {
		$list_id=input('id');
		$page=input('page');
		$pagesize=20;
		$menu=Db::name('menu')->find(input('id'));
		$menu_pid=$menu['parentid'];
		if($menu_pid==0){
			$menu_sibling=Db::name('menu')->where(array('parentid'=>3,'menu_open'=>1))->select();
		}else{
			$menu_sibling=Db::name('menu')->where(array('parentid'=>$menu_pid,'menu_open'=>1))->select();
		}
		
		$this->assign('menu_sibling',$menu_sibling);
		if(empty($menu)){
			$this->error('无效操作');
		}
		$tplname=$menu['menu_listtpl'];
		$tplname=$tplname?:'news_list';
		if($tplname=="pro_list") $pagesize=9;//相册格式
		if($menu['menu_type']==5){
			//如果menu_type值为5，则获取产品数据
			if(request()->isAjax()){
				$lists=get_product('cid:'.$list_id.';order:listorder desc',1,$pagesize,null,null,array(),$page);
				$tplname=":ajax_".$tplname;
			}else{
				$lists=get_product('cid:'.$list_id.';order:listorder desc',1,$pagesize);
			}
			//替换成带ajax的class
			$page_html=$lists['page'];
			$page_html=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$page_html);
		}elseif($menu['menu_type']==6){
			//如果menu_type值为6，则获取视频数据
			if(request()->isAjax()){
				$lists=get_video('cid:'.$list_id.';order:listorder desc',1,$pagesize,null,null,array(),$page);
				$tplname=":ajax_".$tplname;
			}else{
				$lists=get_video('cid:'.$list_id.';order:listorder desc',1,$pagesize);
			}
			//替换成带ajax的class
			$page_html=$lists['page'];
			$page_html=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$page_html);
		}else{
			//默认获取文章数据
			if(request()->isAjax()){
				$lists=get_article('cid:'.$list_id.';order:modified desc',1,$pagesize,null,null,array(),$page);
				$tplname=":ajax_".$tplname;
			}else{
				$lists=get_article('cid:'.$list_id.';order:modified desc',1,$pagesize);
			}
			//替换成带ajax的class
			$page_html=$lists['page'];
			$page_html=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$page_html);
			
		}
    	$linktype=Db::name("plug_linktype")->order("plug_linktype_order asc")->select();
		$this->assign('linktype',$linktype);
		$this->assign('menu',$menu);
		$this->assign('page_html',$page_html);
		$this->assign('lists',$lists);
		$this->assign('list_id', $list_id);
		return $this->view->fetch(":$tplname");
	}
    public function search()
    {
		$k = input("keyword");
		$page = input("post.page");
		$pagesize=20;
		$menu=Db::name('menu')->find(input('id'));
		$this->assign('menu',$menu);
		$menu_pid=$menu['parentid'];
		if($menu_pid==0){
			$menu_sibling=Db::name('menu')->where(array('parentid'=>3,'menu_open'=>1))->select();
		}else{
			$menu_sibling=Db::name('menu')->where(array('parentid'=>$menu_pid,'menu_open'=>1))->select();
		}
		$this->assign('menu_sibling',$menu_sibling);
    	$linktype=Db::name("plug_linktype")->order("plug_linktype_order asc")->select();
		$this->assign('linktype',$linktype);
		if (empty($k)) {
			$this -> error('请输入关键词搜索');
		}
		if(request()->isAjax()){
 			if(empty($page)){
				$this->success('success',url('home/Listn/search',array('keyword'=>$k)));
			}else{
				$lists=get_article('order:modified desc',1,$pagesize,'keyword',$k,array(),$page);
				//替换成带ajax的class
				$page_html=$lists['page'];
				$page_html=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$page_html);
				$this->assign('page_html',$page_html);
				$this->assign('lists',$lists);
				$this -> assign("keyword", $k);
				return $this->view->fetch(":ajax_news_list");				
			} 
		}else{
			$lists=get_article('order:modified desc',1,$pagesize,'keyword',$k);
			//替换成带ajax的class
			$page_html=$lists['page'];
			$page_html=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$page_html);
			$this->assign('page_html',$page_html);
			$this->assign('lists',$lists);
			$this -> assign("keyword", $k);		
			return $this->view->fetch(':search');
		}
    }
}
