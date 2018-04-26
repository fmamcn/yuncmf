<?php 
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \GatewayWorker\Register;
//加载本地配置文件
$file = __DIR__ . '/../../data/conf/config.php';
$cfg = array();
if (file_exists($file)) {
    $cfg = (include $file);
}
$workerman=isset($cfg['workerman']) ? $cfg['workerman'] : null;
// worker名称
$register_name=$workerman['register']['name'];
// 协议地址
$register_ip=$workerman['register']['to'];


// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';

// register 必须是text协议
$register = new Register($register_ip);
$register->name = $register_name;

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

