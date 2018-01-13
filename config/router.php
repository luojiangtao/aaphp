<?php
/**
 * 路由配置
 */
return [
//    后台
    'a'=>'admin/Index/index',
    '/^c\/(\d+?)$/' =>'example/RouterController/test2/id/$1',
    // 文章详情，以数字为结尾 http://localhost/blog/index.php/17.html
    '/^(\d+?)$/' =>'index/Article/articleDetail/article_id/$1',
    // 分类列表，
    '/^list\/(\d+?)$/' =>'index/Article/articleList/category_id/$1',

//    路由例子
    'router'=>'example/RouterController/index',
    '/^get\/(\d+?)$/'=>'example/RouterController/get/id/$1',
];
