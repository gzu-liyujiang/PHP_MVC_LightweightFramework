<?php

/**
 *
 * @author 李玉江<liyujiang_tk@yeah.net>
 * @copyright Li YuJiang, All Rights Reserved
 * @version 2015/8/4
 * Created by IntelliJ IDEA
 */
final class CategoryManageController extends LoginedController
{

    public function main()
    {
        $this->template->assign('title', '分类管理');
        $model = new CategoryModel();
        $this->template->assign('categories', $model->getCategoryList());
        $this->template->display("CategoryIndex.htm");
    }

    public function add()
    {
        if ($this->isPost()) {
            //数据入库之前必须转义字符
            $name = htmlspecialchars(addslashes(urldecode($_POST['name'])));
            $description = '关于' . $name;
            $sortby = intval($_POST['sortby']);
            $hidden = 0;
            if (empty($name)) {
                Flight::getInstance()->halt(200, '名称不能为空！');
            }
            $model = new CategoryModel();
            if ($model->isSimilar($name)) {
                Flight::getInstance()->halt(200, '数据库中已存在相似的数据，请重新修改名称');
            } else {
                $data = array('name' => $name, 'description' => $description, 'sortby' => $sortby, 'hidden' => $hidden);
                $id = $model->addCategory($data);
                if ($id > 0) {
                    Flight::getInstance()->halt(200, '恭喜，《' . $name . '》添加成功！');
                } else {
                    Flight::getInstance()->halt(200, '很不幸，《' . $name . '》添加失败！');
                }
            }
        } else {
            $this->template->assign('title', '添加分类');
            $this->template->display('CategoryAdd.htm');
        }
    }

    public function modify()
    {
        if ($this->isPost()) {
            if (!isset($_GET['id'])) {
                Flight::getInstance()->halt(200, '错误，未传递ID！');
            }
            $id = intval($_GET['id']);
            $name = htmlspecialchars(addslashes(urldecode($_POST['name'])));
            $description = '关于' . $name;
            $sortby = intval($_POST['sortby']);
            $hidden = intval($_POST['hidden']);
            if (empty($name)) {
                Flight::getInstance()->halt(200, '名称不能为空！');
            }
            $data = array('name' => $name, 'description' => $description, 'sortby' => $sortby, 'hidden' => $hidden);
            $model = new CategoryModel();
            if (false != $model->editCategory($id, $data)) {
                Flight::getInstance()->halt(200, '恭喜，《' . $name . '》编辑成功！');
            } else {
                Flight::getInstance()->halt(200, '很不幸，《' . $name . '》编辑失败！');
            }
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $model = new CategoryModel();
            $category = $model->getCategoryById($id);
            if (false === $category) {
                $category['name'] = '错误：内容不存在！';
                $category['description'] = '很不幸，该内容已被删除或者不存在！';
                $category['sortby'] = 999;
                $category['hidden'] = 0;
            }
            $this->template->assign('title', '修改分类');
            $this->template->assign('id', $id);
            $this->template->assign('name', $category['name']);
            $this->template->assign('description', $category['description']);
            $this->template->assign('sortby', $category['sortby']);
            $this->template->assign('hidden', $category['hidden']);
            $this->template->display('CategoryModify.htm');
        }
    }

    public function delete()
    {
        if (!isset($_GET['id'])) {
            Flight::getInstance()->halt(200, '错误，未传递ID！');
        }
        $id = intval($_GET['id']);
        $model = new CategoryModel();
        if ($model->deleteCategory($id)) {
            Flight::getInstance()->halt(200, '恭喜，删除成功！');
        } else {
            Flight::getInstance()->halt(200, '很不幸，删除失败');
        }
    }

}