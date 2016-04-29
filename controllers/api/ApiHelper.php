<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-10-02 18:06
 */
class ApiHelper
{

    /**
     * 检测access_token是否有效
     *
     * @param $token
     * @return array|bool
     */
    public static function accessTokenValid($token)
    {
        if (empty($token)) {
            Logger::getInstance()->warn("Token为空");
            return false;
        }
        $model = new TokenModel();
        $res = $model->read($token);
        if ($res) {
            return $res;
        } else {
            Logger::getInstance()->warn("未授权或已过期");
            return false;
        }
    }

}