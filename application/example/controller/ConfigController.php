<?php

namespace application\example\controller;

use aaphp\Config;
use aaphp\Controller;

/**
 * 配置文件例子
 * Class ConfigController
 * @package application\example\controller
 */
class ConfigController extends Controller
{
    /**
     * 公共配置文件操作
     */
    public function common()
    {
        $defaultModuel = Config::common('default_moduel');
        echo '获取/config/common.conf的 default_moduel：' . $defaultModuel . '<br/>';

//        临时修改/config/common.conf的默认加载模块
        Config::common('default_moduel', 'home');
        $defaultModuel = Config::common('default_moduel');
        echo '获取临时修改的/config/common.conf的 default_moduel：' . $defaultModuel . '<br/>';

        $all = Config::common();
        echo '获取/config/common.conf的所有配置项：';
        var_dump($all);
    }

    /**
     * 数据库配置文件操作
     */
    public function database()
    {
        $defaultModuel = Config::database('hostname');
        echo '获取/config/database.conf的 hostname：' . $defaultModuel . '<br/>';

//        临时修改/config/database.conf的 hostname
        Config::database('hostname', '127.0.0.1');
        $defaultModuel = Config::database('hostname');
        echo '获取临时修改的/config/database.conf的 hostname：' . $defaultModuel . '<br/>';

        $all = Config::database();
        echo '获取/config/database.conf的所有配置项：';
        var_dump($all);
    }

    /**
     * 自定义配置文件操作
     */
    public function param()
    {
        $name = Config::param('name');
        echo '获取/config/param.conf的 name：' . $name . '<br/>';

//        临时修改/config/param.conf的 name
        Config::param('name', '李四');
        $name = Config::param('name');
        echo '获取临时修改的/config/param.conf的 name：' . $name . '<br/>';

        $all = Config::param();
        echo '获取/config/param.conf的所有配置项：';
        var_dump($all);
    }

    /**
     * 路由配置文件操作
     */
    public function router()
    {
        $router = Config::router('router');
        echo '获取/config/router.conf的 router：' . $router . '<br/>';

//        临时修改/config/router.conf的 aaphp
        Config::router('router', 'home');
        $router = Config::router('router');
        echo '获取临时修改的/config/router.conf的 router：' . $router . '<br/>';

        $all = Config::router();
        echo '获取/config/router.conf的所有配置项：';
        var_dump($all);
    }
}
