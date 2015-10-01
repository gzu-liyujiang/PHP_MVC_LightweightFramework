String.prototype.htmlEncode = function(str) {
	str = str.replace(/</g, "&lt;");
	str = str.replace(/>/g, "&gt;");
	str = str.replace(/&lt;br\s?\/?&gt;/g, "<br />"); //保留换行
	return str;
}
String.prototype.htmlDecode = function(str) {
	str = str.replace(/&lt;/g, "<");
	str = str.replace(/&gt;/g, ">");
	str = str.replace(/&quot;/g, "\"");
	str = str.replace(/&nbsp;/g, " ");
	str = str.replace(/&amp;/g, "&"); //以上几句用于解码HTML标签及其内嵌的CSS
	return str;
}

/**
 * 字符串处理扩展，参见http://blog.sina.com.cn/s/blog_670884360100mz22.html
 */
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "");　　
}　　
String.prototype.ltrim = function() {
	return this.replace(/(^\s*)/g, "");
}　　
String.prototype.rtrim = function() {
	return this.replace(/(\s*$)/g, "");　　
}
String.prototype.endWith = function(str) {
	if (str == null || str == "" || this.length == 0 || str.length > this.length)
		return false;
	if (this.substring(this.length - str.length) == str)
		return true;
	else
		return false;
	return true;
}
String.prototype.startWith = function(str) {
	if (str == null || str == "" || this.length == 0 || str.length > this.length)
		return false;
	if (this.substr(0, str.length) == str)
		return true;
	else
		return false;
	return true;
}