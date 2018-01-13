<?php
namespace console\migration;

use aaphp\console\Migration;

/**
 * 数据库升级例子
 * Class _2017_12_02_03_38_11_test_add_column
 * @package console\migration
 */
class _2017_12_02_03_38_11_test_add_column extends Migration
{
    /**
     * 升级数据库
     */
    public function up()
    {
        $sql="ALTER TABLE `{$this->prefix}test`
                ADD COLUMN `birthday` varchar(32) NOT NULL DEFAULT '' COMMENT '生日'";
        $this->execute($sql);
    }

    /**
     * 降级数据库
     */
    public function down()
    {
        $sql="ALTER TABLE `{$this->prefix}test`
                DROP COLUMN `birthday`";
        $this->execute($sql);
    }

}
