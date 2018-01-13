<?php

namespace aaphp;

/**
 * 数据库
 * Class Database
 * @package aaphp
 */
class Database
{
//    保存连接信息 pod 对象
    protected $pdo = null;
//    预处理
    protected $prepare = null;
//    主机名
    protected $hostname;
//    数据库名
    protected $database;
//    用户名
    protected $username;
//    密码
    protected $password;
//    表前缀
    protected $prefix;
//    最后插入数据的id
    protected $lastInsertId;
//    影响的行数
    protected $rowCount;
//    记录发送的sql
    public $sqls = [];

    /**
     * 构造方法，载入数据库配置文件
     */
    public function __construct()
    {
        // 获取数据配置
        $this->hostname = Config::database('hostname');
        $this->database = Config::database('database');
        $this->username = Config::database('username');
        $this->password = Config::database('password');
        $this->prefix = Config::database('prefix');

        $this->connect();
    }

    /**
     * 链接数据库
     */
    private function connect()
    {
        if (!is_null($this->pdo)) {
            // 如果连接过，就不链接了。使用之前的链接，节约资源，提高效率
            return;
        }

        try {
//            pdo方式连接数据库
            $this->pdo = new \PDO("mysql:host={$this->hostname};dbname={$this->database}", $this->username, $this->password);
        } catch (\PDOException $e) {
            Error::halt('连接数据库失败:' . $e->getMessage());
        }
//        设置数据库编码
        $this->pdo->query("SET NAMES 'UTF8'");
//        设置时区
        $this->pdo->query("SET TIME_ZONE = '+8:00'");
    }

    /**
     * 开始事务
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * 事务回滚
     * @return bool
     */
    public function rollback()
    {
        return $this->pdo->rollback();
    }

    /**
     * 事务提交
     * @return bool
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * 最底层的数据库查询方法
     * @param string $sql [sql语句]
     * @param array $params
     * @return mixed [执行结果]
     */
    public function execute($sql, $params = [])
    {
//        保存sql语句，方便调试
        $this->sqls[] = $sql;
//        预处理sql
        $this->prepare = $this->pdo->prepare($sql);
//        预处理绑定参数
        $result = $this->prepare->execute($params);
        $this->getPDOError();

        switch (true) {
//            sql语句是删除，则返回受影响行数
            case stripos($sql, 'delete') !== false:
//            sql语句是更新，则返回受影响行数
            case stripos($sql, 'update') !== false:
                $this->rowCount = $this->prepare->rowCount();
                break;
//            sql语句是插入，则返回最后插入id
            case stripos($sql, 'insert') !== false:
                $this->lastInsertId = $this->pdo->lastInsertId();
                break;
//            sql语句是搜索，则返回查询结果集
            case stripos($sql, 'select') !== false:
                $result = $this->prepare->fetchAll(\PDO::FETCH_ASSOC);
                break;
//            sql语句是DDL，则返回查询结果集
//                create table 创建表
//                alter table  修改表
//                drop table 删除表
//                truncate table 删除表中所有行
//                create index 创建索引
//                drop index  删除索引
            default:
                $result = $this->prepare->fetchAll(\PDO::FETCH_ASSOC);
                break;
        }
        return $result;
    }

    /**
     * 获取数据库错误信息
     */
    protected function getPDOError()
    {
        if ($this->prepare->errorCode() != '00000') {
            $error = $this->prepare->errorInfo();
            Error::halt($error[2]);
        }
    }

    /**
     * 获取最后插入id
     * @return integer
     */
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    /**
     * 获取受影响行数
     * @return integer
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * 析构方法，销毁数据库链接对象
     */
    public function __destruct()
    {
        $this->pdo = null;
    }
}
