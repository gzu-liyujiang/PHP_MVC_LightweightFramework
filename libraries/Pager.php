<?php
/**
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 */

/**
 * 分页类
 */
class Pager
{
	private $total; //总记录条数或总字数
	private $page; //当前第几页
	private $num; //每页显示的记录条数或每页显示的字数
	private $offset; //页数偏移量
	private $url; //当前页面所在的地址（包含页码）

	/**
	 *
	 * @param int $total 总共有几条数据记录或总共有多少字
	 * @param int $page 当前第几页
	 * @param int $num 每页显示几条记录或每页显示多少字
	 */
	public function __construct($total, $page, $num=5) {
		$this->total = $total;
		$this->page = $page;
		$this->num = $num;
		$this->offset = $this->getOffset();
		$this->url = $this->getPageUrl();
	}

	/**
	 * 页数偏移量
	 * @return number
	 */
	public function getOffset() {
		return ($this->page - 1) * $this->num;
	}

	public function getPageUrl() {
		if (!isset($_SERVER['REQUEST_URI'])) {
			$url = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
		} else {
			$url = $_SERVER['REQUEST_URI'];
		}
		$url = preg_replace("/(.*)(&|\?)page=\d+/i", "\\1", $url);
		$delimiter = (false == strpos($url, '?')) ? '?' : '&';
		return $url . $delimiter . 'page=';
	}
	/**
	 * 总共多少页
	 * @return number
	 */
	public function getPageNum() {
		return ceil($this->total / $this->num);
	}

	/**
	 * @return number
	 */
	public function getStartNum() {
		if ($this->total == 0) {
			return 0;
		} else {
			return $this->offset + 1;
		}
	}

	/**
	 * @return number
	 */
	public function getEndNum() {
		return min($this->offset + $this->num, $this->total);
	}

	/**
	 * 首页
	 * @return string
	 */
	public function getFirstPage() {
 		if($this->page == 1) {
			$str = '<span>首页</span>';
		} else {
 			$str = '<a href="'.$this->url.'1">首页</a>';
 		}
 		return $str;
	}

	/**
	 * 尾页
	 * @return string
	 */
	public function getLastPage() {
 		if($this->page == $this->getPageNum()) {
			$str = '<span>尾页</span>';
		}else {
			$str = '<a href="'.$this->url.$this->getPageNum().'">尾页</a>';
		}
		return $str;
	}

	/**
	 * 上一页
	 * @return string
	 */
	public function getPrevPage() {
		if($this->page == 1) {
			$str = '&nbsp;<span>上页</span>&nbsp;';
		} else {
			$str = '<a href="'.$this->url.($this->page - 1).'">&nbsp;上页&nbsp;</a>';
		}
		return $str;
	}

	/**
	 * 下一页
	 * @return string
	 */
	public function getNextPage() {
		if($this->page == $this->getPageNum()) {
			$str = '&nbsp;<span>下页</span>&nbsp;';
		}else {
			$str = '<a href="'.$this->url.($this->page + 1).'">&nbsp;下页&nbsp;</a>';
		}
		return $str;
	}
	
	/**
	 * 显示分页链接
	 */
	public function show() {
		echo '<p>'.$this->getFirstPage().$this->getPrevPage().$this->getNextPage().$this->getLastPage().'</p>';
	}

}
?>