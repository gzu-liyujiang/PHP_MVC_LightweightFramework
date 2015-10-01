# 关于PHP_MVC_REST
PHP_MVC_REST作为一个极其轻量级MVC&amp;API开发框架，是本人多年的php开发经验总结，不断吸取参考了flight、phx、discuz、punbb等开源项目的优点，最终形成独具自己风格的快速开发框架。

# 主要特性(Main Features)
极其轻量——核心代码不到100KB。
Lightweight - Less than 100KB.
结构清晰——使用MVC模式，分三个主目录，分前台后台，分普通网页及API。
Constructer - Include models/views/controllers, backend/front, js/css/img, html/json...
易学易用——按照示例依样画葫芦，相信很快就能上手。
SoEasy - Extremely easy to learn and use.
兼容性好——支持linux/windows+apache+mysql+php(lamp及windows)、windows+iis+php、android+lighttp+mysql+php(almp)等常见环境。
Compatible - Support linux/windows+apache+mysql+php(lamp及windows)，windows+iis+php，android+lighttp+mysql+php(almp) and more.
免费开源——使用GPL协议，欢迎使用。
Free - Under GPL license, you can use it anywhere if you want.

# 使用说明(Get Started)
1.在install.sql中写好创建表结构及其数据的SQL;
2.删掉config.php，访问首页即可进行数据库表的初始化;
3.在/views/front/目录下写前台的html页面，backend目录下写后台页面；
4.在/models/目录下继承者Model类对数据库表进行增删改查；
5.在/controllers/front/目录下继承自FrontController类把Model中对数据库表的操作绑定到前台页面中，backend目录下继承自BackendController把Model的数据操作绑定到后台页面中，api目录下继承自ApiController可将数据作为json格式返回供客户端使用；
6.具体参见源代码。

# 参考链接(Links)
Demo website: http://ime.qqtheme.cn
Contact me: QQ:103269470, Email:liyujiang_tk@yeah.net
