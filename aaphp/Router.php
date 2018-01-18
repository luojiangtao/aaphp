<?php

namespace aaphp;

/**
 * 路由
 * Class Router
 * @package aaphp
 */
class Router
{
//    自身对象实例
    private static $instance;

//    根据路由规则转化后的 pathInfo
    private $pathInfo;

    /**
     * 构造方法
     */
    protected function __construct()
    {
        $pathInfo = self::parsePathInfo();
        $pathInfo = self::deleteSuffix($pathInfo);
        $this->pathInfo = self::transformation($pathInfo);
    }

    /**
     * 获取自身实例对象，单例模式
     * @return Router
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取根据路由规则转化后的 pathInfo
     * @return string
     */
    public function getPathInfo()
    {
        return $this->pathInfo;
    }

    /**
     * 去掉伪静态 .php .html .htm
     * @param string [$path_info [url里的pathInfo值]]
     * @return string [去掉后缀的url里的pathInfo值]
     */
    private function deleteSuffix($pathInfo)
    {
        if (strstr($pathInfo, '.')) {
            $array = explode('.', $pathInfo);
            $pathInfo = $array[0];
        }
        return $pathInfo;
    }

    /**
     * 解析url里的pathInfo值
     * @return string [解析后的url里的pathInfo值]
     */
    private function parsePathInfo()
    {
        $pathInfo = '';
        if (isset($_GET['r'])) {
//            支持 http://localhost/aaphp/index.php?r=example/ModelController/select
            $pathInfo = $_GET['r'];
        } elseif (IS_CLI) {//命令行模式也放在$_SERVER['PATH_INFO']里面
//            CLI模式下 index.php module/controller/action/params/...
            $pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        }

        if (!$pathInfo) {
            $pathInfo = empty($_SERVER['PATH_INFO']) ? '' : ltrim($_SERVER['PATH_INFO'], '/');
        }

        if (!$pathInfo) {
            $pathInfo = self::createPathInfo();
        }

        return $pathInfo;
    }

    /**
     * pathInfo 为空的情况下，通过REQUEST_URI和SCRIPT_NAME生成
     * @return string [通过REQUEST_URI和SCRIPT_NAME生成的 pathInfo]
     */
    private function createPathInfo()
    {
        if (!isset($_SERVER['REQUEST_URI']) || !isset($_SERVER['SCRIPT_NAME'])) {
            return '';
        }
//        去掉index.php
        $scriptName = str_replace('/index.php','',$_SERVER['SCRIPT_NAME']);
        $pathInfo = str_replace($scriptName,'',$_SERVER['REQUEST_URI']);
//        去掉 ?p=1 方式的get传参
        $pathInfo       = preg_replace("/\?.*/i", '', $pathInfo);
        $pathInfo = ltrim($pathInfo, '/');
        return $pathInfo;
    }

    /**
     * 读取用户路由配置，转化为真实的pathInfo
     * @param $pathInfo [url里的pathInfo值]
     * @return mixed|null|string|string[] []
     */
    private function transformation($pathInfo)
    {
        if (!$pathInfo) {
            return $pathInfo;
        }
        $pathInfoArray = explode('/', $pathInfo);

//        用户路由配置
        $router = Config::router();
        if (!$router || !is_array($router)) {
            return $pathInfo;
        }

        foreach ($router as $key => $value) {
//            查看配置项里面的key存在于$_SERVER['PATH_INFO']中
            if (!strstr($key, '/')) {// 自定义路由如果不包含 /
                if ($key == $pathInfoArray[0]) {
                    return $value;
                }
            } elseif (strstr($key, '/') && !strstr($key, '/^')) {// 自定义路由如果包含 / 但是不包含 /^ 则表示是普通路由 如： 'index/index'
                $tempArray = explode('/', $key);
                if (count($tempArray) <= count($pathInfoArray)) {
                    $flag = true;
                    foreach ($tempArray as $key2 => $value2) {
//                        用户自定义路由按 / 拆封后对比 都能匹配上才返回自定义路由
                        if ($value2 != $pathInfoArray[$key2]) {
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        return $value;
                    }

                }
            } elseif (strstr($key, '/^')) {//正则路由
                if (preg_match($key, $pathInfo)) {
//                    例子 ： '/^(\d+?)$/' =>'index/Article/article_detail/article_id/$1',
//                    文章详情，以数字为结尾 http://localhost/blog/index.php/17.html
                    $pathInfo = preg_replace($key, $value, $pathInfo);
                }

            }
        }
        return $pathInfo;
    }
}
