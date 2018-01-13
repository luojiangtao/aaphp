<?php

namespace aaphp;

/**
 * 记录日志类
 * Class Log
 * @package aaphp
 */
class Log
{
    /**
     * 写日志
     * @param string $message [错误信息]
     * @param string $file [错误文件]
     * @param integer $line [错误行数]
     * @param integer int $type [错误类型]
     * @param string null $path [日志路径]
     */
    public static function write($message, $file, $line, $type = 3, $path = null)
    {
        // 查看是否需要写日志
        $saveLog = Config::common('save_log');
        if (!$saveLog) {
            return;
        }

        // 如果路径为空则只用系统默认
        if (is_null($path)) {
            $path = LOG_PATH . '/' . date('Y_m_d') . '.log';
        }

        $errorMessage  = '[time]    : ' . date('Y_m_d H:i:s') . PHP_EOL;
        $errorMessage .= '[message] : ' . $message . PHP_EOL;
        $errorMessage .= '[file]    : ' . $file . PHP_EOL;
        $errorMessage .= '[line]    : ' . $line . PHP_EOL;
        $errorMessage .= PHP_EOL;

        // 没有文件夹则创建
        is_dir(LOG_PATH) || mkdir(LOG_PATH, 0777, true);

        // 写日志
        if (is_dir(LOG_PATH)) {
            error_log($errorMessage, $type, $path);
        }
    }
}
