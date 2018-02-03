<?php

namespace application\example\model;

use aaphp\Model;

/**
 * 用户模型
 * Class User
 * @package application\example\model
 */
class User extends Model
{
//    定义表名，前缀须定义在配置文件
    protected $table = 'user';
}
