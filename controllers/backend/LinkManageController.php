<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2015/7/26
 * Created by IntelliJ IDEA
 */
final class LinkManageController extends LoginedController
{

    public function main()
    {
        $this->template->assign('title', '友链管理');
        $model = new LinkModel();
        $this->template->assign('links', $model->getLinkList());
        $this->template->display("LinkIndex.htm");
    }

    public function add()
    {
        if ($this->isPost()) {
            $cid = intval($_POST['category']);
            //数据入库之前必须转义字符
            $name = htmlspecialchars(addslashes(urldecode($_POST['name'])));
            $url = htmlspecialchars(addslashes(urldecode($_POST['url'])));
            $icon = empty($_POST['icon']) ? '' : htmlspecialchars(addslashes(urldecode($_POST['icon'])));
            $sortby = intval($_POST['sortby']);
            $timeline = time();
            if (empty($name) || empty($url)) {
                Flight::getInstance()->halt(200, '名称或地址不能为空！');
            }
            $model = new LinkModel();
            if ($model->isSimilar($name, $url)) {
                Flight::getInstance()->halt(200, '数据库中已存在相似的数据，请重新修改名称或地址');
            } else {
                $data = array('category_id' => $cid, 'name' => $name, 'url' => $url, 'icon' => $icon, 'sortby' => $sortby, 'timeline' => $timeline);
                $id = $model->addLink($data);
                if ($id > 0) {
                    Flight::getInstance()->halt(200, '恭喜，《' . $name . '》添加成功！');
                } else {
                    Flight::getInstance()->halt(200, '很不幸，《' . $name . '》添加失败！');
                }
            }
        } else {
            $this->template->assign('title', '添加友情链接');
            $model = new CategoryModel();
            $this->template->assign('categories', $model->getCategoryList());
            $this->template->display('LinkAdd.htm');
        }
    }

    public function modify()
    {
        if ($this->isPost()) {
            if (!isset($_GET['id'])) {
                Flight::getInstance()->halt(200, '错误，未传递ID！');
            }
            $id = intval($_GET['id']);
            $cid = intval($_POST['category']);
            $name = htmlspecialchars(addslashes(urldecode($_POST['name'])));
            $url = htmlspecialchars(addslashes(urldecode($_POST['url'])));
            $icon = htmlspecialchars(addslashes(urldecode($_POST['icon'])));
            $sortby = intval($_POST['sortby']);
            if (empty($name) || empty($url)) {
                Flight::getInstance()->halt(200, '名称或地址不能为空！');
            }
            $data = array('category_id' => $cid, 'name' => $name, 'url' => $url, 'icon' => $icon, 'sortby' => $sortby);
            $model = new LinkModel();
            if (false != $model->editLink($id, $data)) {
                Flight::getInstance()->halt(200, '恭喜，《' . $name . '》编辑成功！');
            } else {
                Flight::getInstance()->halt(200, '很不幸，《' . $name . '》编辑失败！');
            }
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $model = new LinkModel();
            $link = $model->getLinkById($id);
            if (false === $link) {
                $link['name'] = '错误：内容不存在！';
                $link['url'] = '很不幸，该内容已被删除或者不存在！';
                $link['icon'] = 'http://';
                $link['sortby'] = 999;
            }
            $this->template->assign('title', '修改友情链接');
            $this->template->assign('id', $id);
            $categoryModel = new CategoryModel();
            $this->template->assign('categories', $categoryModel->getCategoryList());
            $this->template->assign('category_id', $link['category_id']);
            $this->template->assign('name', $link['name']);
            $this->template->assign('url', $link['url']);
            $this->template->assign('icon', $link['icon'] == null ? 'http://' : trim($link['icon']));
            $this->template->assign('sortby', $link['sortby']);
            $this->template->display('LinkModify.htm');
        }
    }

    public function delete()
    {
        if (!isset($_GET['id'])) {
            Flight::getInstance()->halt(200, '错误，未传递ID！');
        }
        $id = intval($_GET['id']);
        $model = new LinkModel();
        if ($model->deleteLink($id)) {
            Flight::getInstance()->halt(200, '恭喜，删除成功！');
        } else {
            Flight::getInstance()->halt(200, '很不幸，删除失败');
        }
    }

}