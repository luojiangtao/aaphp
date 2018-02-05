<?php

namespace application\example\controller;

use aaphp\Controller;
use aaphp\Request;
use application\example\validate\CsrfValidate;
use application\example\validate\UserValidate;

/**
 * 数据验证例子
 * Class ValidateController
 * @package application\example\controller
 */
class ValidateController extends Controller
{
    /**
     * 数据验证
     */
    public function check()
    {
        $data = [
            'usernamea' => 'aaphp',
            'password' => '123456',
            'age' => '8',
            'email' => '123456@qq.com',
            'repassword' => '123456',
            'money' => '100.1',
            'birthday' => '2010-8-8',
            'blog' => 'http://luojiangtao.com',
            'blogIp' => 'FF01::101',
            '_token_' => '1',
        ];
        $validate = new UserValidate();
        if ($validate->check($data)) {
            echo '验证通过';
        } else {
            echo '验证未通过，错误信息：<br/>';
            var_dump($validate->getError());
        }
    }

    /**
     * csrf 跨域表单验证
     * @return string
     */
    public function csrf()
    {
        $request = Request::instance();
        if (!$request->isPost()) {
            return $this->fetch();
        }

        $data = [
            'usernamea' => $request->post('usernamea'),
            'password' => $request->post('password'),
//            隐藏的token值
            '_token_' => $request->post('_token_'),
        ];

        $validate = new CsrfValidate();
        if ($validate->check($data)) {
            echo '验证通过<br/>';
        } else {
            echo '验证未通过，错误信息：<br/>';
            var_dump($validate->getError());
        }
    }

}