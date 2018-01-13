<?php

namespace application\example\validate;

use aaphp\Validate;

/**
 * csrf 表单token验证
 * Class CsrfValidate
 * @package application\example\validate
 */
class CsrfValidate extends Validate
{
    protected $rule = [
        '_token_' => [
            'token',
        ],
    ];

}
