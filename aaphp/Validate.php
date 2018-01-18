<?php

namespace aaphp;

/**
 * 数据验证
 * Class Validate
 * @package aaphp
 */
class Validate
{
    // 当前验证的规则
    protected $rule = [];
    // 验证提示信息
    protected $message = [];
    // 验证提示信息
    protected $data = [];
    // 验证字段描述
    protected $field = [];
    // 验证字段描述
    protected $error = [];
    // 验证规则默认提示信息
    protected $typeMsg = [
        'require' => ':attribute不能为空',
        'number' => ':attribute必须是数字',
        'integer' => ':attribute必须是整数',
        'float' => ':attribute必须是浮点数',
        'email' => ':attribute不符邮箱格式',
        'date' => ':attribute不符合日期格式',
        'url' => ':attribute不是有效的URL地址',
        'ip' => ':attribute不是有效的IP地址',
        'in' => ':attribute必须在 :rule 范围内',
        'notIn' => ':attribute不能在 :rule 范围内',
        'between' => ':attribute只能在 :rule 之间',
        'notBetween' => ':attribute不能在 :rule 之间',
        'length' => ':attribute长度不符必须在 :rule 之间',
        'max' => ':attribute长度不能超过 :rule',
        'min' => ':attribute长度不能小于 :rule',
        'confirm' => ':attribute和确认字段 :rule 不一致',
        'different' => ':attribute和比较字段 :rule 不能相同',
        '>=' => ':attribute必须大于等于 :rule',
        '>' => ':attribute必须大于 :rule',
        '<=' => ':attribute必须小于等于 :rule',
        '<' => ':attribute必须小于 :rule',
        '=' => ':attribute必须等于 :rule',
        'regex' => ':attribute不符正则表达式 :rule',
        'token' => '令牌数据无效',
    ];

    /**
     * 执行数据验证
     * @param array $data [需要验证的数据]
     * @return bool [是否合法]
     */
    public function check($data)
    {
        if (!$this->rule) {
            return true;
        }
        $this->data = $data;
//        默认合法
        $result = true;
        foreach ($this->rule as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if (!in_array('require', $value) && !isset($this->data[$key])) {
//                    不是必须字段不验证
                    break;
                }
                $resultCheckOne = $this->checkOne($key, $key2, $value2);
                if (!$resultCheckOne) {
                    $this->addError($key, $key2, $value2);
//                    不合法
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * 单个验证
     * @param $field [被验证的字段，如：age]
     * @param $key [验证规则，如 between]
     * @param $value [验证的值 如：[18,28]]
     * @return bool [是否合法]
     */
    protected function checkOne($field, $key, $value)
    {
        if (is_integer($key)) {//验证规则只有key 如：'require',
            $attribute = $value;
        } else {//验证规则有key和value 如：'between'=>[18,28],
            $attribute = $key;
        }
        if (!isset($this->data[$field])) {
            return false;
        }
//        被验证的值
        $validateValue = $this->getDataValue($field);
        $result = true;
        switch ($attribute) {
            case 'require':
//                必须
                $requireValue = isset($this->data[$field]) ? $this->data[$field] : '';
                if (!$requireValue && '0' != $requireValue) {
                    $result = false;
                }
                break;
            case 'number':
//                是否是数字
                $result = is_numeric($validateValue);
                break;
            case 'integer':
                // 是否为整型
                $result = filter_var($validateValue, FILTER_VALIDATE_INT);
                break;
            case 'float':
                // 是否为整型
                $result = filter_var($validateValue, FILTER_VALIDATE_FLOAT);
                break;
            case 'email':
                // 是否为邮箱地址
                $result = filter_var($validateValue, FILTER_VALIDATE_EMAIL);
                break;
            case 'date':
                // 是否是一个有效日期
                $result = false !== strtotime($validateValue);
                break;
            case 'url':
                // 是否为一个URL地址
                $result = filter_var($validateValue, FILTER_VALIDATE_URL);
                break;
            case 'ip':
                // 是否为IP地址
                if (!filter_var($validateValue, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) && !filter_var($validateValue, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $result = false;
                }
                break;
            case 'in':
                // 是否在数组内
                $result = in_array($validateValue, $value);
                break;
            case 'notIn':
//                是否不在数组内
                $result = !in_array($validateValue, $value);
                break;
            case 'between':
//                在两个数之间
                list($min, $max) = $value;
                $result = $validateValue >= $min && $validateValue <= $max;
                break;
            case 'notBetween':
//                不在两个数之间
                list($min, $max) = $value;
                $result = $validateValue < $min || $validateValue > $max;
                break;
            case 'length':
//                长度范围
                list($min, $max) = $value;
                $length = mb_strlen((string)$validateValue);
                $result = $length >= $min && $length <= $max;
                break;
            case 'max':
//                是否超过最大值
                $result = mb_strlen((string)$validateValue) <= $value;
                break;
            case 'min':
//                是否小于最小数
                $result = mb_strlen((string)$validateValue) >= $value;
                break;
            case 'confirm':
//                和相比数字是否相等
                $result = $validateValue === $this->getDataValue($value);
                break;
            case 'different':
//                和相比数字是否不相等
                $result = $validateValue !== $this->getDataValue($value);
                break;
            case '>=':
//                是否大于等于
                $result = $validateValue >= $value;
                break;
            case '>':
//                大于
                $result = $validateValue > $value;
                break;
            case '<=':
//                小于等于
                $result = $validateValue <= $value;
                break;
            case '<':
//                小于
                $result = $validateValue < $value;
                break;
            case '=':
//                等于
                $result = $validateValue == $value;
                break;
            case 'token':
//                csrf验证
                $token = isset($_SESSION[$field]) ? $_SESSION[$field] : null;
                $result = $validateValue === $token;
                unset($_SESSION[$field]);
                break;
            case 'regex':
//                正则验证
                $result = 1 === preg_match($value, (string)$validateValue);
                break;
            default:
                break;
        }
        return $result;
    }

    /**
     * 获取需要验证数组里面的 value
     * @param $key [键名]
     * @return string [需要验证数组里面的 value]
     */
    protected function getDataValue($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    /**
     * 获取验证失败后的错误
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 添加错误信息
     * @param $field [被验证的字段，如：age]
     * @param $key [验证规则，如 between]
     * @param $value [验证的值 如：[18,28]]
     */
    protected function addError($field, $key, $value)
    {
        if (is_integer($key)) {//验证规则只有key 如：'require',
            $attribute = $value;
        } else {//验证规则有key和value 如：'between'=>[18,28],
            $attribute = $key;
        }
        if (isset($this->message[$field][$attribute])) {
//            用户自定义错误提示消息
            $this->error[$attribute][] = $this->message[$field][$attribute];
        } else {//系统默认提示消息
            $value = is_array($value) ? implode(',', $value) : $value;
            $message = $this->typeMsg[$attribute];
            $message = str_replace(
                [':attribute', ':rule'],
                [$field, $value],
                $message);
            $this->error[$attribute][] = $message;
        }
    }

    /**
     * 前端表单获取token令牌，csrf防御用
     * @param string $name [令牌名称]
     * @return string [令牌值]
     */
    public static function token($name = '_token_')
    {
        $token = md5($_SERVER['REQUEST_TIME_FLOAT']);
//        保存到session
        $_SESSION[$name] = $token;
        return $token;
    }
}
