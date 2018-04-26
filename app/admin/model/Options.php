<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 配置模型
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Model;

class Options extends Model
{
    /*
     * 前台模板文件数组
     */
	public static function tpls()
	{
		$tpls=cache('tpls_');
		if(empty($tpls)){
            $sys=self::get_options('site_options');
			$arr=list_file(APP_PATH.'home/view/'.$sys['site_tpl'],'*.html');
			$tpls=array();
			foreach($arr as $v){
				$tpls[]=basename($v['filename'],'.html');
			}
			cache('tpls_',$tpls);
		}
		return $tpls;
	}
    /*
     * 前台themes
     */
    public static function themes()
    {
        $themes=cache('themes');
        if(empty($themes)){
            $arr=list_file(APP_PATH.'home/view/');
            foreach($arr as $v){
                if($v['isDir'] && strtolower($v['filename']!='public')){
                    $themes[]=$v['filename'];
                }
            }
            cache('themes',$themes);
        }
        return $themes;
    }
    /*
     * 获取系统基本设置
     */
    public static function get_options($type='site_options')
    {
        $options = cache($type.'_');
        if(empty($options)){
            switch ($type){
                case 'email_options':
                    $sys=array(
                        'email_open'=>0,
                        'email_rename'=>'',
                        'email_name'=>'',
                        'email_smtpname'=>'',
                        'smtpsecure'=>'',
                        'smtp_port'=>'',
                        'email_emname'=>'',
                        'email_pwd'=>'',
                    );
                    break;
                case 'active_options':
                    $sys=array(
                        'email_active'=>0,
                        'email_title'=>'',
                        'email_tpl'=>'',
                    );
                    break;
                default:
                    $sys=array(
                        'site_name'=>'',
                        'site_host'=>'',
                        'site_tpl'=>'',
                        'site_tpl_m'=>'',
                        'site_logo'=>'',
                        'site_icp'=>'',
                        'site_tongji'=>'',
                        'site_copyright'=>'',
                        'site_co_name'=>'',
                        'site_address'=>'',
                        'site_tel'=>'',
                        'site_fax'=>'',
                        'site_admin_email'=>'',
                        'site_qq'=>'',
                        'site_seo_title'=>'',
                        'site_seo_keywords'=>'',
                        'site_seo_description'=>'',
                    );
            }
            $options=self::where(array('option_name'=>$type))->find()->toArray();
            if(empty($options)){
                $options=self::where(array('option_name'=>$type))->find()->toArray();
                unset($options['option_id']);
                self::create($options);
            }
            $options=json_decode($options['option_value'],true);
            $options=array_merge($sys,$options);
            cache($type.'_',$options);
        }
        return $options;
    }
    /*
     * 设置系统基本设置
     */
    public static function set_options($options=[],$type='site_options')
    {
        return self::where(['option_name'=>$type])->setField('option_value',json_encode($options));
    }
}
