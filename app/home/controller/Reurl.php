<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 跳转控制器
// | 自动检测服务器池是否可用
// +----------------------------------------------------------------------
namespace app\home\controller;

use think\Cache;
use think\Db;
use think\captcha\Captcha;

class Reurl extends Base
{
    // 可把这个替换home/Index控制器的index方法
	public function index()
    {
    	if (config('re_url')) {
    		$inputstr=input();
    		$input=relatearray2string('&',$inputstr);
    		$reurl=Db::name("reurl")->where('status',1)->select();
    		for ($i=0;$i<count($reurl); $i++){
    			$validate_url=curl_get_https($reurl[$i]['validate_url']);
    			if ($validate_url==='1') {
    				$url=$reurl[$i]['goto_url'];
		    		$gotourl=$url.'?'.$input;
		    		//设置refresh秒后自动跳转
		    		header("refresh:0;url=$gotourl");
		    		print('<div style="width:100%;margin-top:35vh;text-align:center;">正在加载，请稍等...<br>3秒后自动跳转到新网址~~~</div>');
    			}else{
    				echo '<div style="width:100%;text-align:center;margin-top:35vh;">网站出错啦，请明天再来访问吧</div>';
    			}
    		}
    	}else{
    		print('<div style="width:100%;text-align:center;margin-top:35vh;">网站出错啦，请明天再来访问吧</div>');
    	}
	}
    // 网站跳转验证地址http://www.xxx.com/home/Reurl/reurl
    public function reurl()
    {
        return 1;
    }


}