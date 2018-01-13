<?php

namespace aaphp;

use aaphp\Request;
use aaphp\Error;

/**
 * 视图，仿Smarty模版引擎
 * Class View
 * @package aaphp
 */
class View
{
    // 存放assign分配的变量
    public static $array = array();
    // 模版文件目录
//    public static $view_dir = APP_VIEW_PATH;
//    // 编译文件目录
//    public static $compile_dir = COMPILE_PATH;
//    // 缓存文件目录
//    public static $cache_dir = CACHE_PATH;
    // 是否开启缓存
    // public $caching = true;
    public static $caching = false;
    // public $caching = C('is_cache');

    /**
     * 分配变量到模版页面
     * @param string $key [变量的名称]
     * @param string $value [变量的值]
     */
    public static function assign($key, $value)
    {
        // 变量保存到数组中
        View::$array["$key"] = $value;
    }

    /**
     * 载入模版
     * @param string $file [模版文件名]
     * @return string [视图html字符串]
     */
    public static function fetch($file = null)
    {
        // 模版文件路径
        $request = Request::instance();
        $mudule = $request->getMudule();
        $controller = $request->getController();
        $action = $request->getAction();

        if (!is_null($file)) {
            if (strstr($file, '/')) {
                // 如果包含/，则拆分  View::$display("index/index");
                $temp = explode('/', $file);
                $controller = $temp[0];
                $action = $temp[1];
            } else {
                // 默认.html
                $action = $file;
            }
        }

        if (IS_CLI) {//命令行模型运行
            // 默认是当前控制器下当前方法的名称
            $viewPath = ROOT_PATH . '/console/view/' . $controller . '/' . $action . '.html';
        } else {//浏览器模型运行
            $moduelStatus = Config::common('moduel_status');
            if ($moduelStatus) {//开启分组模式
                // 默认是当前控制器下当前方法的名称
                $viewPath = APPLICATION_PATH . '/' . $mudule . '/view/' . $controller . '/' . $action . '.html';
            } else {//关闭分组模式
                // 默认是当前控制器下当前方法的名称
                $viewPath = APPLICATION_PATH . '/view/' . $controller . '/' . $action . '.html';
            }
        }

        if (!is_file($viewPath)) {
            var_dump($viewPath);
            Error::halt('模版文件不存在：' . $viewPath);
        }

        // 将assign分配的变量，保存在数组中的转化为变量
        extract(View::$array);

        // 编译文件路径
        $compile_file = COMPILE_PATH . '/' . md5($viewPath) . '.html';
        //只有当编译文件不存在或者是模板文件被修改过了才重新编译文件
        if (!file_exists($compile_file) || filemtime($compile_file) < filemtime($viewPath)) {
            // 没有文件夹则创建
            is_dir(COMPILE_PATH) || mkdir(COMPILE_PATH, 0777, true);
            // 获取模版文件
            $html = file_get_contents($viewPath);
            // 替换所有模版标签，包括{$value} <foreach> <if><elseif></if> 为PHP代码
            $html = View::replaceAll($html);
            // 保存缓存文件
            file_put_contents($compile_file, $html);
        }

        //开启了缓存才加载缓存文件，否则直接加载编译文件
        if (View::$caching) {
            // 编译文件路径
            $cache_file = COMPILE_PATH . '/' . md5($file) . '.html';
            //只有当缓存文件不存在，或者编译文件已被修改过,则重新生成缓存文件
            if (!file_exists($cache_file) || filemtime($cache_file) < filemtime($compile_file)) {
                // 载入编译文件并执行
                include $compile_file;
                // 执行$compile_file编译文件后，内容输出到缓存区，不会从输出到屏幕。
                $content = ob_get_clean();
                // 保存缓存文件
                if (!file_put_contents($cache_file, $content)) {
                    die('保存缓存文件出错，请检查缓存文件夹写权限');
                }
            }
            // 开启缓存，引入缓存文件,并执行
            ob_start();
            ob_implicit_flush(false);
            include $cache_file;
        } else {
            // 没开启缓存，引入编译文件,并执行
            ob_start();
            ob_implicit_flush(false);
            include $compile_file;
        }

        // 执行文件后，内容输出到缓存区，不会从输出到屏幕。
//        ob_start();
        $content = ob_get_clean();
        return $content;
    }

    /**
     * xss 过滤
     * @param $value
     * @return string
     */
    public static function xssFilter($value)
    {
        if(Config::common('xss_filter')){
            $value = htmlspecialchars($value);
        }
        return $value;
    }

    /*-------------------------以下为解析方法--------------------------------*/

    /**
     * 替换全部模版标签为PHP代码
     * @param string $html [待替换的html字符串]
     * @return string [替换后的视图html字符串]
     */
    private static function replaceAll($html)
    {
        // 替换include标签为引入文件的类容
        $html = View::replaceInclude($html);
        // 替换普通变量标签为PHP代码
        $html = View::replaceValue($html);
        // 替换if标签为PHP代码
        $html = View::replaceIf($html);
        // 替换elseif标签为PHP代码
        $html = View::replaceElseif($html);
        // 替换else标签为PHP代码
        $html = View::replaceElse($html);
        // 替换endif标签为PHP代码
        $html = View::replaceEndif($html);
        // 替换foreach 循环标签为PHP代码
        $html = View::replaceForeach($html);
        // 替换endforeach 循环标签为PHP代码
        $html = View::replaceEndforeach($html);
        return $html;
    }

    /**
     * 替换普通变量标签为PHP代码
     * 格式：{$name} -> <?php echo $name ?>
     * @param string $html [待替换的html字符串]
     * @return string [替换后的html]
     */
    private static function replaceValue($html)
    {
        // 普通变量 {$name}
        $preg = '/\{\$(.+?)\}/';
        $rep = '<?php echo isset($$1) ? aaphp\View::xssFilter($$1) : \'\'; ?>';
        // 标签替换，替换所有 {} 包含的内容
        $html = preg_replace($preg, $rep, $html);

        // 使用函数 {:U('index/index')}
        $preg = '/\{:(.+?)\}/';
        $rep = '<?php echo $1; ?>';
        // 标签替换，替换所有 {} 包含的内容
        $html = preg_replace($preg, $rep, $html);

        // 替换常量 __ROOT__
        $preg = '/__(.+?)__/';
        $rep = '<?php echo __$1__; ?>';
        // 标签替换，替换所有 {} 包含的内容
        $html = preg_replace($preg, $rep, $html);

        return $html;
    }

    /**
     * 替换foreach 循环标签为PHP代码
     * 格式：<foreach name='person' item='v' key='k'>  ->  <?php if(is_array($person)):  foreach($person as $k=>$v): ?>
     * @param string $html [待替换的html字符串]
     * @return string [替换后的html]
     */
    private static function replaceForeach($html)
    {
        // 找出判断条件
        $preg = '/<foreach.+?name=(\'|\")(.+?)(\'|\").+?>/';
        preg_match_all($preg, $html, $matches);
        $count = count($matches[0]);
        // 统计匹配到的次数并循环单次替换，防止第一个匹配的值把后面的覆盖了
        while ($count) {
            $preg = '/<foreach.+?name=(\'|\")(.+?)(\'|\").+?>/';
            preg_match($preg, $html, $match);
            $name = empty($match[2]) ? '' : $match[2];

            // 找出键名 默认 k
            $preg = '/<foreach.+?key=(\'|\")(.+?)(\'|\").+?>/';
            preg_match($preg, $html, $match);
            $key = empty($match[2]) ? 'k' : $match[2];

            // 找出值名 默认 v
            $preg = '/<foreach.+?item=(\'|\")(.+?)(\'|\").+?>/';
            preg_match($preg, $html, $match);
            $item = empty($match[2]) ? 'v' : $match[2];

            $preg = '/<foreach(.+?)>/';
            // 标签替换
            $rep = '<?php if(is_array($' . $name . ')):  foreach($' . $name . ' as $' . $key . '=>$' . $item . '): ?>';
            $html = preg_replace($preg, $rep, $html, 1);
            $count--;
        }
        return $html;
    }

    /**
     * 替换endforeach 循环标签为PHP代码
     * 格式：</foreach>  ->  <?php endforeach; endif; ?>
     * @param string $html [待替换的html字符串]
     * @return string [替换后的html]
     */
    private static function replaceEndforeach($html)
    {
        $preg = '/<\/foreach>/';
        $rep = '<?php endforeach; endif; ?>';
        // 标签替换
        $html = preg_replace($preg, $rep, $html);
        return $html;
    }

    /**
     * 替换if标签为PHP代码
     * 格式：<if condition="$person[0]['name']=='taotao'">  -> <?php if($person[0]['name']=='taotao'): ?>
     * @param string $html [待替换的html字符串]
     * @return string [替换后的html]
     */
    private static function replaceIf($html)
    {
        // 找出判断条件
        $preg = '/<if condition=(\'|\")(.+?)(\'|\")>/';
        preg_match_all($preg, $html, $matches);
        $count = count($matches[0]);
        // 统计匹配到的次数并循环单次替换，防止第一个匹配的值把后面的覆盖了
        while ($count) {
            preg_match($preg, $html, $match);
            $condition = empty($match[2]) ? '' : $match[2];
            $rep = '<?php if(' . $condition . '): ?>';
            // 标签替换
            $html = preg_replace($preg, $rep, $html, 1);
            $count--;
        }
        return $html;
    }

    /**
     * 替换elseif标签为PHP代码
     * 格式：<elseif condition="$person[0]['name']=='taotao2'"/> -> <?php elseif($person[0]['name']=='taotao2'): ?>
     * @param string $html [待替换的html字符串]
     * @return string [替换后的html]
     */
    private static function replaceElseif($html)
    {
        // 找出判断条件
        $preg = '/<elseif condition=(\'|\")(.+?)(\'|\")\s?\/>/';
        preg_match_all($preg, $html, $matches);
        $count = count($matches[0]);
        // 统计匹配到的次数并循环单次替换，防止第一个匹配的值把后面的覆盖了
        while ($count) {
            preg_match($preg, $html, $match);
            $condition = empty($match[2]) ? '' : $match[2];
            $rep = '<?php elseif(' . $condition . '): ?>';
            // 标签替换
            $html = preg_replace($preg, $rep, $html, 1);
            $count--;
        }
        return $html;
    }

    /**
     * 替换else标签为PHP代码
     * 格式：<else /> -> <?php else: ?>
     * @param string $html [待替换的html字符串]
     * @return string [替换后的html]
     */
    private static function replaceElse($html)
    {
        $preg = '/<else\s?\/>/';
        $rep = '<?php else: ?>';
        // 标签替换
        $html = preg_replace($preg, $rep, $html);
        return $html;
    }

    /**
     * 替换endif标签为PHP代码
     * 格式：</if> -> <?php endif; ?>
     * @param string $html [待替换的html字符串]
     * @return string [替换后的html]
     */
    private static function replaceEndif($html)
    {
        $preg = '/<\/if>/';
        $rep = '<?php endif; ?>';
        // 标签替换
        $html = preg_replace($preg, $rep, $html);
        return $html;
    }

    /**
     * 替换include标签为被引入文件的内容
     * @param string $html [待替换的html字符串]
     * @return string [替换后的html]
     */
    private static function replaceInclude($html)
    {
//        找到被引入的文件名
        $preg = '/<include\s{1}file=(\'|\")(.+?)(\'|\")\s?\/>/';
        preg_match_all($preg, $html, $matches);
        $count = count($matches[0]);

//        统计匹配到的次数并循环单次替换，防止第一个匹配的值把后面的覆盖了
        while ($count) {
            preg_match($preg, $html, $match);
            $include = empty($match[2]) ? '' : $match[2];
            if (!empty($include)) {
//                $suffix = strrchr($include, '.');
//                // 默认.html
//                $include      = empty($suffix) ? $include . '.html' : $include;
//
//                $request=Request::instance();
//                $mudule=$request->getMudule();
//                $controller=$request->getController();
////                $action=$request->getAction();
//
////        只包含一个 / 表示 只有方法名
//                if(0 == substr_count($include,'/')){
//                    $include = $controller .'/'.$include;
//                }
//                if(1 == substr_count($include,'/')){
//                    $include = $mudule .'/'.$include;
//                }
//
//                $include      = APPLICATION_PATH . '/'. $mudule . '/view/' . $controller . '/'  . $include;
                $include_file = file_get_contents($include);
                echo $include_file;
                // 标签替换
                $html = preg_replace($preg, $include_file, $html, 1);
            }
            $count--;
        }
        return $html;
    }
}
