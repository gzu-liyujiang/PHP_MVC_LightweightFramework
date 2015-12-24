<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2015/7/26
 * Created by IntelliJ IDEA
 */
abstract class LoginedController extends BackendController
{

    public function __construct()
    {
        parent::__construct();
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
        if (empty($token)) {
            $this->goToLogin();
            return;
        }
        $tokenController = new TokenController();
        if ($tokenController->check($token)) {
            $this->template->assign('token', $_REQUEST['token']);
        } else {
            $this->goToLogin();
        }
    }

    protected function goToLogin()
    {
        Flight::getInstance()->redirect('./admin.php?c=Unlogin');
    }

}