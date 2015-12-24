<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2015/4/14
 * Created by IntelliJ IDEA
 */
class AnnounceController extends ApiController
{

    /**
     * 返回的参数说明：
     * type——公告类型：0-文本、1-图片、2-音乐、3-SVG
     * icon——图标，png、jpg或gif，为空的话客户端将默认为APP图标
     * position——图标位置：0-8，依次为左上、左中、左下、顶中、正中、底中、右上、右中、右下
     * title——标题
     * content——内容
     * url——type为文本及图片时代表http、mqqwpa、tel、mailto等地址，type为音乐或SVG时代表音频文件或SVG文件地址
     * expire——有效期，单位为小时
     */
    public function get()
    {
        $base_url = Flight::getInstance()->get('base_url');
        $mouth = intval(date('m'));
        $day = intval(date('d'));
        $type = 3;
        $icon = '';
        $postion = 5;
        $title = '温馨提示';
        $content = '在使用过程中，如果出现奔溃，建议点击发送日志给开发者以便帮助改善软件……';
        $url = $base_url . '/data/announce/default.svg';
        $expire = 1;
        if ($mouth==9 && $day>24 && $day<28) {
            $title = '祝中秋快乐';
            $icon = $base_url . '/data/announce/zhongqiu.gif';
            $expire = 3*24;
        } else if ($mouth==10 && $day>1 && $day<9) {
            $title = '祝国庆快乐';
            $icon = $base_url . '/data/announce/guoqing.gif';
            $expire = 7*24;
        } else if ($mouth==10 && $day==21) {
            $title = '祝重阳快乐';
            $icon = $base_url . '/data/announce/chongyang.gif';
            $expire = 24;
        } else if ($mouth==11 && $day==11) {
            $title = '祝单身快乐';
            $icon = $base_url . '/data/announce/guanggun.gif';
            $expire = 24;
        } else if ($mouth==11 && $day==26) {
            $title = '拥有感恩的心';
            $icon = $base_url . '/data/announce/ganen.gif';
            $expire = 24;
        } else if ($mouth==12 && $day>23 && $day<25) {
            $title = '祝圣诞快乐';
            //$icon = $base_url . '/data/announce/shengdan.gif';
            $expire = 2*24;
        } else {
            $icon = '';
            $expire = 10*24;
        }
        $data = array(
            'type' => $type,
            'icon' => $icon,
            'postion' => $postion,
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'expire' => $expire,
        );
        $this->responseJson(1, "获取数据成功", $data);
    }

}

