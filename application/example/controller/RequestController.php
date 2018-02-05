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
        $isAjax = $request->isAjax() ? 'true' : 'false';
        echo '是否AJax请求：' . $isAjax . '<br/>';
        $isPost = $request->isPost() ? 'true' : 'false';
        echo '是否isPost请求：' . $isPost . '<br/>';
        $isGet = $request->isGet() ? 'true' : 'false';
        echo '是否isGet请求：' . $isGet . '<br/>';
    }

    /**
     * 获取请求传参
     */
    public function requestParam()
    {
        $request = Request::instance();
        echo '请求参数：';
        var_dump($request->request());
        echo '获取单个请求参数age：' . $request->request('age');

        echo '获取post全部请求参数：';
        var_dump($request->post());
        echo '获取单个请求参数age：' . $request->post('age');

        echo '获取get全部请求参数：';
        var_dump($request->get());
        echo '获取单个请求参数age：' . $request->get('age');
    }

    /**
     * 获取json格式参数，请求header头必须是 header('Content-type: application/json');才能接收到
     */
    public function json()
    {
        $request = Request::instance();
        if (!$request->isPost()) {
//            不是post请求，载入视图模版
            return $this->fetch();
        }

        $data = [
            'status' => 1,
            'message' => "服务器已接收到json数据",
//            获取全部
            'data' => $request->request(),
//            只获取age
            'age' => $request->request('age'),
            'time' => date('Y-m-d H:i:s', time()),
        ];
        return $data;
    }
}
