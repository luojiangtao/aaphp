<?php

namespace aaphp;

use aaphp\console\Console;

require_once("AutoLoad.php");
require_once("Error.php");

/**
 * 应用控制器，执行和用户相关的方法
 * Class Application
 * @package aaphp
 */
final class Application
{
    /**
     * 入口方法，被加载后就执行
     */
    public static function start()
    {
//        错误注册
        Error::register();
//        自动载入注册
        AutoLoad::autoRegister();
//        加载框架配置项，开启session，设置时区
        self::_init();
//        // 设置外部路径，程序员使用这些常量找到相应的路径
        self::setUrl();
//        // 实例化类，并且执行类里面的方法
//        self::applicationRun();

        if (IS_CLI) {//命令行模型运行
            $console=new Console();
            $console->start();
            $isControllerRun=$console->isControllerRun();
            if($isControllerRun){//运行console下面的controller
                self::applicationRun();
            }
        }else{//不是命令行模型运行
            self::applicationRun();
        }
    }

    /**
     * 运行用户自定义controller，并且执行类里面的方法
     */
    private static function applicationRun()
    {
//        404
        $notFound = Config::common('default_not_found');

//        接收控制器名称，默认Index
        $request = Request::instance();
//        模块
        $mudule = $request->getMudule();
//        控制器
        $controller = $request->getController();
//        接收方法名称，默认index
        $action = $request->getAction();

        if (IS_CLI) {//命令行模型运行
            $controllerPath = 'console/controller/' . $controller . '.php';
            $controller = 'console\controller\\' . $controller;
        } else {//浏览器模型运行
            $moduelStatus = Config::common('moduel_status');
            if ($moduelStatus) {//开启分组模式
                $controllerPath = APPLICATION_PATH . '/' . $mudule . '/controller/' . $controller . '.php';
                $controller = 'application\\' . $mudule . '\controller\\' . $controller;
            } else {//关闭分组模式
                $controllerPath = APPLICATION_PATH . '/controller/' . $controller . '.php';
                $controller = 'application\controller\\' . $controller;
            }
        }

        if (file_exists($controllerPath) && class_exists($controller)) {//控制器存在
//            反射类
            $reflectionClass = new \ReflectionClass($controller);
            if ($reflectionClass->hasMethod($action)) {//方法存在
//                通过反射实例化用户控制器
                $class = $reflectionClass->newInstance();
//                执行方法
                $result = $class->$action();
//                输出结果
                self::outPut($result);
            } else {//方法不存在
                if ($notFound != '') {
                    self::notFound();
                    return;
                } else {
                    Error::halt('方法：' . $controller . '->' . $action . '不存在');
                }
            }
        } else {//控制器不存在
            if (!empty($notFound)) {
                self::notFound();
                return;
            } else {
                Error::halt('控制器' . $controller . '不存在');
            }
        }
    }

    /**
     * 执行用户配置的404页面
     */
    private static function notFound()
    {
//        默认分组
        $defaultModuel = Config::common('default_moduel');
//        默认控制器
        $defaultController = Config::common('default_controller');
//        404
        $notFound = Config::common('default_not_found');
//        分组状态
        $moduelStatus = Config::common('moduel_status');

        $request = Request::instance();
        $request->setModule($defaultModuel);
        $request->setController($defaultController);
        $request->setAction($notFound);

        if (IS_CLI) {//命令行模型运行
            $defaultController = 'console\controller\\' . $defaultController;
        } else {//浏览器模型运行
            if ($moduelStatus) {//分组模式开启
                $defaultController = 'application\\' . $defaultModuel . '\controller\\' . $defaultController;
            } else {//分组模式关闭
                $defaultController = 'application\controller\\' . $defaultController;
            }
        }

        if (class_exists($defaultController)) {//默认控制器存在
//            反射类
            $reflectionClass = new \ReflectionClass($defaultController);
            if ($reflectionClass->hasMethod($notFound)) {
//                通过反射实例化用户控制器
                $class = $reflectionClass->newInstance();
                $request->setAction($notFound);
//                执行方法
                $result = $class->$notFound();
//                输出结果
                self::outPut($result);
            } else {
                Error::halt('默认控制器的default_not_found：' . $defaultController . '->' . $notFound . '不存在');
            }
        } else {//默认控制器不存在
            Error::halt('默认控制器：' . $defaultController . '不存在');
        }
    }

    /**
     * 控制器输出
     * @param $result [用户自定义控制器输出的结果]
     */
    private static function outPut($result)
    {
        if (!$result) {
            return;
        }
        if (is_array($result)) {
            $result = json_encode($result);
        }
        echo $result;
    }

    /**
     * 设置外部路径，程序员使用这些常量找到相应的路径，可以在模版文件中使用
     */
    private static function setUrl()
    {
        $httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        // 获取网络路径
        $path = 'http://' . $httpHost . $_SERVER['SCRIPT_NAME'];
        // 为了保证linux和windows都兼容，这里进行替换
        $path = str_replace('\\', '/', $path);
        // 网站根目录
        define('__ROOT__', dirname($path));
    }

    /**
     * 加载框架配置项，开启session，设置时区
     */
    private static function _init()
    {
        $sessionAutoStart = Config::common('session_auto_start');
        $defaultTimeZone = Config::common('default_time_zone');
//        var_dump($defaultTimeZone);die;
        // 设置时区
        date_default_timezone_set($defaultTimeZone);
        // 是否开启session
        $sessionAutoStart && session_start();
    }
}
