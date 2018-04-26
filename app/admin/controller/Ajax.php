<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | Ajax输出
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;

class Ajax
{
	/*
	 * 返回行政区域json字符串
	 */
	public function getRegion()
	{
		$map['pid']=input('pid');
		$map['type']=input('type');
		$list=Db::name("region")->where($map)->select();
		return json($list);
	}
	/*
	 * 返回产品分类json字符串
	 */
	public function getProduct_classify()
	{
		$map['pid']=input('pid');
		$map['type']=input('type');
		$list=Db::name("product_classify")->where($map)->select();
		return json($list);
	}
	/*
	 * 返回模块下控制器json字符串
	 */
	public function getController()
	{
		$module=input('request_module','admin');
		$list=\ReadClass::readDir(APP_PATH . $module. DS .'controller');
		return json($list);
	}
}