<?php

namespace Mobile\Controller;

use User\Api\UserApi;

/**
 * 后台内容控制器
 */
class CenterController extends MobileController
{
    //用户信息
    public function addressList()
    {
        $member = M('member')->where('status=1 and uid!=1')->select();
        $group = M()
            ->table('onethink_auth_group_access a')
            ->join('onethink_auth_group g on a.group_id=g.id')
            ->field('a.uid,g.id,g.description,g.title')
            ->select();
        $department = C('DEPARTMENT');
        foreach ($member as &$v) {
            $v['department'] = $department[$v['department']];
            foreach ($group as $val) {
                if ($v['uid'] == $val['uid']) {
                    $v['description'][] = $val['description'];
                    $v['title'][] = $val['title'];
                }
            }
        }
        int_to_string($member);
        exit(json_encode(array('member' => $member)));
    }

    //消息中心
    public function newsCenter()
    {
        $case = M('case');
        //案件统计过滤的字段
        $field = ['id', 'name', 'case_status', 'add_time', 'trial_time', 'last_instance_time', 'dispatch_time', 'deal_with_time', 'finish_time', 'visit_time'];
        $auth = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $auth['status'])],
        ];
        //消息
        $messages = $case->field($field)->where($where)->order('fill_in_time desc')->select();
        foreach ($messages as &$vs) {
            $vs['case_statuss'] = getStage($vs['case_status']);
            $vs['case_time'] = $vs[statusTime($vs['case_status'])];
        }
        exit(json_encode(array('messages' => $messages)));
    }

    //站内公告
    public function standNotice()
    {
        $document = M('document')->field('id,title,category_id,display')->select();
        foreach ($document as $key => &$val) {
            if ($val['display'] != 5) {
                if (!get_department($val['display'])) {
                    unset($document[$key]);
                }
            }
        }
        exit(json_encode(array('document' => $document)));
    }

    //修改密码
    public function editPassword()
    {
        if (IS_POST) {
            //获取参数
            $data = $_POST;
            $res = '';
            if (empty($data['"pwd'])) $res = '请输入原密码';
            if (empty($data['password'])) $res = '请输入新密码';
            if (empty($data['repassword'])) $res = '请输入确认密码';
            if ($data['password'] !== rtrim($data['repassword'], '"')) $res = '您输入的新密码与确认密码不一致';
            if (!empty($res)) exit(json_encode(array('erron' => 0, 'error' => $res)));
            $uid = $_POST['uid'];
            $Api = new UserApi();
            if ($uid) {
                $res = $Api->updateInfo($uid, $data['"pwd'], array('password' => $data['password']));
            } else {
                $res = $Api->updateInfo(UID, $data['"pwd'], array('password' => $data['password']));
            }
            if ($res['status']) {
                exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
            } else {
                exit(json_encode(array('erron' => 0, 'error' => $res['info'])));
            }
        }
    }

    public function noticeDetails()
    {
        $pid = $_GET['id'];
        var_dump($pid);
        $document = M('Document')->where(array('id' => $pid))->find();
        $article = M('documentArticle')->where(array('id' => $pid))->find();
        $list = array_merge($document, $article);
        exit(json_encode('list', $list));
    }
}
