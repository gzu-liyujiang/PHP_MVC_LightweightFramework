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
        $this->template = Template::getInstance();
        $this->template->setTemplateDir(ROOT_PATH . '/views/front/');
        if (!file_exists(ROOT_PATH . '/config.php')) {
            $this->template->assign('url', './admin.php?c=install');
            $this->template->display("Redirect.htm");
			return;
        }
        $this->base_url = Flight::getInstance()->get('base_url');
        $model = new SettingModel();
        $this->template->assign('base_url', $this->base_url);
        $this->template->assign('author', $model->getValue('contact'));
        $this->template->assign('title', '无标题');
        $this->template->assign('seo_title', $model->getValue('seo_title'));
        $this->template->assign('seo_keywords', $model->getValue('seo_keywords'));
        $this->template->assign('seo_description', str_replace(array("\r\n", "\r", "\n"), "",strip_tags($model->getValue('seo_description'))));
        $this->template->assign('copyright', $model->getValue('copyright'));
	}

}

