<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-30 下午5:02
 */
class ArticleController extends FrontController
{
    private $articleModel;

    public function __construct()
    {
        parent::__construct();
        $this->articleModel = new ArticleModel();
    }

    public function main()
    {
        $this->template->assign('title', '文章列表');
        $res = $this->articleModel->getArticleList();
        $this->template->assign('articles', $res);
        $this->template->display("ArticleIndex.htm");
    }

    public function detail()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $res = $this->articleModel->getArticleById($id);
        if (!$res) {
            $res['title'] = '文章不存在';
            $res['content'] = '文章不存在';
            $res['timeline'] = 0;
        }
        $this->template->assign('title', $res['title']);
        $this->template->assign('description', $res['title']);
        $this->template->assign('content', $res['content']);
        $this->template->assign('timeline', $res['timeline']);
        $this->template->display("ArticleDetail.htm");
    }

    public function search()
    {
        $keyword = isset($_GET['keyword']) ? urldecode($_GET['keyword']) : '请输入关键词';
        $this->template->assign('title', '搜索“' . $keyword . '”');
        $this->template->assign('keyword', strip_tags($keyword));
        $model = new ArticleModel();
        $this->template->assign('articles', $model->getArticleListByKeyword($keyword));
        $this->template->display('Search.htm');
    }

}
