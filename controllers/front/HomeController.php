<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-30 下午5:02
 */
class HomeController extends FrontController
{

    public function main()
    {
        $this->template->assign('title', '输入法皮肤控官网');
        $this->template->display("Index.htm");
    }

}

