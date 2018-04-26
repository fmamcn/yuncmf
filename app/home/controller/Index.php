<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 前端主页控制器
// +----------------------------------------------------------------------
namespace app\home\controller;

use think\Cache;
use think\Db;
use think\captcha\Captcha;
use app\admin\model\Options;

class Index extends Base
{
	public function index()
    {
        //获取地址参数
        $inputstr=input();
        $input=relatearray2string('&',$inputstr);
        $sys=Options::get_options('site_options');
        $host_url=$sys['site_host'];
        // 跳转URL
    	if (config('re_url')) {
    		$reurl=Db::name("reurl")->where('status',1)->select();
    		for ($i=0;$i<count($reurl); $i++){
    			$validate_url=curl_get_https($reurl[$i]['validate_url']);
    			if ($validate_url==='1') {
    				$url=$reurl[$i]['goto_url'];
                    $wait_time=$reurl[$i]['wait_time'];
		    		$gotourl=$url.'cpi?'.$input;
                    $print_text='<div style="width:100%;margin-top:35vh;text-align:center;">正在加载，请稍等...<br>'.$wait_time.'秒后自动跳转到新网址~~~</div>';
		    		header("refresh:$wait_time;url=$gotourl");
		    		print($print_text);
                    break;
    			}else{
    				echo '<div style="width:100%;text-align:center;margin-top:35vh;">网站出错啦，请明天再来访问吧</div>';
    			}
    		}
    	}else{
    		$linktype=Db::name("plug_linktype")->order("plug_linktype_order asc")->select();
			$this->assign('linktype',$linktype);
    		return $this->view->fetch(':index');
    	}
	}
	// 网站跳转验证地址http://www.xxx.com/home/Index/reurl
	public function reurl()
    {
		return 1;
	}
	public function downapp()
    {
		return $this->view->fetch(':downapp');
	}
	public function map()
    {
		$menu=Db::name('menu')->where('parentid = 0')->where('id > 1')->select();
		$this->assign('menu',$menu);
		$linktype=Db::name("plug_linktype")->order("plug_linktype_order asc")->select();
		$this->assign('linktype',$linktype);
		return $this->view->fetch(':map');
	}
//结束
}