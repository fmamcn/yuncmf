<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 产品模型
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Model;

class Product extends Model
{
	public function user()
	{
		return $this->belongsTo('Member','member_id');
	}
	public function menu()
	{
		return $this->belongsTo('Menu','id');
	}
}
