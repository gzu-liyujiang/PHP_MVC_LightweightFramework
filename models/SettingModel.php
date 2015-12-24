<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2015/7/25
 * Created by IntelliJ IDEA
 */
final class SettingModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getValue($key)
    {
        $cachePath = $this->obtainCachePath('setting');
        if (!file_exists($cachePath)) {
            $data = $this->updateCache();
        } else {
            $data = $this->uncache('setting');
        }
        return $data[$key];
    }

    public function saveValue($key, $val = null)
    {
        if (is_array($key) && count($key) > 0) {
            //批量更新数据，参见：http://www.cnblogs.com/bruceleeliya/p/3310137.html
            $sql = "UPDATE lyj_setting SET sval= CASE skey ";
            foreach ($key as $k => $v) {
                $sql .= sprintf("WHEN '%s' THEN '%s' ", $k, $v);
            }
            $ids = '\'' . implode('\',\'', array_keys($key)) . '\'';
            $sql .= "END WHERE skey IN ($ids)";
            $result = $this->execute($sql);
        } else {
            $data = array(
                'skey' => $key,
                'sval' => $val
            );
            $result = $this->insertOrUpdate('lyj_setting', $data, $data);
        }
        if ($result) {
            $this->updateCache();
        }
        return $result;
    }

    private function updateCache()
    {
        $data = array();
        $setting = $this->selectMulti("SELECT skey,sval FROM lyj_setting");
        foreach ($setting as $set) {
            $data[$set['skey']] = $set['sval'];
        }
        unset($setting);
        $this->cache('setting', $data);
        return $data;
    }

}