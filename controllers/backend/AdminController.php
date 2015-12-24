<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-15 下午5:13
 */
class AdminController extends LoginedController
{

    public function main()
    {
        $this->template->assign('title', '后台首页');
        $this->template->display("Index.htm");
    }

    public function setting()
    {
        $model = new SettingModel();
        $this->template->assign('title', '设置');
        $this->template->assign('contact', $model->getValue('contact'));
        $this->template->assign('seo_title', $model->getValue('seo_title'));
        $this->template->assign('seo_keywords', $model->getValue('seo_keywords'));
        $this->template->assign('seo_description', $model->getValue('seo_description'));
        $this->template->assign('copyright', $model->getValue('copyright'));
        $this->template->display("Setting.htm");
    }

    public function modifyPassword()
    {
        $this->template->assign('title', '修改密码');
        $this->template->display("ModifyPassword.htm");
    }

}