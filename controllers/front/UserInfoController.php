<?php

class UserInfoController extends FrontController
{

    public function main()
    {
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
        $tokenController = new TokenController();
        $res = $tokenController->check($token);
        if ($res) {
            $userModel = new UserModel();
            $user = $userModel->findById($res["id"]);
            $this->template->assign("user", $user);
            $this->template->display("UserIndex.htm");
        } else {
            $this->login();
        }
    }

    public function login()
    {
        $this->template->display("UserLogin.htm");
    }

    public function register()
    {
        $this->template->display("UserRegister.htm");
    }


}

