<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-30 下午5:02
 */
final class UbbController extends BackendController
{

    public function main()
    {
        $this->template->assign('title', 'UBB说明');
        $this->template->display("Ubb.htm");
    }

}