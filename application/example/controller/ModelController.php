<?php

namespace application\example\controller;

use aaphp\Controller;
use aaphp\Model;
use application\example\model\User;

/**
 * 模型例子
 * Class ModelController
 * @package application\example\controller
 */
class ModelController extends Controller
{
    /**
     * 插入
     */
    public function insert()
    {
        $data = ['username' => 'aaphp', 'password' => '123456', 'age' => 18];
        $model = new User();
//        $model= new Model('user');//等价于上面
        $id = $model->insert($data);
        echo '自增id：' . $id . '<br/>';
    }

    /**
     * 更新
     */
    public function update()
    {
        $data = ['username' => 'mysql', 'password' => '654321', 'age' => 20];
        $model = new User();
        $rowCount = $model->where(['id', '=', 1])->update($data);
        var_dump($model->getSql());
        echo '更新影响行数：' . $rowCount . '<br/>';
    }

    /**
     * 查询
     */
    public function select()
    {
        $model = new User();
        $result = $model->select();
        echo '查询所有用户：<br/>';
        var_dump($result);
    }

    /**
     * 查询结果条数限制
     */
    public function limit()
    {
        $model = new User();
        $result = $model
            ->limit(10)
//            执行查询
            ->select();
        echo "查询不超过10条：<br/>";
        var_dump($result);
    }

    /**
     * 查询字段
     */
    public function field()
    {
        $model = new User();
        $result = $model
//            只查询 'id','username','age','city' 字段
            ->field(['id', 'username', 'age', 'city'])
//            执行查询
            ->select();
        echo "只查询 'id','username','age','city' 字段：<br/>";
        var_dump($result);
    }

    /**
     * 条件查询
     */
    public function where()
    {
        $model = new User();
        $result = $model->where(['id', '=', 1])->select();
        echo '查询id=1的用户：<br/>';
        var_dump($result);

        $model = new User();
//        注意，是二维数组 and 查询
        $where = [
//            id在[1,2,3,4,5,6,7,8,9,10]内
            ['id', 'in', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
//            id不在[9,34]
            ['id', 'not in', [9, 34]],
//            age在[1,28]之间，包含
            ['age', 'between', [1, 28]],
//            age不在[20,100]之间，包含
            ['age', 'not between', [20, 100]],
//            city 是 NULL
            ['city', 'is', 'null'],
//            city 不是 NULL
            ['city', 'is not', 'null'],
//            id = 1
            ['id', '=', 1],
//            username!=aaphp
            ['username', '!=', 'aaphp'],
//            id<=10
            ['id', '<=', 10],
//            id>=30
            ['id', '>=', 30],
//            username like %aaphp5%
            ['username', 'like', '%aaphp5%'],
//            username not like %aaphp%
            ['username', 'not like', '%aaphp%'],
//            id>1
            ['id', '>', 1],
//            id<10
            ['id', '<', 10],
        ];
        $result = $model
//            查询条件
            ->where($where)
//            执行查询
            ->select();
        echo "多条件查询：<br/>";
        var_dump($result);
    }

    /**
     * 排序
     */
    public function order()
    {
        $model = new User();
        $result = $model
//            id 降序排序
            ->order('id DESC')
//            id 增序排序
//            ->order('id ASC')
//            执行查询
            ->select();
        echo "按id降序排序：<br/>";
        var_dump($result);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $model = new User();
        $rowCount = $model->where(['id', '>', 35])->delete();
        echo "删除id大于35的用户：<br/>";
        var_dump($rowCount);
    }

    /**
     * 分组
     */
    public function group()
    {
        $model = new User();
        $result = $model->group('age')->select();
        echo "查询用户，按年龄分组：<br/>";
        var_dump($result);
    }

    /**
     * 统计条数
     */
    public function count()
    {
        $model = new User();
        $result = $model->where(['id', '<', 10])->count();
        echo "统计id小于10的用户数：{$result}<br/>";
    }

    /**
     * 求平均数
     */
    public function avg()
    {
        $model = new User();
        $result = $model->where(['id', '<', 10])->avg('age');
        echo "id小于10的用户的平均年龄：<br/>";
        var_dump($result);
    }

    /**
     * 求最大值
     */
    public function max()
    {
        $model = new User();
        $result = $model->where(['id', '<', 10])->max('age');
        echo "id小于10的用户的最大年龄：<br/>";
        var_dump($result);
    }

    /**
     * 求最小值
     */
    public function min()
    {
        $model = new User();
        $result = $model->where(['id', '<', 10])->min('age');
        echo "id小于10的用户的最小年龄：<br/>";
        var_dump($result);
    }

    /**
     * 修改单个字段
     */
    public function setField()
    {
        $model = new User();
        $result = $model->where(['id', '=', 10])->setField('username', 'lucy');
        echo "修改id等于10的用户名称为lucy：<br/>";
        var_dump($result);
    }

    /**
     * 获取单个字段
     */
    public function getField()
    {
        $model = new User();
        $result = $model->where(['id', '=', 10])->getField('username');
        echo "获取id等于10的用户名称：{$result}<br/>";
    }

    /**
     * 查询一条数据
     */
    public function selectOne()
    {
        $model = new User();
        $result = $model->where(['id', '=', 10])->selectOne();
        echo "获取id等于10的用户：<br/>";
        var_dump($result);
    }

    /**
     * 字段数字增加
     */
    public function increase()
    {
        $model = new User();
        $result = $model->where(['id', '=', 10])->increase('age');
        echo "将id等于10的用户年龄加1：<br/>";
        var_dump($result);

        $result = $model->where(['id', '=', 10])->increase('age', 5);
        echo "将id等于10的用户年龄加5：<br/>";
        var_dump($result);
    }

    /**
     * 字段数字减少
     */
    public function decrease()
    {
        $model = new User();
        $result = $model->where(['id', '=', 10])->decrease('age');
        echo "将id等于10的用户年龄减1：<br/>";
        var_dump($result);

        $result = $model->where(['id', '=', 10])->decrease('age', 5);
        echo "将id等于10的用户年龄减5：<br/>";
        var_dump($result);
    }

    /**
     * 链式操作
     */
    public function chain()
    {
        $model = new User();
        $result = $model->where(['id', '<', 10])->field(['id', 'username', 'age', 'city'])->order('id DESC')->limit(5)->select();
        echo "查询id<10的用户的'id', 'username', 'age', 'city',按id降序排序，最多5条：<br/>";
        var_dump($result);
    }

    /**
     * 分页
     */
    public function page()
    {
        $model = new User();
//        查询条数
        $limit = 5;
//        偏移量
        $offset = 2;
        $limit = "{$offset},{$limit}";
        $result = $model->limit($limit)->select();
        echo "查询第3-7行：<br/>";
        var_dump($result);
    }

    /**
     * 获取执行过的sql语句
     */
    public function getSql()
    {
        $model = new User();
        $model->where(['id', '<', 10])->field(['id', 'username', 'age', 'city'])->order('id DESC')->limit(5)->select();
        $sqlArray = $model->getSql();
        echo "获取执行过的sql语句：<br/>";
        var_dump($sqlArray);
    }
}