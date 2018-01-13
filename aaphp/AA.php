<?php

namespace aaphp;

require_once("Application.php");
//自动载入vendor下的类
require_once(dirname(dirname(__FILE__)) . "/vendor/autoload.php");

/**
 * 执行框架层的初始化
 * Class AA
 * @package aaphp
 */
final class AA
{
    /**
     * 入口方法，初始化基本信息
     */
    public static function start()
    {
        // 定义常量，用于创建用户目录和找到响应的类和函数
        self::setConst();
        // 默认关闭调试模式
//        defined('DEBUG') || define('DEBUG', false);
//        if (DEBUG) {
//            // 检查项目相关目录是否存在，不存在则自动生成
//            // 载入框架所需文件，一些基本的类和函数
//            self::_import_file();
//        } else {
//            // 关闭错误
//            error_reporting(0);
//            // 加载融合为一个的框架所需的文件，速度更快
//            require TEMP_PATH . '/~boot.php';
//        }

        // 载入Application类后，就执行入口方法，继续设置基本信息
        Application::start();
    }

    /**
     * 定义常量，用于创建用户目录和找到响应的类和函数
     */
    private static function setConst()
    {
        // 这个类的路径，为了兼容而替换路径表示方式
        $path = str_replace('\\', '/', __FILE__);
        // 框架根目录
        defined('AA_PATH') || define('AA_PATH', dirname($path));
        // 框架数据文件目录
        define('TEMPLATE_PATH', AA_PATH . '/template');

        // 网站根目录
        define('ROOT_PATH', dirname(AA_PATH));

        // 应用目录
        define('APPLICATION_PATH', ROOT_PATH . '/application');
        // 临时缓存目录
        define('RUNTIME_PATH', ROOT_PATH . '/runtime');
        // 日志目录
        define('LOG_PATH', RUNTIME_PATH . '/log');
        // 编译文件文件缓存目录
        define('COMPILE_PATH', RUNTIME_PATH . '/compile');
        // 纯html文件缓存目录
        define('CACHE_PATH', RUNTIME_PATH . '/cache');

        //用户配置路径
        define('USER_CONFIG_PATH', ROOT_PATH . '/config');

        // 系统变量，是否是POST提交
        $requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        define('IS_POST', ($requestMethod == 'POST' ? true : false));
//        是否是命令行模型运行
        define('IS_CLI', ('cli' == PHP_SAPI ? true : false));
    }

    /**
     * 载入框架所需文件，一些基本的类和函数
     * @Author   罗江涛
     * @DateTime 2016-08-02T09:57:58+0800
     */
//    private static function _import_file()
//    {
//        // 需要载入的文件
//        $fileArray = array(
//            FUNCTION_PATH . '/Function.php',
//            CORE_PATH . '/Router.php',
//            CORE_PATH . '/application.php',
//            ORG_PATH . '/View.php
//            CORE_PATH . '/controller.php',
//            CORE_PATH . '/Log.php',
//        );
//        $str = '';
//        foreach ($fileArray as $key => $value) {
//            // 去掉文件中 <?php  ? > 开头和结尾;
//            $str .= substr(file_get_contents($value), 5);
//            // 引入框架所需的文件
//            require_once $value;
//        }
//
//        // 把这些文件融合到一个，关闭DEBUG后，只载入这一个，加快速度
    /*        $str = "<?php\r\n" . $str . "\r\n?>";*/
//        file_put_contents(TEMP_PATH . '/~boot.php', $str) || die('access not allow :' . TEMP_PATH);
//    }
}

