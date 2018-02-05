<?php
    header('Content-type:text/html;charset=utf-8');
//    返回json格式
//    header('Content-type: application/json');
    use aaphp\AA;
//	调试模式开启
//	define("DEBUG", false);
	define("DEBUG", true);
//	引入框架入口文件
	require_once("./aaphp/AA.php");
//	执行框架入口
    AA::start();
?>