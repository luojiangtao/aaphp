<?php

namespace application\example\validate;

use aaphp\Validate;

/**
 * 数据验证例子
 * Class UserValidate
 * @package application\example\validate
 */
class UserValidate extends Validate
{
    /**
     * @var array [过滤规则]
     */
    protected $rule = [
        'username' => [
//            不能为空
            'require',
//            长度必须大于25
            'max' => 25,
//            长度必须小于2
            'min' => 2,
//            长度必须 在 5-10 之间
            'length' => [5, 10],
        ],
        'email' => [
//            必须是邮箱格式
            'email'
        ],
        'password' => [
//            必须和 repassword 相等
            'confirm' => 'repassword',
//            不能和 username 相等
            'different' => 'username',
//            正则表达式验证
            'regex' => '/^\d{6}$/',
        ],
        'age' => [
//            必须是整数
            'integer',
//            必须是数字
            'number',
//            必须在 [18, 28] 之间
            'between' => [18, 28],
//            不能在 [30, 50] 之间
            'notBetween' => [30, 50],
//            必须是 [18, 19, 20, 21, 22] 其中之一
            'in' => [18, 19, 20, 21, 22],
//            不能是 [28, 29, 30, 31, 32] 其中之一
            'notIn' => [28, 29, 30, 31, 32],
//            必须等于18
            '=' => 18,
//            必须大于18
            '>' => 18,
//            必须小于18
            '<' => 18,
//            必须大于等于18
            '>=' => 18,
//            必须小于等于18
            '<=' => 18,
        ],
        'money' => [
//            必须是 浮点型
            'float'
        ],
        'birthday' => [
//            必须是日期类型
            'date'
        ],
        'blog' => [
//            必须是url类型
            'url'
        ],
        'blogIp' => [
//            必须是ip 类型
            'ip',
        ],
//        'token' =>  [
//            'token',
//        ],
    ];

    /**
     * @var array [自定义错误信息]
     */
    protected $message = [
        'username' => [
            'require' => '名称不能为空',
            'max' => '名称最多不能超过25个字符',
            'min' => '名称最少不能小于2个字符',
            'length' => '名称最长度范围：5-10',
        ],
        'age' => [
            'integer' => '年龄必须是数字1',
            'between' => '年龄只能在1-120之间1',
        ],
        'email' => [
            'email' => '邮箱格式错误',
        ],
        'password' => [
            'confirm' => '密码和确认密码不一致',
            'different' => '用户名和密码不能相等',
            'regex' => '密码不符合要求',
        ],
    ];
}
