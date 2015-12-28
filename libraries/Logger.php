<?php

/**
 * 服务端日志类，参考phx框架的Log类
 * @link https://github.com/phoenixg/phx
 * 用法：
 * $logger = Logger::getInstance();
 * $logger->debug('debug message');
 * $logger->info('information message');
 * $logger->warn('warn message');
 * $logger->error('error message');
 */
final class Logger
{
    private static $instances = array();
    private $log_path;
    private $log_enable;

    /**
     * @param string $name
     * @return Logger
     */
    public static function getInstance($name = 'app')
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self($name);
        }
        return self::$instances[$name];
    }

    /**
     * forbidden clone instance
     */
    private function __clone()
    {
    }

    /**
     * forbidden new instance
     * @param string $name
     */
    private function __construct($name)
    {
        $path = ROOT_PATH . '/data/logs';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);//允许嵌套创建目录
        }
        $this->log_path = $path . "/" . $name . ".log";
        $this->log_enable = IS_DEVELOP;
    }

    /**
     * write a log
     *
     * @param $type
     * @param $msg
     */
    private function write($type, $msg)
    {
        $msg = $this->format($type, $msg);
        if ($this->log_enable) {
            file_put_contents($this->log_path, $msg, LOCK_EX | FILE_APPEND);
        }
    }

    /**
     * format a log message
     *
     * @param $type
     * @param $msg
     * @return string
     */
    private function format($type, $msg)
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = json_encode($msg);
        }
        return '[' . date('Y-m-d H:i:s') . '] ' . strtoupper($type) . PHP_EOL . $msg . PHP_EOL . PHP_EOL;
    }

    /**
     * debug message
     *
     * @param $msg
     */
    public function debug($msg)
    {
        $this->write("DEBUG", $msg);
    }

    /**
     * information message
     *
     * @param $msg
     */
    public function info($msg)
    {
        $this->write("INFO", $msg);
    }

    /**
     * warn message
     *
     * @param $msg
     */
    public function warn($msg)
    {
        $this->write("WARN", $msg);
    }

    /**
     * error message
     *
     * @param $msg
     */
    public function error($msg)
    {
        $this->write("ERROR", $msg);
    }

}
