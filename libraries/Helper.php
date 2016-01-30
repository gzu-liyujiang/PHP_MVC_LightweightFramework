<?php

/**
 * 一些有用的方法
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 */
final class Helper
{

    public static function initial()
    {
        self::setTimezone("Asia/Shanghai");
        self::forbiddenAutoSlashes();
        self::routeDispense();
    }

    /**
     * 路由分发
     */
    public static function routeDispense()
    {
        //此处用于支持URL重写
        if (isset($_SERVER['PATH_INFO'])) {
            //获取pathinfo
            Logger::getInstance()->debug('path info>>> ' . $_SERVER['PATH_INFO']);
            $pathinfo = explode('/', trim($_SERVER['PATH_INFO'], '/'));
            //获取control
            $_GET['c'] = (!empty($pathinfo[0]) ? $pathinfo[0] : 'Home');
            array_shift($pathinfo); //将数组开头的单元移出数组
            //获取action
            $_GET['a'] = (!empty($pathinfo[0]) ? $pathinfo[0] : 'main');
            array_shift($pathinfo); //再将将数组开头的单元移出数组
            for ($i = 0; $i < count($pathinfo); $i += 2) {
                $_GET[$pathinfo[$i]] = $pathinfo[$i + 1];
            }
        }
        
        //识别真正的URL
        if (isset($_GET['c'])) {
            //请求的控制器
            $controller = ucwords(trim($_GET['c'])) . "Controller";
        } else {
            //默认控制器
            $currentUrl = self::currentUrl();
            Logger::getInstance()->debug('current url>>> ' . $currentUrl);
            if (stripos($currentUrl, "admin.php") > 0) {
                $controller = "AdminController";
            } else if (stripos($currentUrl, "api.php") > 0) {
                $controller = "ApiController";
            } else {
                $controller = "HomeController";
            }
        }
        if ($controller === 'Controller') {
            $controller = 'HomeController';
        }
        $class = $controller;
        $method = isset($_GET['a']) ? trim($_GET['a']) : "main";
        if (empty($method)) {
            $method = 'main';
        }
        if (class_exists($class) && method_exists($class, $method)) {
            call_user_func(array(new $class(), $method));
        } else {
            exit('the class or method is not found: ' . $class . '->' . $method . '()');
        }
    }

    /**
     * 设置服务器的默认时区，以便正常显示本地时间
     *
     * @param string $timezone 时区值请参考PHP官方手册(www.php.net)，如:PRC
     */
    private static function setTimezone($timezone)
    {
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($timezone);
        } else if (function_exists('ini_set')) {
            ini_set("date.timezone", "'{
                $timezone}'");
        }
    }

    /**
     * 禁止服务器自动转义字符
     */
    private static function forbiddenAutoSlashes()
    {
        //来自GPC的字符不自动转义
        /** @noinspection PhpDeprecationInspection */
        if (get_magic_quotes_gpc() == 1) {
            $_GET = self::stripSlashesDeep($_GET);
            $_POST = self::stripSlashesDeep($_POST);
            $_COOKIE = self::stripSlashesDeep($_COOKIE);
            $_REQUEST = self::stripSlashesDeep($_REQUEST);
        }
    }

    /**
     * 递归去除转义字符
     * @param $value
     * @return array|string
     */
    private static function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map(array(__CLASS__, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    /**
     * @return string
     */
    public static function currentUrl()
    {
        $server = $_SERVER['SERVER_NAME'];
        $port = $_SERVER["SERVER_PORT"];
        $uri = $_SERVER["REQUEST_URI"];
        return 'http://' . $server . ':' . $port . $uri;
    }

    /**
     * @return string
     */
    public
    static function runTime()
    {
        if (!defined("START_TIME")) {
            Logger::getInstance()->warn("START_TIME undefined, can't get the run time");
            return "";
        }
        $startTime = explode(" ", START_TIME);
        $endTime = explode(" ", microtime());
        return sprintf("%0.4f", round($endTime[0] + $endTime[1] - $startTime[0] - $startTime[1], 4));
    }

    /**
     * 格式化时间戳
     * @param int $timestamp 时间戳，比如通过“time()”获取
     * @return string 类似于“3天前、10秒前”
     */
    public static function formatTimestamp($timestamp)
    {
        $str = '';
        //获取当前的时间戳，据说用$_SERVER['REQUEST_TIME']比time()高效
        $now = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
        $time = abs($now - $timestamp);
        if ($time <= 60)
            $str .= '刚刚';
        elseif ($time <= (60 * 60))
            $str .= floor($time / 60) . '分钟';
        elseif ($time <= (60 * 60 * 24))
            $str .= floor($time / (60 * 60)) . '小时';
        elseif ($time <= (60 * 60 * 24 * 30))
            $str .= floor($time / (60 * 60 * 24)) . '天';
        elseif ($time <= (60 * 60 * 24 * 30 * 12))
            $str .= floor($time / (60 * 60 * 24 * 30)) . '个月';
        else
            $str .= floor($time / (60 * 60 * 24 * 30 * 12)) . '年';
        if ($time > 60)
            $str .= ($timestamp > $now) ? '后' : '前';
        return $str;
    }

}
