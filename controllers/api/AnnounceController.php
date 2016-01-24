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
     * random——和客户端比较，用于强制更新
     * type——公告类型：0-文本、1-动画、2-网页、3-下载文件、4-打开第三方应用
     * icon——图标，png、jpg或gif，为空的话客户端将默认为APP图标
     * position——图标位置：0-5，依次为左上、左中、左下、右上、右中、右下
     * title——标题
     * content——内容
     * url——type为动画时代表客户端对应的动画方法，如：shake、flash、wave、path、svg等，
            否则代表http、intent、mqqwpa、tel、mailto等地址
     * expire——有效期，单位为小时
     */
    public function get()
    {
        $base_url = Flight::getInstance()->get('base_url');
        $mouth = intval(date('m'));
        $day = intval(date('d'));
        $random = 1234567000;
        $type = 1;
        $icon = $base_url . '/data/announce/default.gif';
        $position = 4;
        $title = '温馨提示';
        $content = '在使用过程中，如果出现奔溃，建议点击发送日志给开发者以便帮助改善软件……';
        $url = 'bubble://duration=3000&startDelay=0&repeatCount=-1&repeatMode=restart&interpolator=LinearInterpolator&action=http://wap.qqtheme.cn';
        //$url = 'svg://repeatCount=5&path=M 42.266949,70.444915 C 87.351695,30.995763 104.25847,28.177966 104.25847,28.177966 l 87.3517,36.631356 8.45339,14.088983 L 166.25,104.25847 50.720339,140.88983 c 0,0 -45.0847458,180.33898 -39.449153,194.42797 5.635594,14.08898 67.627119,183.15678 67.627119,183.15678 l 16.90678,81.7161 c 0,0 98.622885,19.72457 115.529665,22.54237 16.90678,2.8178 70.44491,-22.54237 78.8983,-33.81356 8.45339,-11.27118 76.08051,-107.07627 33.81356,-126.80085 -42.26695,-19.72457 -132.43644,-56.35593 -132.43644,-56.35593 0,0 -33.81356,-73.26271 -19.72458,-73.26271 14.08899,0 132.43644,73.26271 138.07204,33.81356 5.63559,-39.44915 19.72457,-169.0678 19.72457,-169.0678 0,0 28.17797,-25.36017 -28.17796,-19.72457 -56.35593,5.63559 -95.80509,11.27118 -95.80509,11.27118 l 42.26695,-87.35169 8.45339,-28.177968';
        $expire = 7 * 24;
        $data = array(
            'random' => $random,
            'type' => $type,
            'icon' => $icon,
            'position' => $position,
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'expire' => $expire,
        );
        $this->responseJson(1, "获取数据成功", $data);
    }

}

