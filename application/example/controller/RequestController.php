<?php

namespace application\example\controller;

use aaphp\Request;
use aaphp\Controller;

/**
 * 请求例子
 * Class RequestController
 * @package application\example\controller
 */
class RequestController extends Controller
{

    /**
     * 获取请求相关名称
     */
    public function name()
    {
        $request = Request::instance();

        $mudule = $request->getMudule();
        echo "当前模块名称：{$mudule}<br/>";

        $controller = $request->getController();
        echo "当前控制器名称：{$controller}<br/>";

        $action = $request->getAction();
        echo "当前方法名称：{$action}<br/>";
    }

    /**
     * 获取请求相关参数
     */
    public function param()
    {
        $request = Request::instance();
        echo '请求方法：' . $request->getMethod() . '<br/>';
        echo '访问ip地址：' . $request->getIp() . '<br/>';
        echo '是否AJax请求：';
        var_dump($request->isAjax());
        echo '是否isPost请求：';
        var_dump($request->isPost());
        echo '是否isGet请求：';
        var_dump($request->isGet());
    }

    /**
     * 获取请求传参
     */
    public function requestParam()
    {
        $request = Request::instance();
        echo '请求参数：';
        var_dump($request->request());
        echo '获取单个请求参数age：' . $request->request('age') . '<br/>';

        echo '获取post全部请求参数：';
        var_dump($request->post());
        echo '获取post单个请求参数age：' . $request->post('age') . '<br/>';

        echo '获取get全部请求参数：';
        var_dump($request->get());
        echo '获取get单个请求参数age：' . $request->get('age') . '<br/>';
    }
}
