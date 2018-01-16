<?php

namespace aaphp;

/**
 * 配置
 * Class Config
 * @package aaphp
 * @method mixed common(string $key = '', string $value = '') static 公共配置文件
 * @method mixed database(string $key = '', string $value = '') static 数据库配置文件
 * @method mixed router(string $key = '', string $value = '') static 路由配置文件
 * @method mixed param(string $key = '', string $value = '') static 自定义参数配置文件
 */
final class Config
{
    /**
     * @var object 对象实例 单例
     */
    private static $instance;

    /**
     * @var array 保存全部的配置信息，多维数组
     */
    private static $config = [];

    /**
     * 单例模式，获取自身实例化对象
     * @return object [自身实例化对象]
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 保护，防止外部实例化，单例模式
     */
    protected function __construct()
    {
//        读取 ./config下面的所有配置文件
        $fileList = scandir(USER_CONFIG_PATH);
        foreach ($fileList as $key => $value) {
            if ('.php' == strchr($value, '.php')) {
                $configPath = USER_CONFIG_PATH . '/' . $value;
//                引入
                $config = include_once($configPath);
                $key = str_replace('.php', '', $value);
//                保存配置内容
                self::$config[$key] = $config;
            }
        }
    }

    /**
     * 配置信息的读取或修改
     * @param $type [配置文件名称（不包含后缀名）例子：Config::common('default_module');]
     * @param string $key [配置文件的键名]
     * @param string $value [配置文件的值]
     * @return array|string [配置文件信息]
     */
    public function config($type, $key = null, $value = null)
    {
        if (is_null($key)) {// 什么都不传，则返回所有配置项信息
            return self::$config[$type];
        } else if (!is_null($key) && is_null($value)) {// 只传了$key，则返回对应value
            if (isset(self::$config[$type][$key])) {
                return self::$config[$type][$key];
            } else {
                return '';
            }
        } else {// $key和$value都传，则是临时改变配置的值
            self::$config[$type][$key] = $value;
        }
    }

    /**
     * 使方法可以被静态调用 例子：Config::common('default_module');
     * @param string $function [需要调用的方法]
     * @param array $arguments [参数数组]
     * @return mixed|string [配置文件信息]
     */
    public static function __callStatic($function, $arguments)
    {
        $class = Config::instance();
        $key = array_shift($arguments);
        $value = array_shift($arguments);
        if (!isset(self::$config[$function])) {
            Error::halt(USER_CONFIG_PATH . '/' . $function . '.php 不存在');
        }
        return $class->config($function, $key, $value);
    }
}
