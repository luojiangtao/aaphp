<?php

namespace aaphp;

/**
 * 路由
 * Class Request
 * @package aaphp
 */
class Request
{
//    自身对象实例
    private static $instance;
//    模块
    private $module;
//    控制器
    private $controller;
//    方法
    private $action;
//    接收的post和get参数
    private $request = [];
//    接收的post参数
    private $post = [];
//    接收的get参数
    private $get = [];

    /**
     * 构造方法
     */
    protected function __construct()
    {
        $pathInfo = Router::instance()->getPathInfo();
        // 定义分组名，控制器名，方法名，给其他地方用
        $this->analyticParameter($pathInfo);

        $this->post = $_POST;
        $this->get = array_merge($this->get, $_GET);

        if (is_array($this->post)) {
            foreach ($this->post as $key => $value) {
//                安全处理
                $this->post[$key] = htmlspecialchars($value);
            }
        }

        if (is_array($this->get)) {
            foreach ($this->get as $key => $value) {
//                安全处理
                $this->get[$key] = htmlspecialchars($value);
                $_GET[$key] = $value;
            }
        }

        $this->request = array_merge($this->post, $this->get);
    }

    /**
     * 获取自身实例对象，单例模式
     * @return Request [自身实例对象]
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 设置模块
     * @param string $module [模块名称]
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * 设置控制器
     * @param string $controller [控制器名称]
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * 设置方法
     * @param string $action [方法名称]
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * 获取模块名
     * @return string
     */
    public function getMudule()
    {
        return $this->module;
    }

    /**
     * 获取控制器名称
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * 获取方法名
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * 获取ip地址
     * @return string
     */
    public function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * 获取请求方法
     * @return string
     */
    public function getMethod()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
    }

    /**
     * 是否是ajax请求
     * @return bool
     */
    public function isAjax()
    {
        // 系统变量，是否是AJAX提交
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $isAjax = true;
        } else {
            $isAjax = false;
        }
        return $isAjax;
    }

    /**
     * 是否是get请求
     * @return bool
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 是否是post请求
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
    }

    /**
     * 获取post和get传参
     * @param string $key [传参的键名]
     * @param string $default [当结果为空时，返回默认值]
     * @return array|string
     */
    public function request($key = '', $default = '')
    {
        if ($key && isset($this->request[$key])) {
            return $this->request[$key];
        }

        if ($key && !isset($this->request[$key])) {
            return $default;
        }
        return $this->request;
    }

    /**
     * 获取post传参
     * @param string $key [传参的键名]
     * @param string $default [当结果为空时，返回默认值]
     * @return array|string
     */
    public function post($key = '', $default = '')
    {
        if ($key && isset($this->post[$key])) {
            return $this->post[$key];
        }

        if ($key && !isset($this->post[$key])) {
            return $default;
        }
        return $this->post;
    }

    /**
     * 获取get传参
     * @param string $key [传参的键名]
     * @param string $default [当结果为空时，返回默认值]
     * @return array|string [get传参]
     */
    public function get($key = '', $default = '')
    {

        if ($key && isset($this->get[$key])) {
            return $this->get[$key];
        }

        if ($key && !isset($this->get[$key])) {
            return $default;
        }
        return $this->get;
    }

    /**
     * pathinfo的方式解析路由，定义项目组，控制器，方法名称，并赋值给$_GET
     * @param $pathInfo [解析字符串，如：'Test/index' ]
     */
    private function analyticParameter($pathInfo)
    {
        // 分组名称
        $this->module = Config::common('default_moduel');
        // 控制器名称
        $this->controller = Config::common('default_controller');
        // 方法名称
        $this->action = Config::common('default_action');

        $pathInfo = trim($pathInfo);
        $first_str = substr($pathInfo, 0, 1);

        // 如果第一字符是 / 就去掉
        if ($first_str == '/') {
            $pathInfo = substr($pathInfo, 1);
        }

        // 拆分
        $params = explode('/', $pathInfo);
        $moduelStatus = Config::common('moduel_status');
        if ('cli' != PHP_SAPI && $moduelStatus) {//浏览器模型运行 并且开启分组模式
            // 分组名称
            if (isset($params[0]) && $params[0]) {
                // 删除数组第一个值，并赋值给$app_name
                $this->module = array_shift($params);
                define('MODULE_NAME', $this->module);
            }
        }

        if (isset($params[0]) && $params[0]) {
            // 删除数组第一个值，并赋值给$controller
            $this->controller = array_shift($params);
            define('CONTROLLER_NAME', $this->controller);
        }

        if (isset($params[0]) && $params[0]) {
            // 删除数组第一个值，并赋值给$action
            $this->action = array_shift($params);
            define('ACTION_NAME', $this->action);
        }

        // 后面部分赋值给 $this->param
        foreach ($params as $key => $value) {
            if ($key % 2 == 0) {
                $this->get[$value] = isset($params[$key + 1]) ? $params[$key + 1] : '';
            }
        }
    }
}
