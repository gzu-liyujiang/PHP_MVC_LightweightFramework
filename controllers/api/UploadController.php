<?php

/**
 * 单文件上传
 * 图片：api.php?c=Upload&dir=image
 * 文档：api.php?c=Upload&dir=document
 * 压缩包：api.php?c=Upload&dir=archive
 * FLASH：api.php?c=Upload&dir=flash
 * 音视频：api.php?c=Upload&dir=media
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2014/3/31
 */
class UploadController extends ApiController
{

    public function main()
    {
        //允许上传的文件扩展名
        $allow_ext = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'ico', 'bmp'),
            'document' => array('txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'chm'),
            'archive' => array('zip', 'rar', 'jar', 'apk', 'ipa', 'deb', 'tar', 'tgz', 'bz'),
            'flash' => array('swf', 'flv'),
            'media' => array('mp3', 'wav', 'wma', 'mid', '3gp', 'mp4', 'avi', 'wmv')
        );
        //最大文件大小
        $max_size = 1000000;
        //文件保存目录路径
        $save_path = ROOT_PATH . '/data/upload/';
        mkdir($save_path, 0777, true);
        //文件保存目录网址
        $save_url = './data/upload/';
        if (!isset($_FILES['file'])) {
            $this->responseJson(0, '参数错误');
            return;
        }
        $errorCode = $_FILES['file']['error'];
        //PHP上传失败
        if (!empty($errorCode)) {
            switch ($errorCode) {
                case '1':
                    $errorMsg = '超过php.ini允许的大小';
                    break;
                case '2':
                    $errorMsg = '超过表单允许的大小';
                    break;
                case '3':
                    $errorMsg = '图片只有部分被上传';
                    break;
                case '4':
                    $errorMsg = '请选择图片';
                    break;
                case '6':
                    $errorMsg = '找不到临时目录';
                    break;
                case '7':
                    $errorMsg = '写文件到硬盘出错';
                    break;
                case '8':
                    $errorMsg = '该文件类型不允许上传。';
                    break;
                default:
                    $errorMsg = '未知错误';
            }
            $this->responseJson(0, $errorMsg);
            return;
        }
        //有上传文件时
        if (empty($_FILES) === false) {
            //原文件名
            $file_name = $_FILES['file']['name'];
            //服务器上临时文件名
            $tmp_name = $_FILES['file']['tmp_name'];
            //文件大小
            $file_size = $_FILES['file']['size'];
            //检查文件名
            if (!$file_name) {
                $this->responseJson(0, "请选择文件");
                return;
            }
            //检查目录
            if (!is_dir($save_path)) {
                $this->responseJson(0, "上传目录不存在");
                return;
            }
            //检查目录写权限
            if (!is_writable($save_path)) {
                $this->responseJson(0, "上传目录没有写权限");
                return;
            }
            //检查是否已上传
            if (!is_uploaded_file($tmp_name)) {
                $this->responseJson(0, "上传失败");
                return;
            }
            //检查文件大小
            if ($file_size > $max_size) {
                $this->responseJson(0, "上传文件大小超过限制");
                return;
            }
            //检查目录名
            $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
            if (empty($allow_ext[$dir_name])) {
                $this->responseJson(0, "目录名不正确");
                return;
            }
            //获得文件扩展名
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            //检查扩展名
            if (!in_array($file_ext, $allow_ext[$dir_name])) {
                $this->responseJson(0, "上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $allow_ext[$dir_name]) . "格式");
                return;
            }
            //创建文件夹
            if ($dir_name !== '') {
                $save_path .= $dir_name . '/';
                $save_url .= $dir_name . '/';
            }
            mkdir($save_path, 0777, true);
            //新文件名
            $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
            //移动文件
            $file_path = $save_path . $new_file_name;
            $file_url = $save_url . $new_file_name;
            if (!move_uploaded_file($tmp_name, $file_path)) {
                $this->responseJson(0, "上传文件失败");
            } else {
                $this->responseJson(1, $file_url);
            }
        }
    }

}