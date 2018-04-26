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
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;
//加载本地配置文件
$file = __DIR__ . '/../../data/conf/config.php';
$cfg = array();
if (file_exists($file)) {
    $cfg = (include $file);
}
$workerman=isset($cfg['workerman']) ? $cfg['workerman'] : null;
// worker名称
$bussiness_name=$workerman['businessworker']['name'];
// bussinessWorker进程数量
$bussiness_process=$workerman['businessworker']['process'];
// 服务注册地址
$bussiness_registerAddress=$workerman['businessworker']['register'];


// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';

// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = $bussiness_name;
// bussinessWorker进程数量
$worker->count = $bussiness_process;
// 服务注册地址
$worker->registerAddress = $bussiness_registerAddress;

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

