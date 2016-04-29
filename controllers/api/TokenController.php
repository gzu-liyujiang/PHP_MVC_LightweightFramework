<?php

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
     * 检测登录状态
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
     * APP授权，换取访问API的token
     */
    public function appAuth()
    {
        $app_id = isset($_REQUEST['app_id']) ? urldecode($_REQUEST['app_id']) : "";
        $app_secret = isset($_REQUEST['app_secret']) ? urldecode($_REQUEST['app_secret']) : "";
        if (empty($app_id)) {
            $this->responseJson(0, "app id不能为空");
        }
        if (empty($app_secret)) {
            $this->responseJson(0, "AppSecret不能为空");
        }
       $userModel = new UserModel();
        $user = $userModel->findByAccount($app_id);
        if (!$user) {
            $this->responseJson(0, "AppID不存在", $app_id);
        }
        if ($userModel->buildPassword($app_secret) != $user["password"]) {
            $this->responseJson(0, "AppSecret错误", $app_secret);
        }
        if ($user["is_forbidden"] != 0){
            $this->responseJson(0, "APP禁止访问API", $_POST);
        }
        $tokenModel = new TokenModel();
        $token = $tokenModel->build($user["id"]);
        if ($tokenModel->save($user["id"], $token)) {
            $this->responseJson(1, "APP授权成功", $token);
        } else {
            $this->responseJson(0, "APP授权失败", $_POST);
        }

    }

    /**
     * 第三方用户授权，作为登录凭证
     *
     * @return bool
     */
    public function userAuth()
    {
        $device_id = isset($_REQUEST['device_id']) ? $_REQUEST['device_id'] : "";
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : "";
        if (empty($token)) {
            $this->responseJson(0, "Token不能为空", $token);
            return FALSE;
        }
        $nick = isset($_REQUEST['nick']) ? $_REQUEST['nick'] : "";
        if (empty($nick)) {
            $nick = "" . rand(10000, 99999);
        }
        $face = isset($_REQUEST['face']) ? $_REQUEST['face'] : Flight::getInstance()->get("base_url") . "/views/assets/img/icon.png";
        $sex = isset($_REQUEST['sex']) ? intval($_REQUEST['sex']) : 0;
        $userModel = new UserModel();
        $user = $userModel->findByToken($token);
        $tokenModel = new TokenModel();
        if ($user) {
            if ($user["is_forbidden"] != 0){
                $this->responseJson(0, "用户已被冻结", $_REQUEST);
            } else{
                //token存在则更新时间
                $tokenModel->updateTime($token);
                unset($user['password']); //过滤掉密码
                unset($user['is_app']);
                $this->responseJson(1, "用户授权成功", $user);
            }
        } else {
            //token不存在则添加，需防刷
            $id = $userModel->add(array(
                'account' => NULL,
                'password' => NULL,
                'nick' => $nick,
                'sex' => $sex,
                'face' => $face,
                'device_id' => $device_id,
                'is_forbidden' => 0,
                'timeline' => time()
            ));
            if ($id > 0){
                $tokenModel->save($id, $token);
                $this->responseJson(0, "用户授权成功", $id);
            } else {
                $this->responseJson(0, "用户授权失败", $_REQUEST);
            }
        }
    }

    public function revoke()
    {
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : "";
        if (empty($token)) {
            $this->responseJson(0, "Token为空", $token);
        } else {
            $res = $this->model->read($token);
            if ($res) {
                if ($this->model->remove($res["token"])) {
                    $this->responseJson(1, "取消用户授权成功");
                } else {
                    $this->responseJson(0, "取消用户授权失败");
                }
            } else {
                $this->responseJson(1, "已经取消用户授权");
            }
        }
    }

}