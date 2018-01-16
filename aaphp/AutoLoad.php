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

    /**
     * 框架类缓存
     */
//    private static function importFrameClass()
//    {
//        // 需要载入的文件
//        $fileArray = array(
//            AA_PATH . '/Config.php',
//            AA_PATH . '/Controller.php',
//            AA_PATH . '/Database.php',
//            AA_PATH . '/Error.php',
//            AA_PATH . '/Log.php',
//            AA_PATH . '/Model.php',
//            AA_PATH . '/Request.php',
//            AA_PATH . '/Router.php',
//            AA_PATH . '/Url.php',
//            AA_PATH . '/Validate.php',
//            AA_PATH . '/View.php',
//        );
//        $str = '';
//        foreach ($fileArray as $key => $value) {
//            $classString = file_get_contents($value);
//            // 去掉文件中 <?php  ? > 开头和结尾;
//            $str .= preg_replace("/<\?php[^\/]+?\//i", "/", $classString);
//            $str .= PHP_EOL;
//            // 引入框架所需的文件
////            require_once $value;
//        }
//
//        // 把这些文件融合到一个，关闭DEBUG后，只载入这一个，加快速度
//        $str = '<?php' . PHP_EOL . 'namespace aaphp;' . PHP_EOL . $str;
//        $classCache = RUNTIME_PATH . '/ClassCache.php';
//        file_put_contents($classCache, $str) || die('access not allow :' . RUNTIME_PATH);
//        include_once $classCache;
//    }
}
