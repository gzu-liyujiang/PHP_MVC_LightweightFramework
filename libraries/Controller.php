<?php

/**
 * 通用控制器
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 */
abstract class Controller {

	/**
	 * 从模型中获取数据并分配到视图中去
	 * 每个控制器子类必须实现该抽象方法
	 * 相当C语言中的main函数及Java中的main方法
	 */
	public abstract function main();

	/**
	 * 是否POST请求
	 *
	 * @return bool
	 */
	protected function isPost() {
		if (isset($_SERVER['REQUEST_METHOD'])) {
			$method = $_SERVER['REQUEST_METHOD'];
		} else if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
			$method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		} else if (isset($_REQUEST['_method'])) {
			$method = $_REQUEST['_method'];
		} else {
			$method = 'GET';
		}
		return strtoupper($method) == "POST";
	}

}
?>