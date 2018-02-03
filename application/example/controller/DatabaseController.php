<?php

namespace application\example\controller;

use aaphp\Controller;
use aaphp\Config;
use aaphp\Database;
use aaphp\Model;

/**
 * 数据库例子
 * Class DatabaseController
 * @package application\example\controller
 */
class DatabaseController extends Controller
{
    /**
     * 插入
     */
    public function insert()
    {
        $sql = "insert into aa_user(username) values ('aaphp')";
        $database = new Database();
//        执行
        $result = $database->execute($sql);
//        新插入数据自增id
        $lastInsertId = $database->getLastInsertId();
        $result = $result ? 'true' : 'false';
        echo '执行结果：' . $result . '<br/>';
        echo '新插入数据自增id：' . $lastInsertId . '<br/>';
    }

    /**
     * 查询
     */
    public function select()
    {
        $sql = "select * from aa_user";
//        $sql="select count(*) as count from aa_user";
//        $sql="describe aa_user";
        $database = new Database();
//        执行
        $result = $database->execute($sql);
        echo '执行结果：<br/>';
        var_dump($result);
    }

    /**
     * 更新
     */
    public function update()
    {
        $sql = "update aa_user set user_name='lucy' where user_id>10";
        $database = new Database();
//        执行
        $result = $database->execute($sql);
        $rowCount = $database->getRowCount();
        var_dump('执行结果：' . $result);
        echo '受影响行数：' . $rowCount . '<br/>';
    }

    /**
     * 删除
     */
    public function delete()
    {
        $sql = "delete from aa_user where user_id>11";
        $database = new Database();
//        执行
        $result = $database->execute($sql);
        $rowCount = $database->getRowCount();
        var_dump('执行结果：' . $result);
        echo '受影响行数：' . $rowCount . '<br/>';
    }

    /**
     * 绑定参数，预处理，防止sql注入
     */
    public function bindParam()
    {
        $database = new Database();
//        需要绑定的参数
        $param = ['user_id' => 5, 'user_name' => 'aaphp'];
        $result = $database->execute("select * from aa_user where user_id>:user_id and user_name!=:user_name", $param);
        var_dump($result);
    }

    /**
     * 事务处理
     */
    public function transaction()
    {
        $sql = "insert into aa_user(username) values ('aaphp')";

        $database = new Database();
        $result = $database->beginTransaction();
        if ($result) {
            echo '开启事务成功<br/>';
        } else {
            echo '开启事务失败<br/>';
        }

        $database->execute($sql);

        if (false) {
            $result = $database->rollback();
            if ($result) {
                echo '事务回滚成功<br/>';
            } else {
                echo '事务回滚失败<br/>';
            }
        } else {
            $result = $database->commit();
            if ($result) {
                echo '事务提交成功<br/>';
            } else {
                echo '事务提交失败<br/>';
            }
        }
    }
}