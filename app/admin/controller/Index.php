<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 后台首页控制器
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use think\Cache;
use think\helper\Time;
use app\admin\model\Article as ArticleModel;
use app\admin\model\Member;

class Index extends Base
{
	public function index()
	{
		$article_model=new ArticleModel;
		$member_model=new Member;
		//热门文章排行
		$article_list=$article_model->order('hits desc')->limit(0,10)->select();
		$this->assign('article_list',$article_list);
		//总文章数
		$article_count=$article_model->count();
		$this->assign('article_count',$article_count);
		//总会员数
		$members_count=$member_model->count();
		$this->assign('members_count',$members_count);
		//总评论数
		$coms_count=Db::name('comments')->count();
		$this->assign('coms_count',$coms_count);
		
		//日期时间戳
		list($start_t, $end_t) = Time::today();
		list($start_y, $end_y) = Time::yesterday();

		//今日发表文章数
		$toarticle_count=$article_model->whereTime('time', 'between', [$start_t, $end_t])->count();
		$this->assign('toarticle_count',$toarticle_count);
		//昨日文章数
		$ztarticle_count=$article_model->whereTime('time', 'between', [$start_y, $end_y])->count();
		$this->assign('ztarticle_count',$ztarticle_count);
		//提升比
		$difday=($ztarticle_count>0)?($toarticle_count-$ztarticle_count)/$ztarticle_count*100:0;
		$this->assign('difday',$difday);
		
		//今日增加会员
		$tomembers_count=$member_model->whereTime('member_addtime', 'between', [$start_t, $end_t])->count();
		$this->assign('tomembers_count',$tomembers_count);
		//昨日会员数
		$ztmembers_count=$member_model->whereTime('member_addtime', 'between', [$start_y, $end_y])->count();
		$this->assign('ztmembers_count',$ztmembers_count);
		//提升比
		$difday_m=($ztmembers_count>0)?($tomembers_count-$ztmembers_count)/$ztmembers_count*100:0;
		$this->assign('difday_m',$difday_m);
		
		//今日评论
		$tocoms_count=Db::name('comments')->whereTime('createtime', 'between', [$start_t, $end_t])->count();
		$this->assign('tocoms_count',$tocoms_count);
		//昨日评论
		$ztcoms_count=Db::name('comments')->whereTime('createtime', 'between', [$start_y, $end_y])->count();
		$this->assign('ztcoms_count',$ztcoms_count);
		//提升比
		$difday_c=($ztcoms_count>0)?($tocoms_count-$ztcoms_count)/$ztcoms_count*100:0;
		$this->assign('difday_c',$difday_c);
		//渲染模板
		return $this->fetch();
	}
// 结束
}