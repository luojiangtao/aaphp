<?php

namespace application\example\controller;

use aaphp\Controller;
use aaphp\Url;

/**
 * url地址生成例子
 * Class UrlController
 * @package application\example\controller
 */
class UrlController extends Controller
{
    /**
     * 生成url
     */
    public function index()
    {
        $url = Url::build('index');
        echo '当前模块，当前控制器，的 index 方法：' . $url . '<br/>';
        $url = Url::build('UrlController/index');
        echo '当前模块，UrlController控制器，的 index 方法：' . $url . '<br/>';
        $url = Url::build('example/UrlController/index');
        echo 'example 模块，UrlController 控制器，的 index 方法：' . $url . '<br/>';
        echo '<br/>';

        $param = ['name' => 'lucy', 'age' => 18];
        $url = Url::build('index', $param);
        echo '增加get传参：' . $url . '<br/>';
        echo '<br/>';

        $suffix = '.html';
        $url = Url::build('index', $param, $suffix);
        echo '增加后缀名：' . $url . '<br/>';
        echo '<br/>';

        $param = ['name' => 'lucy', 'age' => 18];
        $url = Url::build('index', $param, $suffix, true);
        echo '完整域名：' . $url . '<br/>';
    }
}
