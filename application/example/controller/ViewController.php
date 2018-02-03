<?php

namespace application\example\controller;

use aaphp\Controller;
use aaphp\Config;

/**
 * 视图例子
 * Class ViewController
 * @package application\example\controller
 */
class ViewController extends Controller
{
    /**
     * 获取视图
     * 视图文件位置: /application/example/view/ViewController/fetchExample.html
     * @return string
     */
    public function fetchExample()
    {
        return $this->fetch();
    }

    /**
     * 获取指定的视图
     * 视图文件位置: /application/example/view/ViewController/index.html
     * @return string
     */
    public function fetchDesignated()
    {
//        获取ViewController控制器下index.html视图
//        return $this->fetch('ViewController/index');
//        获取当前控制器下index.html视图
        return $this->fetch('index');
    }

    /**
     * 模版中使用函数
     * 视图文件位置: /application/example/view/ViewController/fetchExample.html
     * @return string
     */
    public function functionExample()
    {
        $age = 18.88;
        $this->assign('age', $age);
        return $this->fetch();
    }

    /**
     * 分配参数到视图
     * 视图文件位置: /application/example/view/ViewController/assignExample.html
     * @return string
     */
    public function assignExample()
    {
        $name = 'aaphp';
        $this->assign('name', $name);
        return $this->fetch();
    }

    /**
     * 引入头部和底部
     * 视图文件位置: /application/example/view/ViewController/includeExample.html
     * @return string
     */
    public function includeExample()
    {
        return $this->fetch();
    }

    /**
     * 模版标签例子
     * 视图文件位置: /application/example/view/ViewController/foreachExample.html
     * @return string
     */
    public function foreachExample()
    {
        $user = [
            [
                'name' => 'lily',
                'age' => 18,
            ],
            [
                'name' => 'tom',
                'age' => 19,
            ],
        ];
        $this->assign('user', $user);
        return $this->fetch();
    }

    /**
     * if判断
     * 视图文件位置: /application/example/view/ViewController/ifExample.html
     * @return string
     */
    public function ifExample()
    {
        return $this->fetch();
    }

    /**
     * 输出json
     * @return array [被json_encode后的json字符串]
     */
    public function jsonExample()
    {
        Config::common('HEADER', 'Content-type:application/json;charset=utf-8');
        $data = ['name' => 'aaphp', 'email' => '1368761119@qq.com'];
        return $data;
    }

    /**
     * xss 过滤
     * @return string
     */
    public function xss()
    {
        // 开启 xss 过滤，默认开启
        Config::common('xss_filter',true);
        $xss = "<script>alert('XSS')</script>";
        $this->assign('xss', $xss);
        return $this->fetch();
    }

    /**
     * 关闭 xss 过滤
     * @return string
     */
    public function closeXssFilter()
    {
//        关闭xss过滤， 也可以修改 /config/common.php 'xss_filter' => false,
        Config::common('xss_filter',false);
        $xss = "<script>alert('XSS')</script>";
        $this->assign('xss', $xss);
        return $this->fetch();
    }
}
