<?php

namespace aaphp;

/**
 * 自动加载。例子：用户只需 use aaphp\Config; 就会自动 require_once 'aaphp\Config';
 * Class autoLoad
 * @package aaphp
 */
class AutoLoad
{
    /**
     * 注册自动加载
     */
    public static function autoRegister()
    {
        spl_autoload_register(array(__CLASS__, '_autoLoad'));
    }

    /**
     * 自动加载
     * @param $className [类名，包含命名空间]
     */
    private static function _autoLoad($className)
    {
//        类的绝对路径
        $fullPath = ROOT_PATH . '/' . $className . '.php';
        $fullPath = str_replace('\\', '/', $fullPath);
        if (file_exists($fullPath)) {
            include_once($fullPath);
        } else {
            if (DEBUG) {
                Error::halt('文件：' . $fullPath . '不存在');
            } else {
                echo '网站出了点小错，需要查看详情，请设置 index.php 中 define("DEBUG", true); ';
            }
        }
    }
}