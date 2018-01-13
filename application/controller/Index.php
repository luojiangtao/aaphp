<?php

namespace application\controller;

use aaphp\Controller;

/**
 * 访问该文件夹下面的控制器，需要设置 config/common.php
 * 'moduel_status'         => false,//关闭分组状态
 * Class ViewController
 * @package application\example\controller
 */
class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function test()
    {
        var_dump('test');
    }
}
