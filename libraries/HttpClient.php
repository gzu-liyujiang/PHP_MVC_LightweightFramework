<?php

/**
 * HttpClient，基于CURL实现
 * @link https://github.com/smartmeng/HttpClient
 * @author @SmartMeng http://weibo.com/mengsmart
 * @version 0.2
 * @time 2014.04.06
 */
class HttpClient
{
    // Request vars
    var $url;
    var $method;
    var $postdata = '';
    var $cookies = array();
    var $referer;
    var $user_agent = 'HttpClient by SmartMeng v0.2';

    // Options
    var $timeout = 20;
    var $use_gzip = true;
    var $persist_cookies = true; // If true, received cookies are placed in the $this->cookies array ready for the next request
    // Note: This currently ignores the cookie path (and time) completely. Time is not important,
    //       but path could possibly lead to security problems.
    var $handle_redirects = true; // Auaomtically redirect if Location or URI header is found
    var $max_redirects = 5;
    var $headers_only = false; // If true, stops receiving once headers have been read.

    // Response vars
    var $status;
    var $headers = array();
    var $content = '';
    var $errormsg;

    function __construct($url)
    {
        if (!function_exists('curl_init')) {
            exit('curl module required');
        }
        $this->url = $url;
    }

    public function get($url = "")
    {
        if ("" != $url) {
            $this->url = $url;
        }
        $this->method = 'GET';
        return $this->doRequest();
    }

    public function post($url, $data)
    {
        if ("" != $url) {
            $this->url = $url;
        }
        $this->method = 'POST';
        $this->postdata = $this->buildQueryString($data);
        return $this->doRequest();
    }

    private function buildQueryString($data)
    {
        $query_string = '';
        if (is_array($data)) {
            // Change data in to postable data
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $val2) {
                        $query_string .= urlencode($key) . '=' . urlencode($val2) . '&';
                    }
                } else {
                    $query_string .= urlencode($key) . '=' . urlencode($val) . '&';
                }
            }
            $query_string = substr($query_string, 0, -1); // Eliminate unnecessary &
        } else {
            $query_string = $data;
        }
        return $query_string;
    }

    private function doRequest()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        if ($this->referer) {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }
        if ($this->use_gzip) {
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        }
        if ($this->method == 'POST') {
            //发送一个常规的POST请求，类型为：application/x-www-form-urlencoded
            curl_setopt($ch, CURLOPT_POST, true);
        }
        if ($this->postdata) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postdata);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        //设置连接等待时间,0不等待
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        //设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        //启用时会将服务器服务器返回的“Location:”放在header中递归的返回给服务器
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->handle_redirects);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $this->max_redirects);

        if ($this->cookies) {
            $cookie = 'Cookie: ';
            foreach ($this->cookies as $key => $value) {
                $cookie .= "$key=$value; ";
            }
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($ch, CURLOPT_NOBODY, $this->headers_only);
        //是否将头文件的信息作为数据流输出(HEADER信息),这里保留报文
        curl_setopt($ch, CURLOPT_HEADER, true);
        //获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        $result = curl_exec($ch);
        $this->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) { //出错则抛出错误信息
            $_erroStr = curl_error($ch);
            $_erroNo = curl_errno($ch);
            $this->errormsg = $_erroStr;
            return false;
        }
        curl_close($ch); //关闭curl链接

        $result = explode("\r\n\r\n", $result, 2);
        $this->content = $result[1];

        $headers = $result[0];
        $headers = explode("\r\n", $headers);
        foreach ($headers as $line) {
            if (!preg_match('/([^:]+):\\s*(.*)/', $line, $m)) {
                // Skip to the next header
                continue;
            }
            $key = strtolower(trim($m[1]));
            $val = trim($m[2]);
            // Deal with the possibility of multiple headers of same name
            if (isset($this->headers[$key])) {
                if (is_array($this->headers[$key])) {
                    $this->headers[$key][] = $val;
                } else {
                    $this->headers[$key] = array($this->headers[$key], $val);
                }
            } else {
                $this->headers[$key] = $val;
            }
        }

        if ($this->persist_cookies && isset($this->headers['set-cookie'])) {
            $cookies = $this->headers['set-cookie'];
            if (!is_array($cookies)) {
                $cookies = array($cookies);
            }
            foreach ($cookies as $cookie) {
                if (preg_match('/([^=]+)=([^;]+);/', $cookie, $m)) {
                    $this->cookies[$m[1]] = $m[2];
                }
            }
        }
        return true;
    }

    function getStatus()
    {
        return $this->status;
    }

    function getContent()
    {
        return $this->content;
    }

    function getHeaders()
    {
        return $this->headers;
    }

    function getHeader($header)
    {
        $header = strtolower($header);
        if (isset($this->headers[$header])) {
            return $this->headers[$header];
        } else {
            return false;
        }
    }

    function getError()
    {
        return $this->errormsg;
    }

    function getCookies()
    {
        return $this->cookies;
    }

    function getRequestURL()
    {
        return $this->url;
    }

    // Setter methods
    function setUserAgent($string)
    {
        $this->user_agent = $string;
    }

    function setCookies($array)
    {
        $this->cookies = $array;
    }

    // Option setting methods
    function setUseGzip($boolean)
    {
        $this->use_gzip = $boolean;
    }

    function setPersistCookies($boolean)
    {
        $this->persist_cookies = $boolean;
    }

    function setHandleRedirects($boolean)
    {
        $this->handle_redirects = $boolean;
    }

    function setMaxRedirects($num)
    {
        $this->max_redirects = $num;
    }

    function setHeadersOnly($boolean)
    {
        $this->headers_only = $boolean;
    }

    // "Quick" static methods
    public static function quickGet($url)
    {
        $client = new HttpClient($url);
        if (!$client->get()) {
            return false;
        } else {
            return $client->getContent();
        }
    }

    public static function quickPost($url, $data)
    {
        $client = new HttpClient($url);
        if (!$client->post($url, $data)) {
            return false;
        } else {
            return $client->getContent();
        }
    }
}

?>