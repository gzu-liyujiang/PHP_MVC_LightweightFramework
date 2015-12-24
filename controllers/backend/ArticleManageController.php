<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2013/3/31
 */
class ArticleManageController extends LoginedController
{

    public function main()
    {
        $this->template->assign('title', '文章管理');
        $articleModel = new ArticleModel();
        $this->template->assign('articles', $articleModel->getArticleList());
        $this->template->display("ArticleIndex.htm");
    }

    public function add()
    {
        if ($this->isPost()) {
            $cid = intval($_POST['category']);
            //数据入库之前必须转义字符
            $title = htmlspecialchars(addslashes(urldecode($_POST['title'])));
            //不实体化内容中的HTML
            $content = addslashes(urldecode($_POST['content']));
            $timeline = time();
            if (empty($title) || empty($content)) {
                Flight::getInstance()->halt(200, '标题或内容不能为空！');
            }
            $model = new ArticleModel();
            if ($model->isSimilar($title)) {
                Flight::getInstance()->halt(200, '数据库中已存在相似的标题，请重新修改');
            } else {
                $data = array('category_id' => $cid, 'title' => $title, 'content' => $content, 'timeline' => $timeline);
                $id = $model->addArticle($data);
                if ($id > 0) {
                    Flight::getInstance()->halt(200, '恭喜，《' . $title . '》发表成功！');
                } else {
                    Flight::getInstance()->halt(200, '很不幸，《' . $title . '》发表失败！');
                }
            }
        } else {
            $this->template->assign('title', '添加文章');
            $model = new CategoryModel();
            $this->template->assign('categories', $model->getCategoryList());
            $this->template->display("ArticleAdd.htm");
        }
    }

    public function modify()
    {
        if ($this->isPost()) {
            if (!isset($_GET['id'])) {
                Flight::getInstance()->halt(200, '错误，未传递ID！');
            }
            $id = intval($_GET['id']);
            $cid = intval($_POST['category']);
            $title = htmlspecialchars(addslashes(urldecode($_POST['title'])));
            //不实体化内容中的HTML
            $content = addslashes(urldecode($_POST['content']));
            if (empty($title) || empty($content)) {
                Flight::getInstance()->halt(200, '标题或内容不能为空！');
            }
            $data = array('category_id' => $cid, 'title' => $title, 'content' => $content);
            $model = new ArticleModel();
            if (false != $model->editArticle($id, $data)) {
                Flight::getInstance()->halt(200, '恭喜，《' . $title . '》编辑成功！');
            } else {
                Flight::getInstance()->halt(200, '很不幸，《' . $title . '》编辑失败！');
            }
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $articleModel = new ArticleModel();
            $res = $articleModel->getArticleById($id);
            if (!$res) {
                $res['title'] = '文章不存在';
                $res['category_id'] = 0;
                $res['content'] = '文章不存在';
                $res['timeline'] = 0;
            }
            $this->template->assign('id', $id);
            $categoryModel = new CategoryModel();
            $this->template->assign('categories', $categoryModel->getCategoryList());
            $this->template->assign('category_id', $res['category_id']);
            $this->template->assign('title', $res['title']);
            $this->template->assign('content', $res['content']);
            $this->template->assign('timeline', $res['timeline']);
            $this->template->display("ArticleModify.htm");
        }
    }

    public function delete()
    {
        if (!isset($_GET['id'])) {
            Flight::getInstance()->halt(200, '错误，未传递ID！');
        }
        $model = new ArticleModel();
        if ($model->deleteArticle(intval($_GET['id']))) {
            Flight::getInstance()->halt(200, '恭喜，删除成功！');
        } else {
            Flight::getInstance()->halt(200, '很不幸，删除失败');
        }
    }

}