<?php

final class AppTokenModel extends Model
{

    /**
     * @param $app_id
     * @return bool
     */
    public function exist($app_id)
    {
        $arr = $this->selectOne("SELECT count(*) as num FROM lyj_app_token WHERE app_id={$app_id}");
        return $arr['num'] > 0;
    }

    /**
     * @param $token
     * @return array|bool
     */
    public function read($token)
    {
        return $this->selectOne("SELECT * FROM lyj_app_token WHERE token='{$token}'");
    }

    /**
     * @param $app_id
     * @param $token
     * @return bool
     */
    public function save($app_id, $token)
    {
        $data = array(
            "app_id" => $app_id,
            "token" => $token,
            "update_timeline" => time()
        );
//        if ($this->exist($app_id)) {
//            return $this->update("lyj_token", $data, "app_id={$app_id}");
//        } else {
//            return $this->insert("lyj_token", $data) > 0;
//        }
        return $this->insertOrUpdate("lyj_app_token", $data, array("token" => $token));
    }

    /**
     * @param $token
     * @return bool
     */
    public function updateTime($token)
    {
        $res = $this->read($token);
        if ($res) {
            return $this->save($res["app_id"], $token);
        }
        return FALSE;
    }

    /**
     * @param $token
     * @return bool
     */
    public function remove($token)
    {
        return $this->delete("lyj_app_token", "token='{$token}'");
    }

    /**
     * 根据APP ID生成token
     *
     * @param string $app_id
     * @return string
     */
    public function build($app_id = "")
    {
        return md5(md5($app_id . 'liyujiang') . time());
    }

}

