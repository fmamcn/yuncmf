<?php
// +----------------------------------------------------------------------
// | oneMall [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
namespace app\home\controller;

class Error extends Base
{
    //空控制器
    public function index()
    {
        $this->error('operation not valid');
    }
}