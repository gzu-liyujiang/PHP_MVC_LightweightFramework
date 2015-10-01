<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-30 下午5:02
 */
class AboutController extends FrontController
{

    public function main()
    {
        $this->template->assign('title', '关于我们');
        $this->template->display("About.htm");
    }

}

