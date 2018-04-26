<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 手机APP端基架
// +----------------------------------------------------------------------
namespace app\mobile\controller;

use app\common\controller\Common;
use think\Db;

class Base extends Common
{
	protected function _initialize()
    {
        parent::_initialize();
        // 初始化,设置跨域请求权限
    	header('Access-Control-Allow-Origin:*');
		header("Access-Control-Allow-Headers:Origin,X-Requested-With,Content-Type,Accept");
		header('Access-Control-Allow-Methods:GET,POST,PUT');
	}

}