<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 安装控制器
// +----------------------------------------------------------------------

/*
*	测试是否可写
*/
function testwrite($d)
{
    $tfile = "_test.txt";
    $fp = @fopen($d . "/" . $tfile, "w");
    if (!$fp) {
        return false;
    }
    fclose($fp);
    $rs = @unlink($d . "/" . $tfile);
    if ($rs) {
        return true;
    }
    return false;
}
/*
*	建立文件夹
*/
function create_dir($path)
{
    if (is_dir($path))
        return true;
    $path = dir_path($path);
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir))
            continue;
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}
/*
*	返回路径
*/
function dir_path($path)
{
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}
/*
*	执行sql文件
*/
function execute_sql($db,$file,$tablepre)
{
    //读取SQL文件
    $sql = file_get_contents(APP_PATH. request()->module().'/data/'.$file);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);
    //替换表前缀
    $default_tablepre = "yc_";
    $sql = str_replace(" `{$default_tablepre}", " `{$tablepre}", $sql);
    //开始安装
    showmsg('开始安装数据库...');
    foreach ($sql as $item) {
        $item = trim($item);
        if(empty($item)) continue;
        preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
        if($matches) {
            $table_name = $matches[1];
            $msg  = "创建数据表{$table_name}";
            if(false !== $db->exec($item)){
                showmsg($msg . ' 完成');
            } else {
                session('error', true);
                showmsg($msg . ' 失败！', 'error');
            }
        } else {
            $db->exec($item);
        }
    
    }
}
/*
*	更新系统设置
*/
function update_site_configs($db,$table_prefix)
{
    $sitename=input("sitename");
    $email=input("manager_email");
    $siteurl=input("siteurl");
    $seo_keywords=input("sitekeywords");
    $seo_description=input("siteinfo");
    $site_options=<<<helllo
        {
            "site_name":"$sitename",
            "site_host":"$siteurl",
            "site_tpl":"default",
            "site_tpl_m":"mobile",
            "site_icp":"",
            "site_tongji":"",
            "site_copyright":"",
            "site_co_name":"",
            "site_address":"",
            "site_tel":"+86 755 8888 8888",
            "site_fax":"+86 755 8888 8888",
            "site_admin_email":"$email",
            "site_qq":"4000000",
            "site_seo_title":"$sitename",
            "site_seo_keywords":"$seo_keywords",
            "site_seo_description":"$seo_description",
            "site_logo":"\\/app\\/home\\/view\\/default\\/images\\/logo.png"
        }
helllo;
    $sql="INSERT INTO `{$table_prefix}options` (option_value,option_name) VALUES ('$site_options','site_options')";
    $db->exec($sql);
    showmsg("网站信息配置成功!");
}
/*
*	创建管理员
*/
function create_admin_account($db,$table_prefix)
{
    $username=input("manager");
	$admin_pwd_salt=random(10);
    $password=encrypt_password(input("manager_pwd"),$admin_pwd_salt);
    $email=input("manager_email");
    $create_date=time();
    $ip=request()->ip();
    $sql =<<<hello
    INSERT INTO `{$table_prefix}admin` 
    (admin_id, admin_username, admin_pwd, admin_pwd_salt,admin_changepwd, admin_email, admin_realname, admin_tel, admin_hits, admin_ip, admin_addtime, admin_mdemail, admin_open,member_id) VALUES
    ('1', '{$username}', '{$password}','{$admin_pwd_salt}','{$create_date}','{$email}', '','',1,'{$ip}', {$create_date}, '', 1,1);
	INSERT INTO `{$table_prefix}member` 
    (member_id, member_username, member_pwd, member_salt,member_groupid, member_nickname, member_email, member_open, member_addtime, last_login_ip, last_login_time, user_status) VALUES
    ('1', '{$username}', '{$password}','{$admin_pwd_salt}',1,'{$username}','{$email}',1,'{$create_date}','{$ip}','{$create_date}',1);
hello;
    $db->exec($sql);
    showmsg("管理员账号创建成功!");
}

/*
*	写入配置
*/
function create_config($config)
{
    if(is_array($config)){
        //读取配置内容
        $conf = file_get_contents(APP_PATH. request()->module(). '/data/database.php');
        //替换配置项
        foreach ($config as $key => $value) {
            $conf = str_replace("#{$key}#", $value, $conf);
        }
        //写入应用配置文件
        if(file_put_contents(APP_PATH. 'database.php', $conf)){
            showmsg('配置文件写入成功');
        } else {
            session('error', true);
            showmsg('配置文件写入失败！', 'error');
        }
        return '';
    }
}