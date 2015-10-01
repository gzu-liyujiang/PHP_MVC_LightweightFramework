<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2014/3/31
 */
final class LinkModel extends Model
{

    /**
     * 获取所有友情链接
     * @return array|bool
     */
    public function getLinkList()
    {
        $sql = 'SELECT * FROM lyj_link ORDER BY sortby ASC,id DESC';
        return $this->selectMulti($sql);
    }

    /**
     * 获取所有友情链接（含分类）
     * @return array|bool
     */
    public function getLinkListIncludeCategory()
    {
        $sql = 'SELECT a.*,b.name as category_name FROM lyj_link a, lyj_category b WHERE a.category_id=b.id ORDER BY a.sortby ASC,a.id DESC';
        return $this->selectMulti($sql);
    }

    /**
     * 按ID获取友情链接
     * @param int $id
     * @return array|bool
     */
    public function getLinkById($id)
    {
        $sql = 'SELECT * FROM lyj_link WHERE id=' . $id;
        return $this->selectOne($sql);
    }

    /**
     * 添加一条友情链接
     * @param array $data
     * @return int
     */
    public function addLink($data)
    {
        return $this->insert('lyj_link', $data);
    }

    /**
     * 编辑友情链接
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function editLink($id, $data)
    {
        return $this->update('lyj_link', $data, 'id=' . $id);
    }

    /**
     * 删除友情链接
     * @param int $id
     * @return bool
     */
    public function deleteLink($id)
    {
        return $this->delete('lyj_link', 'id=' . $id);
    }

    /**
     * 判断友情链接是否重复
     * @param string $name 网站名称
     * @param string $url 网站地址
     * @return boolean
     */
    public function isSimilar($name, $url)
    {
        $sql = 'SELECT name,url FROM lyj_link ORDER BY id DESC LIMIT 0,1';
        $arr = $this->selectOne($sql);
        //比较名称相似度
        similar_text($arr['name'], $name, $percent_name);
        if (90 < $percent_name) {
            return true;
        }
        //比较地址是否相同
        return $arr['url'] == $url;
    }

}

?>