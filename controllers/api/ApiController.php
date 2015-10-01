<?php
/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-14 下午3:02
 */

/**
 * 接口控制器
 */
class ApiController extends Controller
{

    public function __construct()
    {
        $access_token = isset($_REQUEST['access_token']) ? $_REQUEST['access_token'] : '';
        if (empty($access_token)) {
            $this->noPermission();
        }
        $tokenController = new TokenController();
        if (!$tokenController->check($access_token)) {
            $this->noPermission();
        }
    }

    public function main()
    {
        $this->noPermission();
    }

    /**
     * 返回JSON数据
     *
     * @param $code
     * @param $msg
     * @param $data
     */
    protected function responseJson($code, $msg, $data = NULL)
    {
        $res = array(
            "code" => $code,
            "msg" => $msg,
            "data" => $data
        );
        Flight::getInstance()->json($res);
    }

    public function noPermission()
    {
        //$this->responseJson(0, "没有权限访问");
    }
    
}