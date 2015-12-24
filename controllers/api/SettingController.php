<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2015/7/25
 * Created by IntelliJ IDEA
 */
final class SettingController extends ApiController
{

    public function main()
    {
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
        $tokenController = new TokenController();
        if ($tokenController->check($token)) {
            $contact = addslashes(urldecode($_REQUEST['contact']));
            $seo_title = addslashes(urldecode($_REQUEST['seo_title']));
            $seo_keywords = addslashes(urldecode($_REQUEST['seo_keywords']));
            $seo_description = addslashes(urldecode($_REQUEST['seo_description']));
            $copyright = addslashes(urldecode($_REQUEST['copyright']));
            $data = array(
                'contact' => $contact,
                'seo_title' => $seo_title,
                'seo_keywords' => $seo_keywords,
                'seo_description' => $seo_description,
                'copyright' => $copyright
            );
            $model = new SettingModel();
            if ($model->saveValue($data)) {
                $this->responseJson(1, "修改成功");
            } else {
                $this->responseJson(0, "修改失败");
            }
        } else {
            $this->responseJson(0, "请先登录");
        }
    }

}