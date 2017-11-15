<?php

namespace Admin\Controller;


/**
 * 后台案件管理控制器
 */
class ManageController extends AdminController
{
    /**
     * 案件管理列表
     */
    public function index()
    {
        /* 获取案件管理列表 */
        $list = M('Case_manage')->where(array('status' => array('gt', -1)))->order('id asc')->select();
        $this->assign('list', $list);
        $this->meta_title = '案件管理';
        $this->display();
    }

    /**
     * 添加案件管理
     */
    public function add()
    {
        if (IS_POST) {
            $CaseManage = D('Case_manage');
            $data = $CaseManage->create();
            if ($data) {
                $id = $CaseManage->add();
                if ($id) {
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_channel', 'case_manage', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($CaseManage->getError());
            }
        } else {
            $this->assign('info', null);
            $this->meta_title = '新增案件管理';
            $this->display('edit');
        }
    }

    /**
     * 编辑案件管理
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $CaseManage = D('Case_manage');
            $data = $CaseManage->create();
            if ($data) {
                if ($CaseManage->save()) {
                    //记录行为
                    action_log('update_channel', 'case_manage', $data['id'], UID);
                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }

            } else {
                $this->error($CaseManage->getError());
            }
        } else {
            /* 获取数据 */
            $info = M('Case_manage')->find($id);
            $this->assign('info', $info);
            $this->meta_title = '编辑案件管理';
            $this->display();
        }
    }

    /**
     * 删除案件管理
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id));
        if (M('Case_manage')->where($map)->delete()) {
            //记录行为
            action_log('update_channel', 'case_manage', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function status()
    {
        $ids = I('request.ids');
        $status = I('request.status');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        $map['id'] = array('in', $ids);
        switch ($status) {
            case 0  :
                $this->forbid('Case_manage', $map, array('success' => '禁用成功', 'error' => '禁用失败'));
                break;
            case 1  :
                $this->resume('Case_manage', $map, array('success' => '启用成功', 'error' => '启用失败'));
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }
}