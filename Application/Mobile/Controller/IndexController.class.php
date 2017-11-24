<?php

namespace Mobile\Controller;

/**
 * 首页控制器
 */
class IndexController extends MobileController
{
    /**
     * 首页
     */
    public function index()
    {
        //UID为用户id uid为用户组id
        if (UID) {
            $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
            //根据用户id，查询用户上次登录ip
            $ucenter_member = M('ucenter_member');
            $where = array(
                'id' => intval($_SESSION['onethink_admin']['user_auth']['uid']),
            );
            $last_login_ip = $ucenter_member->where($where)->getField('last_login_ip');
            //用户信息
            $user_info = array(
                'uid' => $uid,
                'last_login_ip' => $last_login_ip,
                'username' => $_SESSION['onethink_admin']['user_auth']['username'],
                'last_login_time' => $_SESSION['onethink_admin']['user_auth']['last_login_time'],
            );
            $case = M('case');
            $auth = getStatusFromAuth();
            $where = [
                'case_status' => ['in', implode(',', $auth['status'])],
            ];
            //案件统计过滤的字段
            $field = ['id', 'name', 'case_status', 'add_time', 'trial_time', 'last_instance_time', 'dispatch_time', 'deal_with_time', 'finish_time', 'visit_time'];
            //消息中心
            $messages = $case->field($field)->where($where)->order('fill_in_time desc')->select();
            $overtime = $this->countOverTimeCases($messages);
            //案件统计
            $count = array(
                'handling' => count($messages),//正在处理的案件
                'waiting' => count($messages),//待处理的案件
                'overtiming' => $overtime['overtiming'],//即将超时的案件
                'overtimed' => $overtime['overtimed'],//已经超时的案件
            );
            exit(json_encode(array('erron' => 1, 'user_info' => $user_info, 'messages' => array_slice($messages, 0, 20, false), 'count' => $count)));
        } else {
            exit(json_encode(array('erron' => 0, 'error' => '请登录...')));
        }
    }

    public function scrolling()
    {
        $case = M('case');
        $auth = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $auth['status'])],
        ];
        //案件统计过滤的字段
        $field = ['id', 'name', 'case_status', 'add_time', 'trial_time', 'last_instance_time', 'dispatch_time', 'deal_with_time', 'finish_time', 'visit_time'];
        $start = $_GET['index'];

        $count = $case->field($field)->where($where)->order('fill_in_time desc')->count();
        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 20;
        $p = I('index') ? I('index') : 1;
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $Page = new Page($count, $pagesize);
//        $page = $Page->show();
        $messages = $case->field($field)->where($where)->limit($limit)->order('fill_in_time desc')->select();
        exit(json_encode(array('messages' => $messages)));
    }

    /**
     * 计算 超时/即将超时 案件数
     * return array $overtime 返回数组包含键值overtiming, overtimed
     * overtiming为即将超时, overtimed为已经超时
     */
    private function countOverTimeCases($cases)
    {
        //初始化返回结果
        $overtime = ['overtiming' => 0, 'overtimed' => 0];
        $rules    = M('CaseManage')->field(['node', 'warn_time', 'execute_time'])->where(['status' => 1])->select();
        $rules    = rebuildArray($rules, 'node');
        $now = time();
        foreach ($cases as $k => $v) {
            $status = $v['case_status'];
            if ($rules[$status]['warn_time'] == 0)
                //表示禁用 则不限制时间
                continue;
            else {
                $key = translate($status);
                //现阶段该执行的状态的时间
                $caseTime = strtotime($v[$key['now'] . '_time']);
                if (!empty($caseTime)) {
                    //下一阶段该执行的状态的时间
                    $nextTime = $v[$key['next'] . '_time'];
                    $time = empty($nextTime) ? $caseTime : $nextTime;
                    $overtiming = $time + $rules[$status]['warn_time'] * 3600;
                    $overtimed = $time + $rules[$status]['execute_time'] * 3600;

                    if ($now >= $overtiming) {
                        if ($now >= $overtimed)
                            $overtime['overtimed']++;
                        else
                            $overtime['overtiming']++;
                    }
                }
            }
        }
        return $overtime;
    }
}
