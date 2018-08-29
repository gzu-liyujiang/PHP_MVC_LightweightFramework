~~【已废弃，个人精力不在PHP方面，故不再更新维护】~~
# 关于PHP_MVC_API   
PHP_MVC_API作为一个极其轻量级的MVC&amp;API开发框架，不断吸取参考了flight、phx、discuz、punbb等开源项目的优点，最终形成独具自己风格的快速开发框架。
适合<span style="color:red;font-weight:bold;">微型项目</span>，让自己学会和巩固一些设计模式及一些必要的基础知识。目前我主要用于<span style="color:red">服务端应用程序接口</span>开发，提供API和移动APP进行数据交换。   

# 主要特性(Feature)   
* 极其轻量——核心代码主要在"libraries"目录下，几十KB，不到100KB。   
* 单一入口——所有URL都是基于index.php。格式：http://{host}/{path}/index.php?c={controller}&a={action}&key1=value1&key2=value2...，
如：http://localhost/php-develop/PHP_MVC_API/index.php?c=Article&a=detail&id=1。   
如果服务器支持URL重写，则格式亦可为：http://{host}/{path}/{controller}/{action}/key1/value1/key2/value2...，
如：http://localhost/php-develop/PHP_MVC_API/index.php/Article/detail/id/1（当然也可以改成自己的规则）。   
* 结构清晰——使用MVC模式，分三个主目录，分前台后台，分普通网页及数据接口。   
* 兼容性好——兼容php5.x，支持linux/windows+apache+mysql+php(lamp及wamp)、windows+iis+php等常见环境，安卓上的php环境也可运行。   
* 开放源码——使用GPL协议，欢迎使用、反馈并贡献您的代码。   

# 使用说明(Usage)   
1. 在install.sql中写好创建表结构及其数据的SQL;   
2. 删掉config.php，访问首页即可进行数据库表的初始化，自动创建config.php;   
3. 在/views/front/目录下写前台的html页面，backend目录下写后台页面；   
4. 在/models/目录下继承者Model类对数据库表进行增删改查；   
5. 在/controllers/front/目录下继承自FrontController类把Model中对数据库表的操作绑定到前台页面中，
backend目录下继承自BackendController把Model的数据操作绑定到后台页面中，
api目录下继承自ApiController可将数据作为json格式返回供客户端使用；   
6. 具体参见源代码。   

# 示例代码(Sample)  
### 接口(默认返回JSON格式，XML格式需自行扩展)   
see http://ime.qqtheme.cn/index.php?c=Upgrade   
```php
class UpgradeController extends ApiController
{

    public function get()
    {
        $data = array(
            'versionName' => 'V1.3.4',
            'versionCode' => 10,
            'enforce' => 0,
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
```   
### 网页(默认使用jQuery及MUI，可以自行替换)   
see http://ime.qqtheme.cn/index.php?c=link   
```sql
DROP TABLE IF EXISTS `lyj_link`;
CREATE TABLE `lyj_link` (
  `id`          INT(8) UNSIGNED  NOT NULL AUTO_INCREMENT,
  `category_id` INT(10) UNSIGNED NOT NULL,
  `name`        VARCHAR(200)     NOT NULL DEFAULT '链接名称',
  `description` VARCHAR(255)     NOT NULL DEFAULT '链接简介',
  `url`         VARCHAR(255)     NOT NULL DEFAULT 'http://',
  `icon`        VARCHAR(255)     NOT NULL DEFAULT 'http://',
  `sortby`      INT(5)           NOT NULL DEFAULT 0,
  `hidden`      INT(2)           NOT NULL DEFAULT 0,
  `timeline`    INT(8)           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COMMENT = '友链表';
```    
```php
class LinkModel extends Model
{

    public function getLinkList()
    {
        $sql = 'SELECT * FROM lyj_link ORDER BY sortby ASC,id DESC';
        return $this->selectMulti($sql);
    }

}
```   
```php
class LinkController extends FrontController {

    public function main() {
        $this -> template -> assign('title', '友情推荐');
        $model = new LinkModel();
        $this -> template -> assign('links', $model -> getLinkList());
        $this -> template -> display('LinkIndex.htm');
    }

}
```   
```html
<!--{eval $count = isset($links) ? count($links) : 0;}-->
<!--{if $count < 1}-->
<div class="mui-content">
    <br />
    <p class="center hint">暂时还没有友情链接！</p>
</div>
<!--{else}-->
<p class="center">下面的也是我们的作品，欢迎使用！</p>
<ul class="mui-table-view content">
    <!--{eval $page_size = 7;}-->
    <!--{eval $page_index = isset($_GET['page']) ? intval($_GET['page']) : 1;}-->
    <!--{eval $pager = new Pager($count, $page_index, $page_size);}-->
    <!--{for $i=$pager->getStartNum(); $i<=$pager->getEndNum(); $i++}-->
    <li class="mui-table-view-cell mui-media">
        <a href="{$links[$i-1]['url']}" target="_blank" id="link_{$links[$i-1]['id']}">
            <div class="mui-media-body mui-pull-left">
                <!--{$i}-->、
                <!--{eval echo Ubb::decode($links[$i-1]['name']);}-->
                <p class='mui-ellipsis'>
                    <!--{$links[$i-1]['category_name']}-->
                </p>
            </div>
            <img class="mui-media-object mui-pull-right" src="{$links[$i-1]['icon']}" width="50" height="40">
        </a>
    </li>
    <!--{/for}-->
</ul>
<!--{if $count > $page_size}-->
<ul class="mui-pager">
    <li>
        <!--{$pager->getPrevPage()}-->
    </li>
    <li>
        <!--{$pager->getNextPage()}-->
    </li>
    <li>
        <span><!--{$page_index}-->/<!--{$pager->getPageNum()}--></span>
    </li>
</ul>
<!--{/if}-->
<!--{/if}-->
```   

# 参考链接(Links)   
Demo, wap site: http://ime.qqtheme.cn/?c=article   
<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=1032694760&site=穿青人&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:1032694760:51" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>
<a target="_blank" href="http://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=q8fC0t7BwsrFzIXfwOva2oXIxMY" style="text-decoration:none;"><img src="http://rescdn.qqmail.com/zh_CN/htmledition/images/function/qm_open/ico_mailme_02.png"/></a>

