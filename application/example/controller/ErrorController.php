<?php
namespace application\example\controller;

use aaphp\Controller;
use aaphp\Error;

/**
 * 错误例子
 * Class ErrorController
 * @package application\example\controller
 */
class ErrorController extends Controller
{

    public function index()
    {
        Error::halt('error');
    }
}

?>
