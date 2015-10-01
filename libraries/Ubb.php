<?php
/**
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 */

/**
 * UBB类
 * 改自elliott [at] (2004-08-30)的UBB类
 * 下载于http://blog.chinaunix.net/uid-18962755-id-2808365.html
 */
class Ubb
{
    private $pattern = array(); //存放UBB标签的数组
    private $replace = array(); //存放HTML标签的数组

    private function __construct()
    {
        $this->initTable();
        $this->initFont();
        $this->initLink();
        $this->initCode();
    }

    //分析表格类标签.
    private function initTable()
    {
        $pattern = array(
            '/\[table\]\s*(.+?)\[\/table\]/is',
            '/\[tr\]\s*(.+?)\[\/tr\]/is',
            '/\[td\]\s*(.+?)\[\/td\]/is',
            '/\[th\]\s*(.+?)\[\/th\]/is',
            '/\[td\s?row=\s*(.+?)\]\s*(.+?)\[\/td\]/is',
            '/\[td\s?col=\s*(.+?)\]\s*(.+?)\[\/td]/is',
            '/\[th\s?row=\s*(.+?)\]\s*(.+?)\[\/th\]/is',
            '/\[th\s?col=\s*(.+?)\]\s*(.+?)\[\/th]/is',
            '/\[ul\]\s*(.+?)\[\/ul\]/is',
            '/\[li\]\s*(.+?)\[\/li\]/is',
            '/\[ol=1\]\s*(.+?)\[\/ol\]/is',
            '/\[ol=i\]\s*(.+?)\[\/ol\]/is',
            '/\[ol=I\]\s*(.+?)\[\/ol\]/is',
            '/\[ol=a\]\s*(.+?)\[\/ol\]/is',
            '/\[ol=A\]\s*(.+?)\[\/ol\]/is'
        );
        $this->pattern = array_merge($this->pattern, $pattern);
        $replace = array(
            '<table border="1">\\1</table>',
            '<tr>\\1</tr>',
            '<td>\\1</td>',
            '<th>\\1</th>',
            '<td rowspan="\\1">\\2</td>',
            '<td colspan="\\1">\\2</td>',
            '<th rowspan="\\1">\\2</th>',
            '<th colspan="\\1">\\2</th>',
            '<ul>\\1</ul>',
            '<li>\\1</li>',
            '<ol style="list-style-type:decimal;">\\1</ol>',
            '<ol style="list-style-type:lower-roman;">\\1</ol>',
            '<ol style="list-style-type:upper-roman;">\\1</ol>',
            '<ol style="list-style-type:lower-alpha;">\\1</ol>',
            '<ol style="list-style-type:upper-alpha;">\\1</ol>'
        );
        $this->replace = array_merge($this->replace, $replace);
    }

    //分析字体类标签,如字体大小,颜色,移动等
    private function initFont()
    {
        $pattern = array(
            '/\[h1\]\s*(.+?)\[\/h1\]/is',
            '/\[h2\]\s*(.+?)\[\/h2\]/is',
            '/\[h3\]\s*(.+?)\[\/h3\]/is',
            '/\[h4\]\s*(.+?)\[\/h4\]/is',
            '/\[h5\]\s*(.+?)\[\/h5\]/is',
            '/\[h6\]\s*(.+?)\[\/h6\]/is',
            '/\[b\]\s*(.+?)\[\/b\]/is',
            '/\[u\]\s*(.+?)\[\/u\]/is',
            '/\[i\]\s*(.+?)\[\/i\]/is',
            '/\[s\]\s*(.+?)\[\/s\]/is',
            '/\[size=\s*(.+?)\]\s*(.+?)\[\/size\]/is',
            '/\[big\]\s*(.+?)\[\/big\]/is',
            '/\[large\]\s*(.+?)\[\/large\]/is',
            '/\[small\]\s*(.+?)\[\/small\]/is',
            '/\[color=\s*(.+?)\]\s*(.+?)\[\/color]/is',
            '/\[font=\s*(.+?)\]\s*(.+?)\[\/font\]/is',
            '/\[align\s*=\s*(\S+?)\s*\]\s*(.+?)\[\/align\]/is',
            '/\[center\]\s*(.+?)\[\/center\]/is',
            '/\[fly\]\s*(.+?)\[\/fly\]/is'
        );
        $this->pattern = array_merge($this->pattern, $pattern);
        $replace = array(
            '<h1>\\1</h1>',
            '<h2>\\1</h2>',
            '<h3>\\1</h3>',
            '<h4>\\1</h4>',
            '<h5>\\1</h5>',
            '<h6>\\1</h6>',
            '<b>\\1</b>',
            '<u>\\1</u>',
            '<i>\\1</i>',
            '<s>\\1</s>',
            '<span style="font-size:\\1">\\2</span>',
            '<span style="font-size:x-large">\\1</span>',
            '<span style="font-size:large">\\1</span>',
            '<span style="font-size:small">\\1</span>',
            '<span style="color:\\1">\\2</span>',
            '<span style="font-family:\\1">\\2</span>',
            '<div align="\\1">\\2</div>',
            '<center>\\1</center>',
            '<marquee>\\1</marquee>'
        );
        $this->replace = array_merge($this->replace, $replace);
    }

    //分析代码类标签,如引用,例子,签名等
    private function initCode()
    {
        $pattern = array(
            '/\[space\]/i',
            '/\[tab\]/i',
            '/\[br\]/i',
            '/\[br\/\]/i',
            '/\[p\]/i',
            '/\[\/p\]/i',
            '/\[hr\]/i',
            '/\[code\]\s*(.+?)\[\/code\]/is'
        );
        $this->pattern = array_merge($this->pattern, $pattern);
        $replace = array(
            '&nbsp;',
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            '<br />',
            '<br />',
            '<p>',
            '</p>',
            '<hr style="width: 99%;color: #cccccc;border: 0.1px dotted #cccccc;">',
            '<div style="background-color:#eeeeee"><pre>\\1</pre></div>'
        );
        $this->replace = array_merge($this->replace, $replace);
    }

    //分析连接类标签,如网址,邮箱,图片等
    private function initLink()
    {
        $pattern = array(
            '/\[url\]\s*(.+?)\[\/url\]/is',
            '/\[url=\s*(.+?)\]\s*(.+?)\[\/url\]/is',
            '/\[ed\]\s*(.+?)\[\/ed\]/is',
            '/\[email\]\s*(.+?)\[\/email\]/is',
            '/\[email=\s*(.+?)\]\s*(.+?)\[\/email\]/is',
            '/\[img\]\s*(.+?)\[\/img\]/is',
            '/\[limg\]\s*(.+?)\[\/limg\]/is',
            '/\[cimg\]\s*(.+?)\[\/cimg\]/is',
            '/\[rimg\]\s*(.+?)\[\/rimg\]/is',
            '/\[img=(\d+),\s*(\d+)\]\s*(.+?)\[\/img\]/is'
        );
        $this->pattern = array_merge($this->pattern, $pattern);
        $replace = array(
            '<a href="\\1" target="_blank">\\1</a>',
            '<a href="\\1" target="_blank">\\2</a>',
            '<a href="\\1" target="_blank"><b>\\2</b></a>',
            '<a href="mailto:\\1">\\1</a>',
            '<a href="mailto:\\1">\\2</a>',
            '<img src="\\1" border="0" onload="if(this.width>500) this.width=500;this.title=\'看原大图\';" onmouseover="if(this.title) this.style.cursor=\'hand\';"; onclick="if(this.title) window.open(\'\\1\');">',
            '<img src="\\1" align="left" border="0" onload="if(this.width>500) this.width=500;this.title=\'看原大图\';" onmouseover="if(this.title) this.style.cursor=\'hand\';"; onclick="if(this.title) window.open(\'\\1\');">',
            '<div align="center"><img src="\\1" border="0" onload="if(this.width>500) this.width=500;this.title=\'看原大图\';" onmouseover="if(this.title) this.style.cursor=\'hand\';"; onclick="if(this.title) window.open(\'\\1\');"></div>',
            '<img src="\\1" align="right" border="0" onload="if(this.width>500) this.width=500;this.title=\'看原大图\';" onmouseover="if(this.title) this.style.cursor=\'hand\';"; onclick="if(this.title) window.open(\'\\1\');">',
            '<img src="\\3" border="0" height="\\2" width="\\1" onclick="if(this.title) window.open(\'\\3\');">'
        );
        $this->replace = array_merge($this->replace, $replace);
    }

    /**
     * UBB转为HTML
     * @param string $str 待解析的字符串
     * @return string
     */
    public static function decode($str)
    {
        //为兼容KindEditor，需要保留换行及跳格符
        if (preg_match("/\[exec\]\s*(.+?)\[\/exec\]/is", $str, $code)) {
            $code[1] = str_replace('&nbsp;', ' ', htmlspecialchars_decode($code[1])); //还原HTML代码
            $code[1] = str_replace("<br />", "\n", $code[1]); //还原代码中的换行
            //exit($code[1]);
            $str = preg_replace("/\[exec\]\s*(.+?)\[\/exec\]/is", $code[1], $str);
        }
        $ubb = new self();
        $str = preg_replace($ubb->pattern, $ubb->replace, $str);
        return $str;
    }

    /**
     * 显示所有UBB标签
     */
    public static function help()
    {
        $ubb = new self();
        foreach ($ubb->pattern as $k => $v) {
            $k = $k + 1;
            $pattern = array('/\[', '\s*(.+?)', '\]', '\[', ']/is', ']/i', '\/', '\s?');
            $replace = array('[', '内容', ']', '[', ']', ']', '/', ' ');
            $v = str_replace($pattern, $replace, $v);
            echo $k . '、' . $v . '<br />';
        }
        echo ($k + 1) . '、[exec]内容[/exec]';
    }


}

?>