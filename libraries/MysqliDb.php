<?php
/**
 * MySQLi策略
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 */


/**
 * MySQLi数据库操作类
 */
final class MysqliDb implements IDatabase
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
     * @return MysqliDb 类的实例
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
     * @param string $host 数据库服务器
     * @param int $port 数据库服务器端口
     * @param string $user 数据库用户
     * @param string $pass 数据库用户登录密码
     * @param string $db 数据库名称
     * @return boolean
     */
    public function connect($host, $port, $user, $pass, $db)
    {
        if (!function_exists('mysqli_connect')) {
            exit("服务器不支持MySQLi数据库扩展，详情可咨询服务器提供商。");
        }
        if (false == $this->conn) {
            $this->conn = mysqli_connect($host, $user, $pass, $db, $port);
            if ($this->conn) {
                //解决中文数据乱码
                mysqli_query($this->conn, 'SET NAMES utf8');
            } else {
                //未连接上，故不用“mysqli_error()”
                exit('数据库服务器连接失败。' . (IS_DEVELOP ? mysqli_connect_error() : ''));
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
     * @return mysqli_result|boolean
     */
    public function query($sql)
    {
        $this->sql[] = $sql;
        $this->count++;
        $start = microtime(true);
        $result = mysqli_query($this->conn, $sql);
        if (!$result) {
            exit('执行SQL语句出错。' . $this->error());
        }
        $end = microtime(true);
        $sec = floatval(sprintf('%.4f', $end - $start));
        $this->time += $sec; //每次查询耗时累加
        return $result;
    }

    /**
     *将查询结果转为数组，以字段名作为键名
     *
     * @param mysqli_result $query
     * @return array|boolean
     */
    public function fetchArray($query)
    {
        if ($query == false) {
            return false;
        }
        $res = mysqli_fetch_assoc($query);
        if ($res === null) {
            return false;
        }
        return $res;
    }

    /**
     *统计记录数，失败则返回0
     * @param mysqli_result $query
     * @return int
     */
    public function numRows($query)
    {
        return (false != $query) ? mysqli_num_rows($query) : 0;
    }

    /**
     *刚插入的记录编号
     * @return int
     */
    public function insertId()
    {
        return (false != $this->conn) ? mysqli_insert_id($this->conn) : 0;
    }

    /**
     *获取已执行的SQL条数
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
     * 获取已执行的每条SQL
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
            return mysqli_error($this->conn) . '\n' . end($this->sql);
        }
        return '';
    }

    /**
     * 当前数据库的版本
     * @return string
     */
    public function version()
    {
        return (false != $this->conn) ? mysqli_get_server_info($this->conn) : '0.0.0';
    }

    /**
     * 释放查询所占用的资源
     *
     * @param mysqli_result $query
     * @return void
     */
    public function free($query)
    {
        mysqli_free_result($query);
    }

    /**
     * 析构函数，关闭数据连接
     */
    public function __destruct()
    {
        if (false != $this->conn) {
            mysqli_close($this->conn);
        }
    }

}

?>