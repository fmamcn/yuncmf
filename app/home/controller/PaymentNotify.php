<?php
// +----------------------------------------------------------------------
// | oneMall [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------

/**
 * 客户端需要继承该接口，并实现这个方法，在其中实现对应的业务逻辑
 * Class TestNotify
 * anthor helei
 */
namespace app\home\controller;

class PaymentNotify implements \Payment\Notify\PayNotifyInterface
{

    public function notifyProcess(array $data)
    {
        // 执行业务逻辑，成功后返回true
        //var_dump($data);
        return true;
    }
}