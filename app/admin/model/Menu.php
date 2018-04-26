<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 前台菜单模型
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Model;

class Menu extends Model
{
	public function news()
	{
		return $this->hasMany('Article','columnid')->bind('menu_name');
	}
}
