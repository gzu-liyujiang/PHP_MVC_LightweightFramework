<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-14 下午3:02
 */
class AccessTokenController extends ApiController
{
    /**
     * @var AppTokenModel
     */
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new AppTokenModel();
    }

    public function get()
    {
//        $app_id = isset($_REQUEST['app_id']) ? urldecode($_REQUEST['app_id']) : "";
//        $app_secret = isset($_REQUEST['app_secret']) ? urldecode($_REQUEST['app_secret']) : "";
//        if (empty($app_id)) {
//            $this->responseJson(0, "app id不能为空");
//        }
//        if (empty($app_secret)) {
//            $this->responseJson(0, "app secret不能为空");
//        }
//        $appModel = new AppModel();
//        $apps = $appModel->findByAppId($app_id);
//        if (!$apps) {
//            $this->responseJson(0, "app id不存在", $app_id);
//        }
//        if ($appModel->buildSecret($app_secret) != $apps["app_secret"]) {
//            $this->responseJson(0, "app secret错误", $app_secret);
//        }
//        $tokenModel = new AppTokenModel();
//        $token = $tokenModel->build($apps["id"]);
//        if ($tokenModel->save($apps["id"], $token)) {
//            $this->responseJson(1, "app授权成功", $token);
//        } else {
//            $this->responseJson(0, "app授权失败", $_POST);
//        }
        $this->noPermission();
    }

    public function post()
    {
        $this->get();
    }

    public function put()
    {
        // TODO: Implement put() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }
}