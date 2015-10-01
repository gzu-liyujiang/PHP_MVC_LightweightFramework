<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2014/3/31
 */
final class ArticleModel extends Model
{
    const ARTICLE_TABLE_NAME = 'lyj_article';
    const CATEGORY_TABLE_NAME = 'lyj_category';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取最新文章
     * @param int $cid 栏目ID，为0时代表所有栏目
     * @param int $num 最新文章数，为0时代表所有文章
     * @return array|bool
     */
    public function getArticleListOrderByNew($cid = 0, $num = 0)
    {
        $where = (intval($cid) != 0) ? 'WHERE category_id=' . $cid : '';
        $where .= (intval($num) != 0) ? ' ORDER BY id DESC LIMIT 0,' . $num : ' ORDER BY id DESC';
        $sql = 'SELECT * FROM ' . self::ARTICLE_TABLE_NAME . ' ' . $where;
        return $this->selectMulti($sql);
    }

    /**
     * 获取随机文章
     * @param int $cid 栏目ID，为0时代表所有栏目
     * @param int $num 随机文章数，为0时代表所有文章
     * @return array|bool
     */
    public function getArticleListOrderByRand($cid = 0, $num = 0)
    {
        $where = (intval($cid) != 0) ? 'WHERE category_id=' . $cid : '';
        $where .= (intval($num) != 0) ? ' ORDER BY rand() LIMIT 0,' . $num : ' ORDER BY rand()';
        $sql = 'SELECT * FROM ' . self::ARTICLE_TABLE_NAME . ' ' . $where;
        return $this->selectMulti($sql);
    }

    /**
     * 获取所有文章
     * @return array|bool
     */
    public function getArticleList()
    {
        $sql = 'SELECT a.*,b.name AS category FROM ' . self::ARTICLE_TABLE_NAME . ' a ,' . self::CATEGORY_TABLE_NAME . ' b WHERE a.category_id=b.id ORDER BY a.id DESC';
        return $this->selectMulti($sql);
    }

    /**
     * 按栏目ID获取所有文章
     * @param $cid
     * @return array|bool
     */
    public function getArticleListByCid($cid)
    {
        $sql = 'SELECT * FROM ' . self::ARTICLE_TABLE_NAME . ' WHERE cid=' . $cid . ' ORDER BY id DESC';
        return $this->selectMulti($sql);
    }

    /**
     * 获取搜索到的所有文章
     * @param string $keyword 搜索词
     * @return array|bool
     */
    public function getArticleListByKeyword($keyword)
    {
        $sql = "SELECT * FROM " . self::ARTICLE_TABLE_NAME . " WHERE title LIKE '%$keyword%' OR content LIKE '%$keyword%'";
        return $this->selectMulti($sql);
    }

    /**
     * 按ID获取一篇文章
     * @param int $id
     * @return array|bool
     */
    public function getArticleById($id)
    {
        $sql = 'SELECT * FROM ' . self::ARTICLE_TABLE_NAME . ' WHERE id=' . $id;
        return $this->selectOne($sql);
    }

    /**
     * 随机获取一篇文章
     * @return array|bool
     */
    public function getArticleByRand()
    {
        $sql = 'SELECT * FROM ' . self::ARTICLE_TABLE_NAME . ' ORDER BY rand() LIMIT 0,1';
        return $this->selectOne($sql);
    }

    /**
     * 添加一篇文章
     * @param array $data
     * @return int
     */
    public function addArticle($data)
    {
        return $this->insert(self::ARTICLE_TABLE_NAME, $data);
    }

    /**
     * 编辑文章
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function editArticle($id, $data)
    {
        return $this->update(self::ARTICLE_TABLE_NAME, $data, 'id=' . $id);
    }

    /**
     * 删除文章
     * @param int $id
     * @return bool
     */
    public function deleteArticle($id)
    {
        return $this->delete(self::ARTICLE_TABLE_NAME, 'id=' . $id);
    }

    /**
     * 判断文章是否重复
     * @param string $title 标题
     * @return bool
     */
    public function isSimilar($title)
    {
        $sql = "SELECT title FROM " . self::ARTICLE_TABLE_NAME . " ORDER BY id DESC LIMIT 0,1";
        $arr = $this->selectOne($sql);
        //比较标题相似度
        similar_text($arr['title'], $title, $percent_title);
        if (90 < $percent_title) {
            Logger::getInstance()->debug('相似度为：' . intval($percent_title) . '%');
            return true;
        }
        return false;
    }

    /**
     * 更新文章评论数
     * @param int $id
     * @return bool
     */
    public function updateCommentCount($id)
    {
    }

    /**
     * 更新文章人气数
     * @param int $id
     * @return bool
     */
    public function updateVisitCount($id)
    {
    }

}

?>