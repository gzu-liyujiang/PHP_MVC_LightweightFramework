<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2015/12/28
 * Created by Notpad2
 */
class UrlController extends Controller
{

    public function main()
    {
        $this->home();
    }

    public function home()
    {
        header('Location: ' . Flight::getInstance()->get('base_url') . '/index.php?c=Link');
        exit;
    }

    public function feedback()
    {
        header('Location: ' . Flight::getInstance()->get('base_url') . '/index.php?c=About');
        exit;
    }

}

