<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-30 下午5:02
 */
class LinkController extends FrontController
{

    public function main()
    {
        $this->template->assign('title', '友情推荐');
        $model = new LinkModel();
        $this->template->assign('links', $model->getLinkList());
        $this->template->display('LinkIndex.htm');
    }

}
