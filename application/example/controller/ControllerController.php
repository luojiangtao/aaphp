<?php

namespace application\example\controller;

use aaphp\Controller;

/**
 * 控制器例子
 * Class ControllerController
 * @package application\example\controller
 */
class ControllerController extends Controller
{
    /**
     * 构造方法，预先调用
     * ControllerController constructor.
     */
    public function __construct()
    {
        echo '__construct<br/>';
    }

    /**
     * 首页
     * @return string
     */
    public function index()
    {
        return 'index<br/>';
    }

}
