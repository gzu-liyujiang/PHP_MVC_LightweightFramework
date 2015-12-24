<?php

final class UserModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $id
     * @return array|bool
     */
    public function findById($id)
    {
        return $this->selectOne("SELECT * FROM lyj_user WHERE id={$id}");
    }

    /**
     * @param $account
     * @return array|bool
     */
    public function findByAccount($account)
    {
        return $this->selectOne("SELECT * FROM lyj_user WHERE account='{$account}'");
    }

    /**
     * @param $device_id
     * @return array|bool
     */
    public function findByDeviceId($device_id)
    {
        return $this->selectOne("SELECT * FROM lyj_user WHERE device_id='{$device_id}'");
    }

    /**
     * @param string $device_ids 用逗号分隔
     * @return array|bool
     */
    public function findByDeviceIds($device_ids = '')
    {
        if (empty($device_ids)) {
            return false;
        }
        if (is_array($device_ids)) {
            $strIds = implode(',', $device_ids);
        } else {
            $strIds = $device_ids;
        }
        return $this->selectMulti("SELECT * FROM lyj_user WHERE device_id IN({$strIds})");
    }

    /**
     * @param $token
     * @return array|bool
     */
    public function findByToken($token)
    {
        return $this->selectOne("SELECT a.* FROM lyj_user a JOIN lyj_token b ON a.id=b.user_id WHERE b.token='{$token}'");
    }

    /**
     * @param $data
     * @return int
     */
    public function add($data)
    {
        return $this->insert("lyj_user", $data);
    }

    /**
     * @param $data
     * @return bool
     */
    public function modify($data)
    {
        $where = 'id=0';
        if (isset($data['account'])) {
            $where = "account='{$data['account']}'";
        } else if (isset($data['id'])) {
            $where = 'id=' . $data['id'];
        } else if (isset($data['device_id'])) {
            $where = "device_id='{$data['device_id']}'";
        }
        return $this->update("lyj_user", $data, $where);
    }

    /**
     * @param $password
     * @return string
     */
    public function buildPassword($password)
    {
        //加入私有文字作密钥，防暴力破解
        //return md5(sha1($password) . sha1('李玉江'));
        return md5($password);
    }

}

