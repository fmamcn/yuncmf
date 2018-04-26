<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 共用
// +----------------------------------------------------------------------
namespace app\common\controller;

use think\Controller;
use think\captcha\Captcha;

class Common extends Controller
{
    // Request实例
	protected function _initialize()
    {
		parent::_initialize();
        if (!defined('__ROOT__')) {
            $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
            define('__ROOT__', (('/' == $_root || '\\' == $_root) ? '' : $_root));
        }
		if (!file_exists(ROOT_PATH.'data/install.lock')) {
            //不存在，则进入安装
            header('Location: ' . url('install/Index/index'));
            exit();
        }
        if (!defined('MODULE_NAME')){define('MODULE_NAME', $this->request->module());}
        if (!defined('CONTROLLER_NAME')){define('CONTROLLER_NAME', $this->request->controller());}
        if (!defined('ACTION_NAME')){define('ACTION_NAME', $this->request->action());}
	}
    //空操作
    public function _empty()
    {
        $this->error('无效操作！');
    }
	protected function verify_build($id)
	{
		ob_end_clean();
		$verify = new Captcha (config('verify'));
		return $verify->entry($id);
	}
	protected function verify_check($id)
	{
		$verify =new Captcha ();
		if (!$verify->check(input('verify'), $id)) {
			$this->error('验证码错误',url(MODULE_NAME.'/Login/login'));
		}
	}
    protected function check_admin_login(){
		return model('admin/Admin')->is_login();
    }
}