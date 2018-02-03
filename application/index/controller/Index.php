<?php

namespace application\index\controller;

use aaphp\Controller;
use aaphp\Model;
use aaphp\Request;

/**
 * 首页，控制器
 * 访问该文件夹下面的控制器，需要设置 config/common.php
 * 'moduel_status'         => true,//开启分组状态
 * Class Index
 * @package application\index\controller
 */
class Index extends Controller
{
    /**
     * 首页
     */
    public function index()
    {
        // 载入模版
        return $this->fetch();
    }

    public function test()
    {
        echo 1;
        $const = get_defined_constants(true);
        echo "<pre>";
        print_r($const['user']);
    }

    /**
     * 空方法 没有找到方法时执行
     */
    public function notFound()
    {
        return $this->fetch();
    }
}
