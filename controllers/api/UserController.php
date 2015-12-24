<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-27 下午5:05
 */
class UserController extends ApiController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * 用户名及密码登录
     */
    public function login()
    {
        if (!$this->isPost()) {
            $this->responseJson(0, "请求方式无效");
            return;
        }
        $account = isset($_POST['account']) ? urldecode($_POST['account']) : "";
        $password = isset($_POST['password']) ? urldecode($_POST['password']) : "";
        if (empty($account)) {
            $this->responseJson(0, "账号不能为空");
        }
        if (empty($password)) {
            $this->responseJson(0, "密码不能为空");
        }
        $user = $this->userModel->findByAccount($account);
        if (!$user) {
            $this->responseJson(0, "用户不存在", $account);
        }
        if ($this->userModel->buildPassword($password) != $user["password"]) {
            $this->responseJson(0, "密码错误", $password);
        }
        $tokenModel = new TokenModel();
        $token = $tokenModel->build($user["id"]);
        if ($tokenModel->save($user["id"], $token)) {
            $this->responseJson(1, "用户授权成功", $token);
        } else {
            $this->responseJson(0, "用户授权失败", $_POST);
        }
    }

    public function register()
    {
        if (!$this->isPost()) {
            $this->responseJson(0, "请求方式无效");
            return;
        }
        $account = isset($_POST['account']) ? urldecode($_POST['account']) : "";
        $password = isset($_POST['password']) ? urldecode($_POST['password']) : "";
        $nick = isset($_POST['nick']) ? urldecode($_POST['nick']) : $account;
        $device_id = isset($_POST['device_id']) ? trim($_POST['device_id']) : '';
        if (empty($account)) {
            $this->responseJson(0, "账号不能为空");
        }
        if (empty($password)) {
            $this->responseJson(0, "密码不能为空");
        }
        $user = $this->userModel->findByAccount($account);
        if ($user) {
            $this->responseJson(0, "用户账号已存在", $account);
        }
        $hashPassword = $this->userModel->buildPassword($password);
        $id = $this->userModel->add(array(
            'account' => $account,
            'password' => $hashPassword,
            'nick' => $nick,
            'sex' => 0,
            'device_id' => $device_id,
            'timeline' => time()
        ));
        if ($id > 0) {
            $this->responseJson(1, "用户注册成功", $id);
        } else {
            $this->responseJson(0, "用户注册失败", $_POST);
        }
    }

    public function info()
    {
        $token = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : "";
        $account = isset($_REQUEST['account']) ? trim($_REQUEST['account']) : "";
        if (empty($token)) {
            $this->responseJson(0, "Token不能为空");
        }
        if (!empty($account)) {
            //账号参数不为空，则获取指定账号的资料
            $res = $this->userModel->findByAccount($account);
        } else {
            $res = $this->userModel->findByToken($token);
        }
        if ($res) {
            $newRes = array();
            foreach ($res as $k => $v) {
                //过滤掉密码
                if ($k !== 'password') {
                    $newRes[$k] = $v;
                }
            }
            $this->responseJson(1, "获取用户资料成功", $newRes);
        } else {
            $this->responseJson(0, "获取用户资料失败");
        }
    }

    public function modifyPassword()
    {
        if (!$this->isPost()) {
            $this->responseJson(0, "请求方式无效");
            return;
        }
        $token = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : "";
        $password = isset($_REQUEST['password']) ? urldecode(trim($_REQUEST['password'])) : "";
        if (empty($token)) {
            $this->responseJson(0, "登录令牌为空");
            return;
        }
        if (empty($password)) {
            $this->responseJson(0, "密码不能为空");
            return;
        }
        $res = $this->userModel->findByToken($token);
        if (!$res) {
            $this->responseJson(0, "获取用户资料失败");
        } else {
            $passwordHash = $this->userModel->buildPassword($password);
            $boo = $this->userModel->modify(array(
                'id' => $res['id'],
                'password' => $passwordHash
            ));
            if ($boo) {
                $this->responseJson(1, "修改密码成功");
            } else {
                $this->responseJson(0, "修改密码失败");
            }
        }
    }

    public function find()
    {
        $device_ids = isset($_REQUEST['device_ids']) ? trim($_REQUEST['device_ids']) : "";
        if (empty($device_ids)) {
            $this->responseJson(0, "参数错误");
        }
        $res = $this->userModel->findByDeviceIds($device_ids);
        if (!$res) {
            $this->responseJson(0, "获取用户列表失败");
        } else {
            $this->responseJson(1, "获取用户列表成功", $res);
        }
    }

}