<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 安全检测
// +----------------------------------------------------------------------
namespace addons\security;

use think\Addons;

class Security extends Addons
{
    public $info = [
        'name' => 'Security',
        'title' => '安全检测',
        'description' => '网站安全检测',
        'status' => 0,
        'author' => '翟翟',
        'version' => '0.1',
        'admin'=>'1'
    ];
    /**
     * @var array 插件管理方法,格式:['控制器/操作方法',[参数数组]])
     */
    public $admin_actions = [
        'index'=>['Admin/security_list'],//管理首页
        'config'=>[],//设置页
        'edit' => [],//编辑页
        'add'=>[],//增加页
    ];
    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }
}
