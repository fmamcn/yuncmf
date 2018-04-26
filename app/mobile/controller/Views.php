<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 手机APP端内容查看控制器
// +----------------------------------------------------------------------
namespace app\mobile\controller;

use think\Db;

class Views extends Base
{
    //文章内页
    public function article()
    {
		$article=Db::name('article')->alias("a")->join(config('database.prefix').'member_list b','a.auto =b.member_list_id')->join(config('database.prefix').'menu c','a.columnid =c.id')->where(array('n_id'=>input('id'),'open'=>1,'back'=>0))->find();
		$menu=Db::name('menu')->find($article['columnid']);
		$can_do=check_user_action('article'.input('id'),0,false,60);
		if($can_do){
			//更新点击数
			Db::name('article')->update(array("n_id"=>input('id'),"hits"=>array("exp",Db::raw('hits+1'))));
			$article['hits']+=1;
		}
		ajaxReturn($article);
    }
    //文章收藏
    public function articlefavorite()
    {
		$id=input('id');
		$uid=input('uid');
		$favorites_model=Db::name("favorites");
		$find_favorite=$favorites_model->where(array('t_name'=>'article','t_id'=>$id,'uid'=>$uid))->find();
		if($find_favorite){
			ajaxReturn(array('status'=>0,'msg'=>'已收藏'));
		}else{
            $data=array(
                'uid'=>$uid,
                't_name'=>'article',
                't_id'=>$id,
                'createtime'=>time(),
            );
			$result=$favorites_model->insert($data);
			if($result){
				ajaxReturn(array('status'=>1,'msg'=>'收藏成功'));
			}else {
				ajaxReturn(array('status'=>-1,'msg'=>'收藏失败'));
			}
		}
	}

//结束 
}
