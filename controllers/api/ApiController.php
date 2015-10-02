<?php
/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-14 下午3:02
 */

/**
 * 接口控制器
 */
abstract class ApiController extends Controller
{

    public function __construct()
    {
        $access_token = isset($_REQUEST['access_token']) ? $_REQUEST['access_token'] : '';
        if (empty($access_token)) {
            $this->noPermission();
        }
        if (ApiHelper::checkToken($access_token)) {
            $this->noPermission();
        }
    }

    public function main()
    {
        $method = $this->requestMethod();
        switch ($method) {
            case 'POST':
                $this->post();
                break;
            case 'PUT':
                $this->put();
                break;
            case 'DELETE':
                $this->delete();
                break;
            default:
                $this->get();
                break;
        }
    }

    public abstract function get();

    public abstract function post();

    public abstract function put();

    public abstract function delete();

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

    protected function noPermission()
    {
        //$this->responseJson(0, "没有权限访问");
    }

}