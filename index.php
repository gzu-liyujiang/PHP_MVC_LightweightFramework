<?php
/**
 * 入口。
 */

header("Content-Type:text/html;charset=utf-8");
if (PHP_VERSION < 5.3) {
    //计划使用命名空间，届时只支持php5.3+
    //5.3+新特性：namespace、callStatic、__DIR__等
    //exit('本程序需运行在PHP5.3+的环境上，当前的PHP版本为：' . PHP_VERSION);
}

define('IS_DEVELOP', true); // 是否处于开发模式
define("START_TIME", microtime()); // 用于计算页面耗时
define("ROOT_PATH", str_replace('\\', '/', dirname(__FILE__))); // 本程序根目录

//是否允许显示报错信息
if (IS_DEVELOP) {
    error_reporting(E_ALL);
    if (function_exists('ini_set')) {
        ini_set('display_errors', 'On');
    }
} else {
    error_reporting(0);
    if (function_exists('ini_set')) {
        ini_set('display_errors', 'Off');
    }
}

//加载Flight框架
require ROOT_PATH . "/libraries/Flight.php";

//自动加载这些路径下的类
function __autoload($class)
{
    $class_dirs = array(
        ROOT_PATH . "/libraries",
        ROOT_PATH . "/controllers",
        ROOT_PATH . "/controllers/front",
        ROOT_PATH . "/controllers/backend",
        ROOT_PATH . "/controllers/api",
        ROOT_PATH . "/models"
    );
    $class_file = str_replace(array('\\', '_'), '/', $class) . '.php';
    //含命名空间
    foreach ($class_dirs as $dir) {
        $file = $dir . '/' . $class_file;
        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            require $file;
            break;
        }
    }
}

//保存一些全局变量
Flight::getInstance()->set(@include(ROOT_PATH . '/config.php'));

//启动框架，执行“Helper::initial()”进行初始化
Flight::getInstance()->start(array("Helper", "initial"));
?>
