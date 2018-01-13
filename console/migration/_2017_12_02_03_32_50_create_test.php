<?php
namespace console\migration;

use aaphp\console\Migration;

/**
 * 数据库迁移例子
 * Class _2017_12_02_03_32_50_create_test
 * @package console\migration
 */
class _2017_12_02_03_32_50_create_test extends Migration
{
    /**
     * 升级数据库
     */
    public function up()
    {
        $sql="CREATE TABLE `{$this->prefix}test` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
                  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
                  `age` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '年龄',
                  `city` varchar(255) DEFAULT NULL COMMENT '城市',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='测试'";
        $this->execute($sql);
    }

    /**
     * 降级数据库
     */
    public function down()
    {
        $sql="DROP table `{$this->prefix}test`";
        $this->execute($sql);
    }

}
