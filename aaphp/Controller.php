<?php

namespace aaphp;

/**
 * 父类，用户的控制器都需要继承
 * Class Controller
 * @package aaphp
 */
class Controller
{
    /**
     * 载入前端模版
     * @param string $file [模版文件相对路径]
     * @return string [html字符串]
     */
    protected function fetch($file = null)
    {
        return View::fetch($file);
    }

    /**
     * 分配变量到前台模版
     * @param $key [变量名称]
     * @param $value [变量的值]
     */
    protected function assign($key, $value)
    {
        View::assign($key, $value);
    }

    /**
     * 跳转方法
     * @param    string $url [跳转的地址]
     * @param    integer $time [等待时间]
     * @param    string $msg [提示信息]
     */
    protected function redirect($url, $time = 0, $msg = '')
    {
        if (!headers_sent()) {
            // 用header方式跳转
            if ($time) {
                header('refresh:{$time};url={$url}');
            } else {
                header('location:' . $url);
            }
        } else {
            // 用meta方式跳转
            echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
            if ($time) {
                die($msg);
            }
        }
    }
}
