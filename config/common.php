<?php
/**
 * 公共配置文件
 */
return [
    /**
     * 是否开启分组
     * 开启后，访问 application/模块文件夹(index)/控制器文件夹(controller)/控制器(IndexController)/方法(index)
     * 关闭后，访问 application/控制器文件夹(controller)/控制器(IndexController)/方法(index)
     */
    'moduel_status'         => true,
    // 默认加载模块
    'default_moduel'         => 'index',
    // 默认控制器
    'default_controller'         => 'Index',
    // 默认方法
    'default_action'         => 'index',
    // 404 页面
    'default_not_found'         => 'notFound',
    // 默认时区，中国
    'default_time_zone'   => 'PRC',
    // 默认开启session
    'session_auto_start'  => true,
    // 是否开启记录日志
    'save_log'            => true,
    // 是否开启缓存
    'is_cache'            => true,
    // xss 安全处理
    'xss_filter'           => true,
];
