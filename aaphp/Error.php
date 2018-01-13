<?php

namespace aaphp;

/**
 * 错误
 * Class Error
 * @package aaphp
 */
class Error
{
    /**
     * 注册异常处理
     */
    public static function register()
    {
        error_reporting(E_ALL);
//        set_error_handler([__CLASS__, 'appError']);

//        注册自定义错误处理
        set_error_handler(array(__CLASS__, 'errorHandler'));
//        获取致命性错误
//        register_shutdown_function(array(__CLASS__, 'shutdown'));
//        注册异常处理
        set_exception_handler([__CLASS__, 'exceptionHandler']);
    }

    /**
     * 异常处理
     * @param $exception [异常类]
     */
    public static function exceptionHandler($exception)
    {
        $error['message'] = $exception->getMessage();// 返回异常信息
        $error['code'] = $exception->getCode();// 返回异常代码
        $error['file'] = $exception->getFile();// 返回发生异常的文件名
        $error['line'] = $exception->getLine(); // 返回发生异常的代码行号
//        $error['trace'] = $exception->getTraceAsString();// 已格成化成字符串的 getTrace() 信息
        Error::halt($error);
    }

    /**
     * 致命性错误处理
     * @Author   罗江涛
     * @DateTime 2016-08-08T15:15:02+0800
     */
//    public static function shutdown()
//    {
//        $error = error_get_last();
////        var_dump($error);die;
//        if ($error) {
//            self::notice($error['type'], $error['message'], $error['file'], $error['line']);
//        }
//    }

    /**
     * 错误处理方法，包括警告性错误和致命性错误
     * @param    integer $errorNumber [错误级别]
     * @param    string $errorMessage [错误信息]
     * @param    string $file [错误文件]
     * @param    integer $line [第几行]
     */
    public static function errorHandler($errorNumber, $errorMessage, $file, $line)
    {
        $message['message'] = $errorMessage;
        $message['file'] = $file;
        $message['line'] = $line;
        Error::halt($message);
    }

    /**
     * 抛出错误页面
     * @param    string|array $message [错误信息]
     * @param    string $level [错误类型]
     * @param    integer $type [错误代码]
     * @param    string $dest [保存日志的路径]
     */
    public static function halt($message, $level = 'ERROR', $type = 3, $dest = null)
    {
        $saveLog=Config::common('save_log');
        $error = array();
        if (DEBUG || $saveLog) {
            $trace = debug_backtrace();
            if (!is_array($message)) {
    //                运行了哪些文件
                $error['message'] = $message;
                $error['file'] = $trace[0]['file'];
                $error['line'] = $trace[0]['line'];
                $error['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
                $error['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';
            } else {
                $error = $message;
            }
    //            开启缓存区
            ob_start();
    //            把打印的运行的文件信息保存到缓存区
            debug_print_backtrace();
    //            取出缓存区内容
            $error['trace'] = ob_get_clean();
        }

        if($saveLog){
            Log::write($error['message'], $error['file'], $error['line']);
        }

//        不是DUBUG
        if (!DEBUG) {
            $error = [];
            $error['message'] = '网站出错了，请重试...';
        }

//        加载并执行错误页面
        include AA_PATH . '/template/halt.html';
//        退出
        exit;
    }
}
