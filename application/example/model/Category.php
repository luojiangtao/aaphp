<?php

namespace application\example\model;

use aaphp\Model;

/**
 * 分类模型
 * Class Category
 * @package application\example\model
 */
class Category extends Model
{
//    定义表名，前缀须定义在配置文件
    protected $table = 'category';
}
