<?php

namespace aaphp\console;

/**
 * 控制台，命令行模式
 * Class Console
 * @package aaphp\console
 */
class Console
{
//    命令，例子：migrate:down
    private $command;
//    值 例子：[php aa migrate:down 2] 中的 2
    private $value;

    /**
     * 从该方法开始执行
     */
    public function start()
    {
        $this->getInput();
        $this->excute();
    }

    /**
     * 打印到控制台
     * @param string $string [需要输出到控制台的字符串]
     */
    public static function outPut($string)
    {
        echo PHP_EOL . $string;
    }

    /**
     * 获取用户输入，并保存到类变量，如：php aa migrate:down 2
     */
    public function getInput()
    {
//        从控制台获取参数
        $inputArray = $_SERVER['argv'];
//        去掉第一个
        array_shift($inputArray);

        $this->command = array_shift($inputArray);
        $this->value = array_shift($inputArray);
        $allCommand = [
            'controller:create',
            'controller:run',
            'model:create',
            'validate:create',
            'migrate:create',
            'migrate:up',
            'migrate:down',
        ];
    }

    /**
     * 创建模版文件
     * @param string $type [文件类型，如：Migration,Controller,Model]
     * @param string $name [类名称]
     */
    public function create($type, $name)
    {
        $confirmString = "Create {$type}? [y]/n (yes/no) [yes]:";
        if (!Console::confirm($confirmString)) {
            return;
        }

        $templateDir = ROOT_PATH . "/aaphp/console/template/";
        $suffix = '.template';
        $templatePath = $templateDir . $type . $suffix;
//        获取模版内容
        $template = file_get_contents($templatePath);
//        替换类名
        $template = str_replace('{%className%}', $name, $template);

        if (file_exists($template)) {
            Console::outPut('file is exists');
        } else {
            $newFile = ROOT_PATH . '/console/migration/' . $name . '.php';
            if (file_put_contents($newFile, $template)) {
                Console::outPut("create file : {$newFile} success!");
            } else {
                Console::outPut("create file : {$newFile} fail, please check file permissions!");
            }
        }
    }

    /**
     * 让用户确认是否执行 例子：These migration can be upgraded to confirm the up [y]/n (yes/no) [yes]:
     * @param string $confirmString [提示用户确认字符串]
     * @return bool [确认结果]
     */
    public static function confirm($confirmString)
    {
        fwrite(STDOUT, $confirmString);
        $confirm = trim(strtolower(fgets(STDIN)));
        if ('y' != $confirm && 'yes' != $confirm) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 是否是运行controller
     * @return bool
     */
    public function isControllerRun()
    {
        if ($this->command == 'controller:run') {
//            保存到path_info中，在路由解析的时候用
            $_SERVER['PATH_INFO'] = $this->value;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 执行命令
     */
    public function excute()
    {
        switch ($this->command) {
//            创建migration
            case 'migrate:create':
                $name = '_' . date('Y_m_d_H_i_s_', time()) . $this->value;
                $this->create('Migration', $name);
                break;
//                数据库升级
            case 'migrate:up':
                $migration = new Migration();
                $migration->migrateUp();
                break;
//                数据库降级
            case 'migrate:down':
                $number = $this->value;
                if (!is_numeric($number)) {
                    Console::outPut('command error. You can use an example: php aa migrate:down 1');
                    return;
                }
                $migration = new Migration();
                $migration->migrateDown($number);
                break;
//                命令行模型运行controller
            case 'controller:run':

                break;
            default:
                break;
        }
    }
}
