<?php

namespace aaphp;

/**
 * 生成url
 * Class Url
 * @package aaphp
 */
class Url
{
    /**
     * 生成url
     * @param string $path [url路径]
     * @param array $array [生成的url包含的get传参]
     * @param string $suffix [URL后缀]
     * @param string $domain [完整域名]
     * @return string [url]
     */
    public static function build($path, $array = [], $suffix = '', $domain = '')
    {
        $request = Request::instance();
        $mudule = $request->getMudule();
        $controller = $request->getController();
        $action = $request->getAction();

        if (!$path) {
            $path .= $action;
        }
//        只包含一个 / 表示 只有方法名
        if (0 == substr_count($path, '/')) {
            $path = $controller . '/' . $path;
        }

        $moduelStatus = Config::common('moduel_status');
        if ('cli' != PHP_SAPI && $moduelStatus) {//浏览器模型运行 并且开启分组模式
            if (1 == substr_count($path, '/')) {
                $path = $mudule . '/' . $path;
            }
        }

//        如果穿有参数则加在后面
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $path .= '/' . $key . '/' . $value;
            }
        }

        $scriptName = $_SERVER['SCRIPT_NAME'];
        if (isset($_SERVER['REQUEST_URI']) && false == strpos($_SERVER['REQUEST_URI'], 'index.php')) {
//            去掉index.php
            $scriptName = str_replace('/index.php', '', $scriptName);
        }

        $path = $scriptName . '/' . $path;
        if (true === $domain) {
            if (self::isHttps()) {
                $http = 'https://';
            } else {
                $http = 'http://';
            }
            $path = $http . $_SERVER['HTTP_HOST'] . $path;
        }
        $path = $path . $suffix;

        return $path;
    }

    /**
     * 判断是不是https
     * @return bool
     */
    public static function isHttps()
    {
        if (!isset($_SERVER['HTTPS'])) {
            return false;
        }
        if ($_SERVER['HTTPS'] === 1) {  //Apache
            return true;
        } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
            return true;
        } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
            return true;
        }
        return false;
    }
}
