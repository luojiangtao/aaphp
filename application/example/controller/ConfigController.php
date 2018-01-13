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
     * 访问地址：http://localhost/aaphp/index.php/example/ConfigController/common
     */
    public function common()
    {
        $defaultModuel = Config::common('default_moduel');
        echo '获取config/common.conf的 default_moduel：' . $defaultModuel . '<br/>';

//        临时修改config/common.conf的默认加载模块
        Config::common('default_moduel', 'home');
        $defaultModuel = Config::common('default_moduel');
        echo '获取临时修改的config/common.conf的 default_moduel：' . $defaultModuel . '<br/>';

        $all = Config::common();
        echo '获取config/common.conf的所有配置项：';
        var_dump($all);
    }

    /**
     * 数据库配置文件操作
     * 访问地址：http://localhost/aaphp/index.php/example/ConfigController/database
     */
    public function database()
    {
        $defaultModuel = Config::database('DB_HOST');
        echo '获取config/database.conf的 DB_HOST：' . $defaultModuel . '<br/>';

//        临时修改config/database.conf的 DB_HOST
        Config::database('DB_HOST', '127.0.0.1');
        $defaultModuel = Config::database('DB_HOST');
        echo '获取临时修改的config/database.conf的 DB_HOST：' . $defaultModuel . '<br/>';

        $all = Config::database();
        echo '获取config/database.conf的所有配置项：';
        var_dump($all);
    }

    /**
     * 自定义配置文件操作
     * 访问地址：http://localhost/aaphp/index.php/example/ConfigController/param
     */
    public function param()
    {
        $name = Config::param('name');
        echo '获取config/param.conf的 name：' . $name . '<br/>';

//        临时修改config/param.conf的 name
        Config::param('name', '李四');
        $name = Config::param('name');
        echo '获取临时修改的config/param.conf的 name：' . $name . '<br/>';

        $all = Config::param();
        echo '获取config/param.conf的所有配置项：';
        var_dump($all);
    }

    /**
     * 路由配置文件操作
     * 访问地址：http://localhost/aaphp/index.php/example/ConfigController/router
     */
    public function router()
    {
        $router = Config::router('router');
        echo '获取config/router.conf的 router：' . $router . '<br/>';

//        临时修改config/router.conf的 aaphp
        Config::router('router', 'home');
        $router = Config::router('router');
        echo '获取临时修改的config/router.conf的 router：' . $router . '<br/>';

        $all = Config::router();
        echo '获取config/router.conf的所有配置项：';
        var_dump($all);
    }

}
