<?php
/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-30 下午5:02
 */

/**
 * 后台控制器
 */
abstract class BackendController extends Controller
{
    /**
     * @var Template
     */
    protected $template;

    public function __construct()
    {
        $this->template = Template::getInstance();
        $this->template->setTemplateDir(ROOT_PATH . '/views/backend/');
        $this->template->assign('title', '后台管理系统');
    }

}

