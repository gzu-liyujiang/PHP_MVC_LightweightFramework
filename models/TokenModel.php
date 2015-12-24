<?php

final class TokenModel extends Model
{
    private $_expire_time;

    public function __construct()
    {
        parent::__construct();
        $this->_expire_time = 3600 * 24 * 30;
    }

    /**
     * @param $expires
     */
    public function setExpires($expires)
    {
        $this->_expire_time = $expires;
    }

    /**
     * @return int
     */
    public function getExpires()
    {
        return $this->_expire_time;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function exist($user_id)
    {
        $arr = $this->selectOne("SELECT count(*) as num FROM lyj_token WHERE user_id={$user_id}");
        return $arr['num'] > 0;
    }

    /**
     * @param $token
     * @return array|bool
     */
    public function read($token)
    {
        return $this->selectOne("SELECT * FROM lyj_token WHERE token='{$token}'");
    }

    /**
     * @param $user_id
     * @param $token
     * @return bool
     */
    public function save($user_id, $token)
    {
        $data = array(
            "user_id" => $user_id,
            "token" => $token,
            "update_timeline" => time(),
            "expire_timeline" => time() + $this->_expire_time
        );
//        if ($this->exist($user_id)) {
//            return $this->update("lyj_token", $data, "user_id={$user_id}");
//        } else {
//            return $this->insert("lyj_token", $data) > 0;
//        }
        return $this->insertOrUpdate("lyj_token", $data, array("token" => $token));
    }

    /**
     * @param $token
     * @return bool
     */
    public function updateTime($token)
    {
        $res = $this->read($token);
        if ($res) {
            return $this->save($res["user_id"], $token);
        }
        return FALSE;
    }

    /**
     * @param $token
     * @return bool
     */
    public function remove($token)
    {
        return $this->delete("lyj_token", "token='{$token}'");
    }

    /**
     * 根据用户ID生成token
     *
     * @param string $user_id
     * @return string
     */
    public function build($user_id = "")
    {
        $rand_str = "";
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890~!@#$%^&*()-_=+:;\"'|\\<,>.?/";
        $rand_len = 16;
        for ($i = 0; $i < $rand_len; $i++) {
            $k = rand(0, 100) % strlen($chars);
            $rand_str .= $chars[$k];
        }
        return md5(md5($user_id . microtime() . $rand_str) . $rand_str);
    }

}

