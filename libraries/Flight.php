<?php
/**
 * Flight精简修改版。
 * @link https://github.com/mikecao/flight
 *
 * @copyright   Copyright (c) 2011, Mike Cao <mike@mikecao.com>
 * @license     MIT, http://flightphp.com/license
 */

/**
 * 包括：数据缓冲输出、错误异常处理、全局变量管理、HTTP响应处理
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @version 2015/7/25
 */
class Flight
{
    /**
     * Application instance.
     *
     * @var Flight
     */
    private static $instance = null;
    /**
     * Stored variables.
     *
     * @var array
     */
    protected $vars = array();
    /**
     * @var array HTTP headers
     */
    protected $headers = array();


    // Don't allow object instantiation
    private function __construct()
    {
        $this->set('base_url', '/');
    }


    private function __clone()
    {
    }


    /**
     * @return Flight Application instance
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Flight();

        }
        return self::$instance;
    }


    /**
     * Custom error handler. Converts errors into exceptions.
     *
     * @param int $errno Error number
     * @param int $errstr Error string
     * @param int $errfile Error file name
     * @param int $errline Error file line number
     * @throws ErrorException
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }

    /**
     * Custom exception handler. Logs exceptions.
     *
     * @param Exception $e Thrown exception
     */
    public function handleException(Exception $e)
    {
        $this->error($e);
    }

    /**
     * Gets a variable.
     *
     * @param string $key Key
     * @return mixed
     */
    public function get($key = null)
    {
        if ($key === null) return $this->vars;

        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }

    /**
     * Sets a variable.
     *
     * @param mixed $key Key
     * @param string $value Value
     */
    public function set($key, $value = null)
    {
        if (is_array($key) || is_object($key)) {
            foreach ($key as $k => $v) {
                $this->vars[$k] = $v;
            }
        } else {
            $this->vars[$key] = $value;
        }
    }

    /**
     * Checks if a variable has been set.
     *
     * @param string $key Key
     * @return bool Variable status
     */
    public function has($key)
    {
        return isset($this->vars[$key]);
    }

    /**
     * Unsets a variable. If no key is passed in, clear all variables.
     *
     * @param string $key Key
     */
    public function clear($key = null)
    {
        if (is_null($key)) {
            $this->vars = array();
        } else {
            unset($this->vars[$key]);
        }
    }

    /**
     * @param callback $callback
     * @param array $params
     * Initial something and then starts the framework.
     */
    public function start($callback, $params = array())
    {
        call_user_func_array($callback, $params);
        // Flush any existing output
        if (ob_get_length() > 0) {
            ob_end_clean();
        }

        // Enable output buffering
        ob_start();
        // handle error
        if (IS_DEVELOP) {
            set_error_handler(array($this, 'handleError'));
            set_exception_handler(array($this, 'handleException'));
        } else {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    /**
     * Stops the framework and outputs the current response.
     * @param int $code
     * @throws Exception
     */
    public function stop($code = 200)
    {
        $this->halt($code, ob_get_clean());
    }

    /**
     * Adds a header to the response.
     *
     * @param string|array $name Header name or array of names and values
     * @param string $value Header value
     * @return object Self reference
     */
    public function header($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->headers[$k] = $v;
            }
        } else {
            $this->headers[$name] = $value;
        }
        return $this;
    }

    /**
     * stops processing and returns a given response.
     *
     * @param int $code HTTP status code
     * @param string $message response message
     * @throws Exception
     */
    public function halt($code = 200, $message = '')
    {
        // HTTP status codes
        $codes = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required'
        );
        if (!array_key_exists($code, $codes)) {
            throw new Exception('Invalid http status code: ' . $code);
        }
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        if (!headers_sent()) {
            // Send status code header
            if (strpos(php_sapi_name(), 'cgi') !== false) {
                header(sprintf('Status: %d %s', $code, $codes[$code]), true);
            } else {
                header(sprintf('%s %d %s',
                    (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'),
                    $code, $codes[$code]), true, $code);
            }
            // Send other headers
            foreach ($this->headers as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        header($field . ': ' . $v, false);
                    }
                } else {
                    header($field . ': ' . $value);
                }
            }
            // Send content length
            if (($length = strlen($message)) > 0) {
                header('Content-Length: ' . $length);
            }
        }
        exit($message);
    }

    /**
     * Sends an HTTP 500 response for any errors.
     *
     * @param Exception $e Thrown exception
     */
    public function error(Exception $e)
    {
        $msg = sprintf('<h1>500 Internal Server Error</h1>' .
            '<h3>%s (%s)</h3>' .
            '<pre>%s</pre>',
            $e->getMessage(),
            $e->getCode(),
            $e->getTraceAsString()
        );
        try {
            $this->halt(500, $msg);
        } catch (Exception $ex) {
            exit($msg);
        }
    }

    /**
     * Sends an HTTP 404 response when a URL is not found.
     */
    public function notFound()
    {
        $this->halt(404, '<h1>404 Not Found</h1>' .
            '<h3>The page you have requested could not be found.</h3>' .
            str_repeat(' ', 512));
    }

    /**
     * Redirects the current request to another URL.
     * @param $url
     * @throws Exception
     */
    public function redirect($url)
    {
        $this->header('Location', $url)
            ->halt(303, $url);
    }

    /**
     * Sends a JSON response.
     *
     * @param mixed $data JSON data
     * @param bool $encode Whether to perform JSON encoding
     */
    public function json($data, $encode = true)
    {
        $json = new Json();
        $data = $encode ? $json->encode($data) : $data;
        $this->header('Content-Type', 'application/json')
            ->halt(200, $data);
    }

    /**
     * Sends a JSONP response.
     *
     * @param mixed $data JSON data
     * @param string $param Query parameter that specifies the callback name.
     * @param bool $encode Whether to perform JSON encoding
     */
    public function jsonp($data, $param = 'jsonp', $encode = true)
    {
        $json = new Json();
        $data = $encode ? $json->encode($data) : $data;
        $this->header('Content-Type', 'application/javascript')
            ->halt(200, $param . '(' . $data . ');');
    }

    /**
     * Handles ETag HTTP caching.
     *
     * @param string $id ETag identifier
     * @param string $type ETag type
     */
    public function etag($id, $type = 'strong')
    {
        $id = (($type === 'weak') ? 'W/' : '') . $id;
        $this->header('ETag', $id);
        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])
            && $_SERVER['HTTP_IF_NONE_MATCH'] === $id
        ) {
            $this->halt(304);
        }
    }

    /**
     * Handles last modified HTTP caching.
     *
     * @param int $time Unix timestamp
     */
    public function lastModified($time)
    {
        $this->header('Last-Modified', date(DATE_RFC1123, $time));
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $time
        ) {
            $this->halt(304);
        }
    }

}
