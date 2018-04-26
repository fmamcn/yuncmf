<?php
return array (
  'yuncmf_version' => 'v0.4.26',
  'addons_sql' => false,
  'app_debug' => true,
  'app_trace' => true,
  'url_route_mode' => '2',
  'url_route_on' => true,
  'url_route_must' => false,
  're_url' => 0,
  'route_complete_match' => false,
  'url_html_suffix' => 'html',
  'upload_path' => '/data/upload',
  'upload_validate' => 
  array (
    'size' => 10485760,
    'ext' => 
    array (
      0 => 'jpg',
      1 => 'gif',
      2 => 'png',
      3 => 'jpeg',
    ),
  ),
  'auth_config' => 
  array (
    'auth_on' => true,
    'auth_type' => 1,
    'auth_group' => 'auth_group',
    'auth_group_access' => 'auth_group_access',
    'auth_rule' => 'auth_rule',
    'auth_user' => 'admin',
  ),
  'log' => 
  array (
    'clear_on' => true,
    'timebf' => 2592000,
    'level' => 
    array (
    ),
  ),
  'workerman' => 
  array (
    'csport' => 
    array (
      'protocol' => 'websocket',
      'ip' => '0.0.0.0',
      'port' => '9000',
      'to' => 'websocket://0.0.0.0:9000',
    ),
    'adpush' => 
    array (
      'protocol' => 'text',
      'ip' => '0.0.0.0',
      'port' => '9001',
      'to' => 'text://0.0.0.0:9001',
    ),
    'webmsg' => 
    array (
      'protocol' => 'text',
      'ip' => '0.0.0.0',
      'port' => '9002',
      'to' => 'text://0.0.0.0:9002',
    ),
    'appmsg' => 
    array (
      'protocol' => 'text',
      'ip' => '0.0.0.0',
      'port' => '9003',
      'to' => 'text://0.0.0.0:9003',
    ),
    'register' => 
    array (
      'name' => 'YunCMFRegister',
      'protocol' => 'text',
      'ip' => '0.0.0.0',
      'port' => '1238',
      'to' => 'text://0.0.0.0:1238',
    ),
    'businessworker' => 
    array (
      'name' => 'CMSBusinessWorker',
      'process' => '4',
      'register' => '127.0.0.1:1238',
    ),
    'gateway' => 
    array (
      'name' => 'YunCMFRegister',
      'protocol' => 'tcp',
      'ip' => '0.0.0.0',
      'port' => '8282',
      'lanip' => '127.0.0.1',
      'process' => '4',
      'startport' => '2900',
      'to' => 'tcp://0.0.0.0:8282',
      'register' => '127.0.0.1:1238',
    ),
  ),
  'comment' => 
  array (
    't_open' => false,
    't_limit' => '15',
  ),
  'payment' => 
  array (
    'yunpay' => 
    array (
      'gateway' => '下单接口',
      'cpid' => '商户ID',
      'key' => '密钥',
      'display' => '1',
    ),
    'alipay' => 
    array (
      'gateway' => '支付宝网关',
      'appid' => 'APPID',
      'alipayrsaPublicKey' => '支付宝公钥',
      'rsaPrivateKey' => '开发者私钥',
      'notify_url' => '异步通知地址',
      'sign_type' => 'RSA2',
      'display' => '1',
    ),
    'aliwappay' => 
    array (
      'gateway' => '支付宝网关',
      'appid' => 'APPID',
      'alipayrsaPublicKey' => '支付宝公钥',
      'rsaPrivateKey' => '开发者私钥',
      'notify_url' => '异步通知地址',
      'sign_type' => 'RSA',
      'display' => '1',
    ),
    'weih5pay' => 
    array (
      'gateway' => '下单接口',
      'appid' => '公众账号ID',
      'mch_id' => '商户号',
      'key' => '密钥',
      'notify_url' => '异步通知地址',
      'sign_type' => 'MD5',
      'display' => '1',
    ),
    'wxqrcode' => 
    array (
      'gateway' => '下单接口',
      'appid' => '公众账号ID',
      'mch_id' => '商户号',
      'key' => '密钥',
      'notify_url' => '异步通知地址',
      'sign_type' => 'SHA256',
      'display' => '1',
    ),
    'wxjsapi' => 
    array (
      'gateway' => '下单接口',
      'appid' => '公众账号ID',
      'mch_id' => '商户号',
      'key' => '密钥',
      'notify_url' => '异步通知地址',
      'sign_type' => 'SHA256',
      'display' => '1',
    ),
    'qqpay' => 
    array (
      'gateway' => '下单接口',
      'appid' => 'APPID',
      'mch_id' => '商户号',
      'key' => '密钥',
      'notify_url' => '异步通知地址',
      'sign_type' => 'MD5',
      'display' => '1',
    ),
    'personal' => 
    array (
      'weiscan' => '/public/yuncmf/wxpay.png',
      'aliscan' => '/public/yuncmf/alipay.png',
      'display' => '1',
    ),
  ),
  'we_options' => 
  array (
    'we_name' => '',
    'we_id' => '',
    'we_number' => '',
    'app_id' => '',
    'secret' => '',
    'token' => '',
    'aes_key' => '',
    'we_type' => '',
  ),
  'think_sdk_weixin' => 
  array (
    'app_key' => '',
    'app_secret' => '',
    'display' => false,
    'callback' => '',
  ),
  'think_sdk_wechat' => 
  array (
    'app_key' => '',
    'app_secret' => '',
    'display' => false,
    'callback' => '',
  ),
  'think_sdk_qq' => 
  array (
    'app_key' => '',
    'app_secret' => '',
    'display' => true,
    'callback' => '',
  ),
  'think_sdk_sina' => 
  array (
    'app_key' => '',
    'app_secret' => '',
    'display' => true,
    'callback' => '',
  ),
  'think_sdk_sms' => 
  array (
    'AccessKeyId' => '',
    'accessKeySecret' => '',
    'sms_open' => '1',
    'sms_time_out' => '3600',
  ),
);