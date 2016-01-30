<?php

/**
 * Created by PhpStorm.
 * Author: 李玉江[QQ:1032694760]
 * Date: 2015-04-30 下午5:02
 */
class InstallController extends Controller
{

    /**
     * 从模型中获取数据并分配到视图中去
     * 每个控制器子类必须实现该抽象方法
     * 相当C语言中的main函数及Java中的main方法
     */
    public function main()
    {
        if ($this->isPost()) {
            $this->installData();
        } else {
            $this->displayUi();
        }
    }

    private function displayUi()
    {
        if (file_exists(ROOT_PATH . '/config.php')) {
            exit("oops, it installed!");
        }
        $template = Template::getInstance();
        $template->setTemplateDir(ROOT_PATH . '/views/backend/');
        $template->assign('title', '安装向导');
        $template->remove('copyright');
        $template->display("Install.htm");
    }

    private function installData()
    {
        if (empty($_POST['db_host']) or empty($_POST['db_user']) or empty($_POST['db_name'])) {
            exit('数据库服务器、用户名及数据库名称不能为空。');
        }
        $db_type = trim($_POST['db_type']);
        $db_host = trim($_POST['db_host']);
        $db_port = intval(trim($_POST['db_port']));
        $db_user = trim($_POST['db_user']);
        $db_pass = trim($_POST['db_pass']);
        $db_name = trim($_POST['db_name']);
        $config = array("base_url" => dirname(Helper::currentUrl()), "db.type" => $db_type, "db.host" => $db_host, "db.port" => $db_port, "db.user" => $db_user, "db.pass" => $db_pass, "db.name" => $db_name);
        if ($db_type == 'mysqli') {
            $db = MysqliDb::getInstance();
        } else {
            $db = MysqlDb::getInstance();
        }
        $db->connect($db_host, $db_port, $db_user, $db_pass, $db_name);
        $sql = '';
        $files = scandir(ROOT_PATH);
        foreach ($files as $file) {
            if (false != stripos($file, '.sql')) {
                $sql .= @file_get_contents($file);
                //合并SQL文件
            }
        }
        $sql = preg_replace('/--.*?\n/U', '', $sql);
        //删掉注释
        $sql = rtrim($sql, ";\n \t\r\0\x0B");
        //删掉最后一条SQL语句的逗号及换行、空格、跳格、回车等字符，以防止产生空语句
        Logger::getInstance('sql')->debug($sql);
        $arr = explode(';', $sql);
        //切割语句
        $num = count($arr);
        $err = '';
        //存储执行出错的语句
        $suc = 0;
        //存储成功执行的语句数目
        for ($i = 0; $i < $num; $i++) {
            $s = trim($arr[$i]);
            if ($db->query($s)) {
                $suc++;
                echo '<span style="color:green">成功</span>：' . $s . '<br />';
            } else {
                $err .= $s . '<br />';
                echo '<span style="color:red">失败</span>：' . $s . '【' . $db->error() . '】<br />';
            }
        }
        $exe = '总共有' . $num . '条SQL语句，成功执行了<span style="color:green">' . $suc . '条</span>，出错<span style="color:red">' . ($num - $suc) . '条</span>。<br />';
        if ($err != '') {
            echo $exe . '<span style="color:red">警告！无法正常安装程序。</span>以下SQL语句无法执行：<br />' . $err;
        } else {
            $cfg = "<?php\n/**\n*本配置文件由安装程序自动生成，不可轻易修改\n*/\n\n";
            $cfg .= 'return ' . var_export($config, true) . "\n";
            $cfg .= "?>";
            $fp = fopen(ROOT_PATH . '/config.php', 'w');
            fwrite($fp, $cfg);
            fclose($fp);
            echo $exe . '恭喜，安装成功！';
            Flight::getInstance()->stop();
        }
    }

}
