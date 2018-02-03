<?php

namespace aaphp;

/**
 * 数据库模型
 * Class Model
 * @package aaphp
 */
class Model extends Database
{
    // 需要操作的数据库表名
    protected $table = null;
    // 初始化查询数据库表信息
    private $condition;
//    预处理绑定的参数
    protected $param = [];

    /**
     * 构造方法
     * Model constructor.
     * @param string $table [需要操作的表名]
     */
    public function __construct($table = null)
    {
        parent::__construct();
//        获取表前缀
        $prefix = Config::database('prefix');
        $this->table = is_null($table) ? $prefix . $this->table : $prefix . $table;
        // 链接数据库
        $this->_condition();
    }

    /**
     * 获取所有的数据
     * @return array [查询结果]
     */
    public function select()
    {
        $sql = 'SELECT ' . $this->condition['field'] . ' FROM ' . $this->table . $this->condition['where'] . $this->condition['group'] . $this->condition['having'] . $this->condition['order'] . $this->condition['limit'];
        return $this->execute($sql, $this->param);
    }

    /**
     * 获取一条数据
     * @return array [查询结果]
     */
    public function selectOne()
    {
        $result = $this->limit(1)->select();
        $data = isset($result[0]) ? $result[0] : '';
        return $data;
    }

    /**
     * 获取一条数据的指定字段
     * @param string $field [需要获取的字段名]
     * @return string [查询结果]
     */
    public function getField($field)
    {
        $result = $this->field($field)->selectOne();
        $data = isset($result[$field]) ? $result[$field] : '';
        return $data;
    }

    /**
     * 设置需要获取的字段
     * @param string|array $field [需要获取的字段名]
     * @return $this [当前对象]
     */
    public function field($field)
    {
        if (is_array($field)) {
            $fieldString = '';
            foreach ($field as $key => $value) {
                $fieldString .= $value . ',';
            }
            $fieldString = trim($fieldString, ',');
            $this->condition['field'] = $fieldString;
        } else {
            $this->condition['field'] = $field;
        }

        return $this;
    }

    /**
     * 设置sql需要获取条件
     * @param $where [需要查询的条件]
     * @return $this [当前对象]
     */
    public function where($where)
    {
        if (!$where || !is_array($where)) {
            return $this;
        }
        if (count($where) == count($where, COUNT_RECURSIVE)) {//一维数组
            if (!isset($where[2])) {
                Error::halt('where 条件不符合规范，如：where([\'name\',\'=\',\'aaphp\'])');
            }
            $this->analysisWhere($where);
        } else {//多维数组

            foreach ($where as $key => $value) {
                if (!isset($value[2])) {
                    Error::halt('where 条件不符合规范，如：where([\'name\',\'=\',\'aaphp\'])');
                }
                $this->analysisWhere($value);
            }
        }
        return $this;
    }

    /**
     * 解析 where 条件 组装 sql 字符串 where 部分
     * @param array $where [查询条件]
     * @return $this [自身对象]
     */
    public function analysisWhere($where)
    {
//        数据库字段名，如 age
        $field = $where[0];
//        判断的值 如 18
        $value = $where[2];
//        判断的类型 如 >
        $condition = strtoupper($where[1]);
        $condition = trim($condition);
        $whereStr = '';
        switch ($condition) {
            case 'IN':
            case 'NOT IN':
                $valueString = join(',', $value);
//                去掉最后的,
                $valueString = trim($valueString, ',');
                $whereStr .= $field . ' ' . $condition . ' (' . $valueString . ') ';
                break;
            case 'BETWEEN':
            case 'NOT BETWEEN':
                $max = $value[1];
                $mini = $value[0];
                if (!is_numeric($mini) || !is_numeric($max)) {
                    Error::halt($condition . ' ' . $mini . ' 和 ' . $max . '必须是数字');
                }
                $whereStr .= $field . ' ' . $condition . ' ' . $mini . ' AND ' . $max;
                break;
            case 'IS':
            case 'IS NOT':
                $whereStr .= $field . ' ' . $condition . ' ' . $value;
                break;
            case '=':
            case '!=':
            case '>':
            case '<':
            case '>=':
            case '<=':
            case 'LIKE':
            case 'NOT LIKE':
                $paramKey = $field;
                if (isset($this->param[$field])) {
//                    防止参数绑定重名，如 两个 :id
                    $paramKey = $field . '_' . uniqid();
                }
                $whereStr .= $field . ' ' . $condition . ' :' . $paramKey;
                $this->param[$paramKey] = $value;
                break;
            default:
                Error::halt($condition . ' ：不合法，请检查where条件是否正确');
                break;
        }

        if (empty($this->condition['where'])) {
            $this->condition['where'] = ' WHERE ' . $whereStr;
        } else {
            $this->condition['where'] .= ' AND ' . $whereStr;
        }

        return $this;
    }

    /**
     * 设置sql排序规则
     * @param string $order [排序规则]
     * @return $this [当前对象]
     */
    public function order($order)
    {
        $this->condition['order'] = ' ORDER BY ' . $order;
        return $this;
    }

    /**
     * 设置sql从第几行，获取多少条
     * @param string $limit [从第几行，获取多少条]
     * @return $this [当前对象]
     */
    public function limit($limit)
    {
        $this->condition['limit'] = ' LIMIT ' . $limit;
        return $this;
    }

    /**
     * 设置sql,搜索结果按照哪个字段分组
     * @param string $group [搜索结果按照哪个字段分组]
     * @return $this [当前对象]
     */
    public function group($group)
    {
        $this->condition['group'] = ' GROUP BY ' . $group;
        return $this;
    }

    /**
     * 设置sql需要获取条件
     * @param string $having [需要查询的条件]
     * @return $this [当前对象]
     */
    public function having($having)
    {
        $this->condition['having'] = ' HAVING ' . $having;
        return $this;
    }

    /**
     * sql语句初始化信息，用来组装sql语句
     */
    private function _condition()
    {
        $this->condition = array(
            'field' => '*',
            'where' => '',
            'group' => '',
            'having' => '',
            'order' => '',
            'limit' => '',
        );
    }

    /**
     * 更新操作
     * @param array $data [需要保存到数据库的数组]
     * @return integer [影响的行数]
     */
    public function update($data = null)
    {
        if (empty($this->condition['where'])) {
            Error::halt('更新语句必须有where条件');
        }
        // 没有传值，则从$_POST 中获取
        if (is_null($data)) {
            $data = $_POST;
        }

        // 组装sql语句
        $values = '';
        foreach ($data as $key => $value) {
            $values .= "`" . $key . "`=" . ":" . $key . ",";
            $this->param[$key] = $value;
        }

        // 去掉最后的,
        $values = trim($values, ',');
        $sql = 'UPDATE ' . $this->table . ' SET ' . $values . $this->condition['where'];
        return $this->execute($sql, $this->param);
    }

    /**
     * 获取运行过的所有sql语句
     * @return array
     */
    public function getSql()
    {
        return $this->sqls;
    }

    /**
     * 删除操作
     * @return integer [影响行数]
     */
    public function delete()
    {
        if (empty($this->condition['where'])) {
            Error::halt('删除语句必须有where条件');
        }
        // 组装sql语句
        $sql = 'DELETE FROM ' . $this->table . $this->condition['where'];
        return $this->execute($sql, $this->param);;
    }

    /**
     * 添加操作
     * @param array $data 被添加的数据
     * @return integer [自增主键ID]
     */
    public function insert($data = null)
    {
        // 没有传值，则从$_POST 中获取
        if (is_null($data)) {
            return null;
        }

        // 组装sql语句
        $fields = '';
        $values = '';
        foreach ($data as $key => $value) {
            $fields .= "`" . $key . "`,";
            $values .= ":" . $key . ",";
            $this->param[$key] = $value;
        }

        // 去掉最后的,
        $fields = trim($fields, ',');
        $values = trim($values, ',');

//        拼接sql语句，最后执行例子：$result=$database->execute("select * from aa_user where user_id>:user_id and user_name!=:user_name",['user_id'=>5,'user_name'=>'aaphp']);
        $sql = 'INSERT INTO ' . $this->table . ' (' . $fields . ') VALUES (' . $values . ')';
//        执行
        $this->execute($sql, $this->param);
        $lastInsertId = $this->pdo->lastInsertId();
        return $lastInsertId;
    }

    /**
     * 统计有多少条数据
     * @return integer [有多少条数据]
     */
    public function count()
    {
        $sql = 'SELECT COUNT(*) AS count FROM ' . $this->table . $this->condition['where'] . $this->condition['group'] . $this->condition['having'];
        // 执行sql语句
        $count = $this->execute($sql, $this->param);
        $count = $count[0]['count'];
        return $count;
    }

    /**
     * 求平均数
     * @param string $filed
     * @return integer [平均数]
     */
    public function avg($filed)
    {
        $sql = 'SELECT AVG(' . $filed . ') AS avg FROM ' . $this->table . $this->condition['where'] . $this->condition['group'] . $this->condition['having'];
        // 执行sql语句
        $avg = $this->execute($sql, $this->param);
        return $avg;
    }

    /**
     * 求最大值
     * @param string $filed [数据库字段名]
     * @return integer [最大值]
     */
    public function max($filed)
    {
        $sql = 'SELECT MAX(' . $filed . ') AS max FROM ' . $this->table . $this->condition['where'];
        // 执行sql语句
        $max = $this->execute($sql, $this->param);
        return $max;
    }

    /**
     * 求最小值
     * @param string $filed [数据库字段名]
     * @return integer [最小值]
     */
    public function min($filed)
    {
        $sql = 'SELECT MIN(' . $filed . ') AS min FROM ' . $this->table . $this->condition['where'];
        // 执行sql语句
        $min = $this->execute($sql, $this->param);
        return $min;
    }

    /**
     * 修改单个字段
     * @param string $field [数据库字段]
     * @param string $value [修改的值]
     * @return integer [影响的行数]
     */
    public function setField($field, $value)
    {
        $data[$field] = $value;
        return $this->update($data);
    }

    /**
     * 增加数字
     * @param string $field [需要增加的字段]
     * @param integer $step [增加的数值]
     * @return integer [受影响行数]
     */
    public function increase($field, $step = 1)
    {
        if (empty($this->condition['where'])) {
            Error::halt('更新语句必须有where条件');
        }
        $sql = 'UPDATE ' . $this->table . ' SET `' . $field . '`=`' . $field . '`+' . $step . $this->condition['where'];
        return $this->execute($sql, $this->param);
    }

    /**
     * 减少数字
     * @param string $field [需要减少的字段]
     * @param integer $step [减少的数值]
     * @return integer [受影响行数]
     */
    public function decrease($field, $step = 1)
    {
        if (empty($this->condition['where'])) {
            Error::halt('更新语句必须有where条件');
        }
        $sql = 'UPDATE ' . $this->table . ' SET `' . $field . '`=`' . $field . '`-' . $step . $this->condition['where'];
        return $this->execute($sql, $this->param);
    }
}
