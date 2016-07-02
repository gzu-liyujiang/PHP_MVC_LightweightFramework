<?php

/**
 * MySQL策略
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 */

/**
 * MySQL数据库操作类
 */
final class MysqlDb implements IDatabase
{
    private static $instance; //本类实例
    private $conn = null; //数据库连接
    private $count = 0; //查询次数
    private $time = 0; //查询耗时
    private $sql = array(); //已执行的SQL语句

    /**
     * 防止克隆对象以保证单例
     */
    private function __clone()
    {

    }

    /**
     * 私有化构造函数来防止创建实例
     */
    private function __construct()
    {

    }

    /**
     * 获取类的实例
     * 静态方法
     * @return MysqlDb 类的实例
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 连接数据库
     *
     * @see IDatabase::connect($host, $port, $user, $pass, $name)
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $pass
     * @param string $name
     * @return null|resource
     */
    public function connect($host, $port, $user, $pass, $name)
    {
        if (!function_exists('mysql_connect')) {
            exit("服务器不支持MySQL数据库，详情可咨询服务器提供商。");
        }
        if (false == $this->conn) {
            $this->conn = mysql_connect($host . ':' . $port, $user, $pass);
            if ($this->conn) {
                if (!mysql_select_db($name)) {
                    exit('打开数据库失败。' . $this->error());
                } else {
                    //解决中文数据乱码
                    mysql_query('SET NAMES utf8');
                }
            } else {
                exit('数据库服务器连接失败。' . $this->error());
            }
        }
        return $this->conn;
    }

    /**
     * @return null|resource
     */
    public function getConnect()
    {
        return $this->conn;
    }

    /**
     * 执行SQL语句
     * @param string $sql
     * @return resource|boolean
     */
    public function query($sql)
    {
        $this->sql[] = $sql;
        $this->count++;
        $start = microtime(true);
        $result = mysql_query($sql);
        if (!$result) {
            exit('执行SQL语句出错。' . $this->error());
        }
        $end = microtime(true);
        $sec = floatval(sprintf('%.4f', $end - $start));
        $this->time += $sec; //每次查询耗时累加
        return $result;
    }

    /**
     * 将查询结果转为数组，以字段名作为键名
     * @param resource $query
     * @return array
     */
    public function fetchArray($query)
    {
        return (false != $query) ? mysql_fetch_assoc($query) : false;
    }

    /**
     * 统计记录数，失败则返回0
     * @param resource $query
     * @return int
     */
    public function numRows($query)
    {
        return (false != $query) ? mysql_num_rows($query) : 0;
    }

    /**
     * 刚插入的记录编号
     * @return int
     */
    public function insertId()
    {
        return (false != $this->conn) ? mysql_insert_id($this->conn) : 0;
    }

    /**
     * 获取已执行的SQL条数
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * 获取已执行的所有SQL共耗费的秒数
     * @return int
     */
    public function runTime()
    {
        return $this->time;
    }

    /**
     * 获取已执行的每条SQL及其耗时
     * @return string
     */
    public function sql()
    {
        return implode(";\n", $this->sql);
    }

    /**
     * @return string
     */
    public function error()
    {
        if (IS_DEVELOP) {
            return mysql_error($this->conn) . "\n" . end($this->sql);
        }
        return '';
    }

    /**
     * 当前数据库的版本
     * @return string
     */
    public function version()
    {
        return (false != $this->conn) ? mysql_get_server_info($this->conn) : '0.0.0';
    }

    /**
     * 释放查询所占用的资源
     *
     * @param resource $query
     * @return bool|void
     */
    public function free($query)
    {
        return (false != $query) ? mysql_free_result($query) : false;
    }

    /**
     * 析构函数，关闭数据连接
     */
    public function __destruct()
    {
        if (false != $this->conn) {
            mysql_close($this->conn);
        }
    }

}

?>