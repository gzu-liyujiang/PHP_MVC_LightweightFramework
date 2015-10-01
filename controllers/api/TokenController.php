<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-14 下午3:02
 */
class TokenController extends ApiController
{
    /**
     * @var TokenModel
     */
    private $model;

    public function __construct()
    {
        $this->model = new TokenModel();
        if (Flight::getInstance()->get("token.expires")) {
            $this->model->setExpires(Flight::getInstance()->get("token.expires"));
        }
    }

    /**
     * 检测token状态
     *
     * @param $token
     * @return array|bool
     */
    public function check($token)
    {
        if (empty($token)) {
            Logger::getInstance()->warn("Token为空");
            return false;
        }
        $res = $this->model->read($token);
        if ($res) {
            return $res;
        } else {
            Logger::getInstance()->warn("未授权或已过期");
            return false;
        }
    }
    
    /**
     * APP授权，换取访问api的token
     */
    public function auth()
    {
        $app_id = isset($_REQUEST['app_id']) ? urldecode($_REQUEST['app_id']) : "";
        $app_secret = isset($_REQUEST['app_secret']) ? urldecode($_REQUEST['app_secret']) : "";
        if (empty($app_id)) {
            $this->responseJson(0, "app id不能为空");
        }
        if (empty($app_secret)) {
            $this->responseJson(0, "app secret不能为空");
        }
       $userModel = new UserModel();
        $user = $userModel->findByAccount($app_id);
        if (!$user) {
            $this->responseJson(0, "app id不存在", $app_id);
        }
        if ($userModel->buildPassword($password) != $user["password"]) {
            $this->responseJson(0, "app secret错误", $app_secret);
        }
        $tokenModel = new TokenModel();
        $token = $tokenModel->build($user["id"]);
        if ($tokenModel->save($user["id"], $token)) {
            $this->responseJson(1, "app授权成功", $token);
        } else {
            $this->responseJson(0, "app授权失败", $_POST);
        }

    }

}