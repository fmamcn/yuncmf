<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 手机APP端内容列表控制器
// +----------------------------------------------------------------------
namespace app\mobile\controller;

use think\Db;

class Listn extends Base
{
	/*
	 * 获取文章分类列表
	 * @param int $pagesize 分页大小
	 * @param int $page 第N页
	 */
	public function getArticleClassify() {
		//分页大小
		$pagesize = input("pagesize",10);
		//第N页
		$page = input('page');
        $article=Db::name("article_classify")->order('classify_order desc')->paginate($pagesize,false,['page'=>$page]);
		ajaxReturn($article);
	}
	/*
	 * 获取文章列表
	 * @param int $pagesize 分页大小
	 * @param int $page 第N页
	 * @param int $columnid 文章分类
	 */
	public function getArticle() {
		//分页大小
		$pagesize = input("pagesize",10);
		//第N页
		$page = input('page');
		//文章分类
		$columnid = input('columnid','');
		if(!empty($columnid)){
			$where['columnid'] = input('columnid');
		}
		//是否审核
	    $where['open'] = 1;
	    //是否在回收站
	    $where['back'] = 0;
        $article=Db::name("article")->alias("a")->field('a.*,b.member_username,b.member_nickname')
        			->join(config('database.prefix').'member b','a.auto =b.member_id')
        			->where($where)->order('time desc')->paginate($pagesize,false,['page'=>$page]);
		ajaxReturn($article);
	}

// 结束
}
