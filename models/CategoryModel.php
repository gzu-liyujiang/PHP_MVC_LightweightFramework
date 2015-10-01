<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2015/7/26
 * Created by IntelliJ IDEA
 */
final class CategoryModel extends Model
{

    /**
     * 获取所有分类
     * @return array|bool
     */
    public function getCategoryList()
    {
        $cachePath = $this->obtainCachePath('category');
        if (!file_exists($cachePath)) {
            return $this->updateCache();
        }
        return $this->uncache('category');
    }

    /**
     * 按ID获取分类
     * @param int $id
     * @return array|bool
     */
    public function getCategoryById($id)
    {
        $sql = 'SELECT * FROM lyj_category WHERE id=' . $id;
        return $this->selectOne($sql);
    }

    /**
     * 添加分类
     * @param array $data
     * @return bool
     */
    public function addCategory($data)
    {
        $result = $this->insert('lyj_category', $data);
        if ($result > 0) {
            $this->updateCache();
        }
        return $result;
    }

    /**
     * 编辑分类
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function editCategory($id, $data)
    {
        $result = $this->update('lyj_category', $data, 'id=' . $id);
        if ($result) {
            $this->updateCache();
        }
        return $result;
    }

    /**
     * 删除分类
     * @param int $id
     * @return bool
     */
    public function deleteCategory($id)
    {
        $result = $this->delete('lyj_category', 'id=' . $id);
        if ($result) {
            $this->updateCache();
        }
        return $result;
    }

    /**
     * 判断分类是否重复
     * @param string $name 分类名称
     * @return boolean
     */
    public function isSimilar($name)
    {
        $sql = 'SELECT name FROM lyj_category ORDER BY id DESC LIMIT 0,1';
        $arr = $this->selectOne($sql);
        //比较名称相似度
        similar_text($arr['name'], $name, $percent_name);
        if (90 < $percent_name) {
            return true;
        }
        return false;
    }

    private function updateCache()
    {
        $category = $this->selectMulti('SELECT * FROM lyj_category ORDER BY sortby ASC,id DESC');
        if (!$category) {
            $category = array();
        }
        $this->cache('category', $category);
        return $category;
    }

}
