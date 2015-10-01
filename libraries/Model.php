<?php
/**
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 */

/**
 * 模型
 */
class Model
{
    /**
     * @var IDatabase 只允许有单个实例
     */
    protected static $db = null;

    /**
     * 初始化数据库操作对象
     */
    public function __construct()
    {
        if (self::$db == null) {
            $db_type = Flight::getInstance()->get("db.type");
            if (strtolower($db_type) == 'mysqli') {
                self::$db = MysqliDb::getInstance();
            } else {
                self::$db = MysqlDb::getInstance();
            }
        }
        if (self::$db->getConnect() == null) {
            $db_host = Flight::getInstance()->get("db.host");
            $db_port = intval(Flight::getInstance()->get("db.port"));
            $db_user = Flight::getInstance()->get("db.user");
            $db_pass = Flight::getInstance()->get("db.pass");
            $db_name = Flight::getInstance()->get("db.name");
            self::$db->connect($db_host, $db_port, $db_user, $db_pass, $db_name);
        }
    }

    /**
     * 执行SQL语句
     * @param string $sql
     * @return boolean
     */
    public function execute($sql)
    {
        Logger::getInstance('sql')->debug($sql);
        return self::$db->query($sql);
    }

    /**
     * 获取表中数据
     * @param string $sql SELECT语句
     * @param boolean $onlyOne 是否仅有一条记录
     * @return array|boolean
     */
    protected function select($sql, $onlyOne = false)
    {
        Logger::getInstance('sql')->debug($sql);
        if (!stristr($sql, 'SELECT')) return false;
        $rs = self::$db->query($sql);
        $result = array();
        if (true == $onlyOne) {
            $result = self::$db->fetchArray($rs);
        } else {
            while ($arr = self::$db->fetchArray($rs))
                $result[] = $arr;
        }
        self::$db->free($rs);
        return $result;
    }

    /**
     * 获取一条数据
     * @param $sql
     * @return array|bool
     */
    public function selectOne($sql)
    {
        return $this->select($sql, true);
    }

    /**
     * 获取多条数据
     * @param $sql
     * @return array|bool
     */
    public function selectMulti($sql)
    {
        return $this->select($sql, false);
    }

    /**
     * 添加一条数据，成功将返回自动编号
     * @param string $table 要操作的表
     * @param array $data 要插入的数据，数组的键名对应表中的字段。
     * @return int
     */
    public function insert($table, $data)
    {
        $item1 = array();
        $item2 = array();
        foreach ($data as $key => $val) {
            $item1[] = $key; //字段名;
            $item2[] = is_numeric($val) ? $val : '\'' . $val . '\''; //字段值;
        }
        $i = implode(',', $item1);
        $j = implode(',', $item2);
        $sql = "INSERT INTO $table ( $i ) VALUES ( $j )";
        Logger::getInstance('sql')->debug($sql);
        self::$db->query($sql);
        return self::$db->insertId();
    }

    /**
     * 编辑一条数据
     * @param string $table 要操作的表
     * @param array $data 要更新的数据，数组的键名对应表中的字段。
     * @param string $where WHERE条件，如：id=2
     * @return boolean
     */
    public function update($table, $data, $where = 'id=0')
    {
        $item = array();
        foreach ($data as $key => $val) {
            $val = is_numeric($val) ? $val : '\'' . $val . '\''; //字段值;
            $item[] = $key . '=' . $val; //形如：title='标题'
        }
        $i = implode(',', $item);
        $sql = "UPDATE $table SET $i WHERE $where";
        Logger::getInstance('sql')->debug($sql);
        return self::$db->query($sql);
    }

    /**
     * 添加一条数据，若数据已存在，则修改数据。
     * 注意：表必须有主键或者是唯一索引，否则将会直接插入导致表中出现重复的数据。
     * @param string $table 要操作的表
     * @param array $insertData 要插入的数据，数组的键名对应表中的字段。
     * @param array $updateData 若数据已存在则要更改为此数据，数组的键名对应表中的字段。
     * @return bool
     */
    public function insertOrUpdate($table, $insertData, $updateData)
    {
        $item1 = array();
        $item2 = array();
        foreach ($insertData as $key1 => $val1) {
            $item1[] = $key1; //字段名;
            $item2[] = is_numeric($val1) ? $val1 : '\'' . $val1 . '\''; //字段值;
        }
        $i = implode(',', $item1);
        $j = implode(',', $item2);
        $item = array();
        foreach ($updateData as $key2 => $val2) {
            $val2 = is_numeric($val2) ? $val2 : '\'' . $val2 . '\''; //字段值;
            $item[] = $key2 . '=' . $val2; //形如：title='标题'
        }
        $k = implode(',', $item);
        $sql = "INSERT INTO $table ($i) VALUES ($j) ON DUPLICATE KEY UPDATE $k";
        Logger::getInstance('sql')->debug($sql);
        return self::$db->query($sql);
    }

    /**
     * 删除一条数据
     * @param string $table 要操作的表
     * @param string $where WHERE条件，如：id=2
     * @return boolean
     */
    public function delete($table, $where = 'id=0')
    {
        $sql = "DELETE FROM $table WHERE $where";
        Logger::getInstance('sql')->debug($sql);
        return self::$db->query($sql);
    }

    /**
     * 得到缓存文件绝对路径
     * @param $name
     * @return string
     */
    public function obtainCachePath($name)
    {
        $path = ROOT_PATH . '/data/cache';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);//允许嵌套创建目录
        }
        return $path . '/' . $name . '.cache';
    }

    /**
     * 缓存数据
     * @param string $name 缓存文件的名称
     * @param array $data 来自数据库的数据
     */
    public function cache($name, $data = array())
    {
        $cachePath = $this->obtainCachePath($name);
        file_put_contents($cachePath, serialize($data));
        if (!file_exists($cachePath)) {
            exit('创建' . $name . '缓存失败，请检查缓存目录是否可写');
        }
    }

    public function uncache($name)
    {
        $cachePath = $this->obtainCachePath($name);
        return unserialize(file_get_contents($cachePath));
    }

}

?>