<?php

namespace aaphp\console;

use aaphp\Database;

/**
 * 数据库迁移
 * Class Migration
 * @package aaphp\console
 */
class Migration extends Database
{
    /**
     * 构造方法
     * Migration constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->createTableMigration();
    }

    /**
     * 创建migration表，如果不存在
     */
    public function createTableMigration()
    {
//        查看表是否存在
        $sql = "show tables like '{$this->prefix}migration'";
        $result = $this->execute($sql);
        if (!$result) {//不存在，则创建
            $sql = "CREATE TABLE `{$this->prefix}migration` (
                  `version` varchar(128) NOT NULL DEFAULT '' COMMENT '版本号',
                  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
                  PRIMARY KEY (`version`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据库版本控制'";
            $this->execute($sql);
        }
    }

    /**
     * 获取migration文件列表
     * @param string $type [类型，up,down]
     * @param int $number [获取文件数量]
     * @param bool $isReverse [是否倒叙排序]
     * @return array [可以处理的migration列表]
     */
    public function getList($type, $number = 1000, $isReverse = false)
    {
        $list = [];
        $migrationDir = ROOT_PATH . '/console/migration';
        $migrationist = scandir($migrationDir);
        if ($isReverse) {
//        倒序排序
            $migrationist = array_reverse($migrationist);
        }

        foreach ($migrationist as $key => $value) {
            if ('.php' == strchr($value, '.php')) {
                if (0 == $number) {
                    break;
                }
                $number--;
                $className = str_replace('.php', '', $value);
//                查看执行记录
                $sql = "select count(*) as count FROM {$this->prefix}migration WHERE version='{$className}'";
                $result = $this->execute($sql);
                if ('up' == $type && 0 == $result[0]['count']) {//升级方法，并且未升级过
                    $list[] = $className;
                }
                if ('down' == $type && 0 != $result[0]['count']) {//降级方法，并且已升级过
                    $list[] = $className;
                }
            }
        }
        return $list;
    }

    /**
     * 数据库升级
     */
    public function migrateUp()
    {
        $migrationist = $this->getList('up');
        if (!$migrationist) {
            Console::outPut('No migration needs to be up');
            return;
        }

        $listString = '';
        foreach ($migrationist as $key => $value) {
//            推荐换行
            $listString .= $value . PHP_EOL;
        }
        $confirmString = $listString . "These migration can be upgraded to confirm the up [y]/n (yes/no) [yes]:";
        if (!Console::confirm($confirmString)) {
            return;
        }

        foreach ($migrationist as $key => $value) {
            $migrationClass = 'console\migration\\' . $value;
//            var_dump($defaultController);
            if (class_exists($migrationClass)) {
//                通过反射实例化类
                $reflectionClass = new \ReflectionClass($migrationClass);
                $method = 'up';
                if ($reflectionClass->hasMethod($method)) {
                    $class = $reflectionClass->newInstance();
//                    执行升级方法
                    $class->$method();
                    $sql = "INSERT INTO {$this->prefix}migration (version,time) VALUE ('{$value}'," . time() . ")";
//                    保存升级记录
                    $this->execute($sql);
                    Console::outPut("{$value}->{$method} : success!");
                } else {
                    Console::outPut("{$value}->{$method} : not exists!");
                }
            } else {
                Console::outPut("{$value} : not exists!");
            }
        }
    }

    /**
     * 数据库降级
     * @param integer $number [降级数量]
     */
    public function migrateDown($number)
    {
        $migrationist = $this->getList('down', $number, true);

        if (!$migrationist) {
            Console::outPut('There is no migration to be down');
            return;
        }

        $listString = '';
        foreach ($migrationist as $key => $value) {
            $listString .= $value . PHP_EOL;
        }

        $confirmString = $listString . "These migration can be upgraded to confirm the up [y]/n (yes/no) [yes]:";
        if (!Console::confirm($confirmString)) {
            return;
        }

        foreach ($migrationist as $key => $value) {
            $migrationClass = 'console\migration\\' . $value;
            if (class_exists($migrationClass)) {
//                通过反射实例化类
                $reflectionClass = new \ReflectionClass($migrationClass);
                $method = 'down';
                if ($reflectionClass->hasMethod($method)) {
                    $class = $reflectionClass->newInstance();
//                    执行降级方法
                    $class->$method();
//                    删除升级记录
                    $sql = "DELETE FROM {$this->prefix}migration WHERE version='{$value}'";
                    $this->execute($sql);
                    Console::outPut("{$value}->{$method} : success!");
                } else {
                    Console::outPut("{$value}->{$method} : not exists!");
                }
            } else {
                Console::outPut("{$value} : not exists!");
            }
        }
    }
}
