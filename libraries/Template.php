<?php
/**
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 */

/**
 * 仿Discuz模板类
 * 用法：
 * $tpl = Template::getInstance();
 * $tpl->assign("str","我是字符串啦啦啦");
 * $tpl->assign("a",array('dasdasd'.'bbbbbbb','cccccccccccccc'));
 * $tpl->display("index.tpl");
 * 语法：
 * 变量输出：{$str}或{echo $str}
 * 数组遍历：{foreach $arr $val}{$val}{/foreach}或{foreach $arr $key $val }{$val}{/foreach}或{for $i=0;$i<10;$i++}{$i}{/for}
 * 条件判断：{if xxx}...{elseif yyy}...{else}...{/if}
 * 子模板包含：{template xxx/yyy.html}
 * PHP代码执行：{eval $phpcode}
 */
class Template {
	const COMPILED_FILE_EXT = '.php';
	//编译后的文件后缀
	private static $instance;
	//本类实例
	private $templateDir;
	//模板目录，必须以“/”结尾
	private $compiledDir;
	//编译缓存目录
	private $templateFile;
	//模板文件
	private $compiledFile;
	//编译文件
	private $vars = array();
	//存储所有模板变量

	private function __construct() {
		$this -> templateDir = ROOT_PATH . '/views/template/';
		$this -> compiledDir = ROOT_PATH . '/views/compiled/';
		$this -> assign('title', '无标题');
		$this -> assign('author', '李玉江，QQ:1032694760');
		$this -> assign('keywords', '');
		$this -> assign('description', 'Powered by 李玉江，MVC模式的单一入口的API框架');
		$this -> assign('copyright', '');
	}

	/**
	 * 获取本类的实例
	 *
	 * @return Template
	 */
	public static function getInstance() {
		if (!(self::$instance instanceof self)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function setTemplateDir($dirPath) {
		$this -> templateDir = $dirPath;
	}

	public function setCompiledDir($dirPath) {
		$this -> compiledDir = $dirPath;
	}

	/**
	 * 向模板分配变量
	 * @param mixed $key
	 * @param string $val 可选，默认值为“null”，如果已赋值，则将$key作为数组的键而将其作为值
	 */
	public function assign($key, $val = null) {
		if (null != $val) {
			$this -> vars[$key] = $val;
		} else {
			if (is_array($key)) {
				$this -> vars = array_merge($this -> vars, $key);
			}
		}
	}

	/**
	 * 移除模板中分配的某个变量
	 * @param mixed $key
	 */
	public function remove($key) {
		if (array_key_exists($key, $this -> vars)) {
			unset($this -> vars[$key]);
		}
	}

	public function display($templateFile) {
		$this -> templateDir = rtrim($this -> templateDir, '/') . '/';
		$this -> compiledDir = rtrim($this -> compiledDir, '/') . '/';
		if (!is_dir($this -> compiledDir)) {
			mkdir($this -> compiledDir, 0755);
		}
		$this -> templateFile = $this -> templateDir . $templateFile;
		$this -> compiledFile = $this -> compiledDir . md5($this -> templateFile) . self::COMPILED_FILE_EXT;
		$templateContent = $this -> loadTemplate($templateFile);
		//判断模板是否已存在或已被修改
		if ((!is_file($this -> compiledFile)) || (filemtime($this -> compiledFile) < filemtime($this -> templateFile))) {
			$this -> compile($templateContent);
		}
		extract($this -> vars, EXTR_SKIP);
		/** @noinspection PhpIncludeInspection */
		include_once ($this -> compiledFile);
		Flight::getInstance() -> stop();
	}

	public function fetch($templateFile) {
		ob_start();
		$this -> display($templateFile);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	private function compile($template) {
		//{template 子模板路径}，因子模板内可能含有模板标签，故必须在所有模板标签之前解析
		$template = preg_replace("/[\n\r\t]*(\<\!\-\-)?\{template\s+([a-z0-9_:\.\/]+)\}(\-\-\>)?[\n\r\t]*/ies", "\$this->loadTemplate('\\2')", $template);
		//<!--{模板标签内容}-->
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
		//{eval PHP代码片段}
		$template = preg_replace("/\{eval\s+(.*?);?\}/is", "<?php \\1; ?>", $template);
		//{for 条件}
		$template = preg_replace("/\{for\s+(.*?)\}/is", "<?php for (\\1) {?>", $template);
		//{/for}
		$template = preg_replace("/\{\/for\}/is", "<?php } ?>", $template);
		//{if 条件}
		$template = preg_replace("/\{if\s+(.+?)\}/is", "<?php if (\\1) { ?>", $template);
		//{elseif 条件}
		$template = preg_replace("/\{elseif\s+(.+?)\}/is", "<?php } else if (\\1) { ?>", $template);
		//{else}
		$template = preg_replace("/\{else\}/is", "<?php } else { ?>", $template);
		//{/if}
		$template = preg_replace("/\{\/if\}/is", "<?php } ?>", $template);
		//{foreach 条件}
		$template = preg_replace("/\{foreach\s+(.*?)\}/is", "<?php foreach (\\1) { ?>", $template);
		//{/foreach}
		$template = preg_replace("/\{\/foreach\}/is", "<?php } ?>", $template);
		//{变量},{数组},{常量},{方法},因“{else}”也符合常量条件，故该局必须在{else}标签之后解析
		$template = preg_replace("/\{([\$a-z0-9_\-\[\]'\"\>\(\):]+)\}/is", "<?php echo \\1; ?>", $template);
		$template = "$template\n" . '<!-- Created By Template Engineer At ' . date('Y/m/d,H:i') . ' -->' . "\n";
		try {
			file_put_contents($this -> compiledFile, $template);
		} catch (Exception $e) {
			echo '无法创建模板缓存文件“' . $this -> compiledFile . '”。' . (IS_DEVELOP ? $e -> getMessage() : '');
		}
	}

	private function loadTemplate($templateFile) {
		$file = $this -> templateDir . $templateFile;
		if (!is_file($file)) {
			return '不存在模板“' . $file . '”。';
		} else {
			return file_get_contents($file);
		}
	}

	public function __destruct() {
		unset($this -> vars);
	}

}
?>