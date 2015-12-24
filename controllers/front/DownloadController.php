<?php

class DownloadController extends FrontController
{

    public function main()
    {
        $file_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
        $file_path = './data/apk/' . $file_name;
        if (empty($file_name)) {
            Flight::getInstance()->halt(200, 'no file');
        } else if (!file_exists($file_path)) {
            Flight::getInstance()->halt(200, 'file not exists: ' . $file_name);
        } else if (stripos($file_name, '.apk') === false) {
            Flight::getInstance()->halt(200, 'file extension forbidden: ' . $file_name);
        } else {
            Flight::getInstance()->redirect($file_path . '?time=' . time());
        }
    }

    public function weiyun()
    {
        $share_key = isset($_REQUEST['share_key']) ? $_REQUEST['share_key'] : '';
        $client = new HttpClient('http://share.weiyun.com/' . $share_key);
        $client->setUserAgent('Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1');
        if ($client->get()) {
            $content = $client->getContent();
            exit($content);
        } else {
            exit('get error');
        }
    }

}

