<?php
namespace console\controller;
use aaphp\Controller;
use application\example\model\User;

/**
 * 命令行模式
 * Class IndexController
 * @package console\controller
 */
class Index extends Controller
{
    /**
     * 命令行模式运行：php aa controller:run Index/index
     * 需要在 aa 同目录下
     */
    public function index()
    {
        echo 'hello aaphp';
    }
}
