<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-14 下午3:02
 */
class UpgradeController extends ApiController
{

    /**
     * 返回的参数说明：
     * patch——是否补丁包
     * enforce——是否强制下载最新版本
     * versionName——最新版本名称
     * versionCode——最新版本代号
     * content——最新版本说明
     * url——最新版本安装包
     */
    public function get()
    {
        $data = array(
            'patch' => 0,
            'enforce' => 0,
            'versionName' => 'V1.3.4',
            'versionCode' => 10,
            'content' => 'V1.3.4，建议升级：
            支持导入已使用过的讯飞、搜狗及百度皮肤；
            支持直接编辑已制作好的讯飞、搜狗及百度皮肤；
            支持普通按键、功能键及拼音输入区背景单独设置；
            修复讯飞皮肤候选栏及拼音区文字颜色总是为黑色的bug；
            修复搜狗皮肤拼音区文字颜色设置无效的bug；
            讯飞及搜狗皮肤拼音区背景颜色可选择完全透明；
            其他一些改进功能改进及界面调整。',
            'url' => 'http://ime.qqtheme.cn'
        );
        $this->responseJson(1, "获取数据成功", $data);
    }

    public function post()
    {
        $this->get();
    }

}
