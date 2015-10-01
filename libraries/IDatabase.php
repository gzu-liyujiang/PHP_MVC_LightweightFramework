<?php
/**
 * 数据库策略所遵循的公共接口
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 */

/**
 * 操作数据库的接口
 */
interface IDatabase
{

    /**
     * 获取类的实例
     * 静态方法
     * @return IDatabase 类的实例
     */
    public static function getInstance();

    /**
     * 连接数据库
     * @param string $host 数据库服务器
     * @param int $port 数据库服务器端口
     * @param string $user 数据库用户
     * @param string $pass 数据库用户登录密码
     * @param string $name 数据库名称
     * @return null|resource 数据库连接
     */
    public function connect($host, $port, $user, $pass, $name);

    /**
     * @return null|resource
     */
    public function getConnect();

    /**
     * 执行SQL语句
     * @param string $sql
     * @return resource
     */
    public function query($sql);

    /**
     * 根据查询结果返回数组
     * @param resource $query
     * @return array
     */
    public function fetchArray($query);

    /**
     * 根据查询结果返回记录数
     * @param resource $query
     * @return int
     */
    public function numRows($query);

    /**
     * 获取刚刚插入的记录的编号
     * @return int
     */
    public function insertId();

    /**
     * 返回已执行的SQL语句数
     * @return int
     */
    public function count();

    /**
     * 返回执行SQL语句共耗费的秒数
     * @return int
     */
    public function runTime();

    /**
     * 返回已执行的每条SQL语句及其耗费的秒数
     * @return string
     */
    public function sql();

    /**
     * @return string
     */
    public function error();

    /**
     * 数据库版本
     * @return string
     */
    public function version();

    /**
     * 释放查询所占用的资源
     * @param resource $query
     * @return void
     */
    public function free($query);

}

?>