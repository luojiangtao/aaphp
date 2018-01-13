<?php

namespace application\example\controller;

use aaphp\Config;
use aaphp\Controller;
use aaphp\Request;

/**
 * 路由例子
 * 配置文件位置：/config/router.php
 * Class RouterController
 * @package application\example\controller
 */
class RouterController extends Controller
{
    /**
     * 简单路由
     * 路由地址：http://localhost/aaphp/index.php/router
     * 实际地址：http://localhost/aaphp/index.php/example/RouterController/index
     */
    public function index()
    {
        echo '经过路由处理后的页面，路由配置：' . Config::router('router').'<br/>';
    }

    /**
     * 正则路由
     * 路由地址：http://localhost/aaphp/index.php/get/8
     * 实际地址：http://localhost/aaphp/index.php/example/RouterController/get/id/8
     */
    public function get()
    {
        echo '经过正则路由处理后的页面，路由配置：' . Config::router('/^get\/(\d+?)$/').'<br/>';
        $id = Request::instance()->request('id');
        echo '路由映射传入的$_GET参数id:' . $id;
    }
}
