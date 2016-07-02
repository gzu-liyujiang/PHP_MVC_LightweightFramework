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
    protected $base_url;
    /**
     * @var Template
     */
    protected $template;

    public function __construct()
    {
        $this->template = Template::getInstance();
        $this->template->setTemplateDir(ROOT_PATH . '/views/backend/');
		$isInstall = $_GET['c'] == 'install';
        if (!file_exists(ROOT_PATH . '/config.php')) {
            $this->template->assign('url', './admin.php?c=install');
            $this->template->display("Redirect.htm");
        } else {
            $this->template->assign('title', '后台管理系统');
            $this->base_url = Flight::getInstance()->get('base_url');
            $this->template->assign('base_url', $this->base_url);	
		}
    }

}

