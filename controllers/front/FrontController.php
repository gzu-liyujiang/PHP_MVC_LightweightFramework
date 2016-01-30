<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-30 下午5:02
 */

/**
 * 前台控制器
 */
abstract class FrontController extends Controller
{
    protected $base_url;
    /**
     * @var Template
     */
    protected $template;

    public function __construct()
    {
        $this->base_url = Flight::getInstance()->get('base_url');
        $this->template = Template::getInstance();
        $this->template->setTemplateDir(ROOT_PATH . '/views/front/');
        $this->template->assign('author', '李玉江，QQ:1032694760');
        $this->template->assign('title', '网页标题');
        $this->template->assign('base_url', Flight::getInstance()->get('base_url'));
        $this->template->assign('seo_title', '网站名称');
        $this->template->assign('seo_keywords', '关键词');
        $this->template->assign('seo_description', '网页描述');
        $this->template->assign('copyright', 'Copyright &copy; 穿青人-李玉江');
    }

}

