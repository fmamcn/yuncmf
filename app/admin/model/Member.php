<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 会员模型
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Model;

class Member extends Model
{
	public function news()
	{
		return $this->hasMany('News','news_auto');
	}
	/**
	 * 增加会员
	 * @return int 0或会员id
	 */
	public static function add($username,$salt='',$pwd,$groupid=1,$nickname='',$email='',$tel='',$open=0,$status=0)
	{
		$salt=$salt?:random(10);
		$sldata=array(
			'member_username'=>$username,
			'member_salt' => $salt,
			'member_pwd'=>encrypt_password($pwd,$salt),
			'member_groupid'=>$groupid,
			'member_nickname'=>$nickname,
			'member_email'=>$email,
			'member_tel'=>$tel,
			'member_open'=>$open,
			'last_login_ip'=>request()->ip(),
			'member_addtime'=>time(),
			'last_login_time'=>time(),
			'user_status'=>$status,
		);
		$member=self::create($sldata);
		if($member){
			return $member['member_id'];
		}else{
			return 0;
		}
	}
}
