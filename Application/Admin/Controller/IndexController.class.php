<?php

namespace Admin\Controller;

/**
 * 首页控制器
 */
class IndexController extends AdminController
{
    /**
     * 首页
     */
    public function index()
    {
        //UID为用户id uid为用户组id
        if (UID) {
            if (IS_ROOT) { //管理员首页
                $this->meta_title = '案件列表';
                $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
                $this->uid = $uid;
                $user_info = array(
                    'username' => $_SESSION['onethink_admin']['user_auth']['username'],
                    'last_login_time' => $_SESSION['onethink_admin']['user_auth']['last_login_time'],
                );

                $this->assign('user_info', $user_info);

            } else { //普通用户首页
                $this->meta_title = '案件列表';
                $user_info = array(
                    'username' => $_SESSION['onethink_admin']['user_auth']['username'],
                    'last_login_time' => $_SESSION['onethink_admin']['user_auth']['last_login_time'],
                );
                //根据用户id，查询用户上次登录ip
                $ucenter_member = M('member');
                $what = array(
                    'uid' => intval($_SESSION['onethink_admin']['user_auth']['uid']),
                );
                $department = C('DEPARTMENT');
                $info = $ucenter_member->where($what)->find();
                $user_info['department'] = $department[$info['department']];
                $user_info['area_code'] = getShequ($info['area_code']);
                $user_info['street_code'] = getShequ($info['street_code']);
                $user_info['community_code'] = getShequ($info['community_code']);
                $this->assign('user_info', $user_info);

                $uid = M('auth_group_access')->field('group_id')->where(array('uid' => UID))->select();

                $this->uid = $uid;
            }
            //案件统计
            $case = M('case');
            $auth = getStatusFromAuth();
            $where = [
                'case_status' => ['in', implode(',', $auth['status'])],
            ];
            if (UID != 1) {
                $user = M('member')->where(array('uid' => UID))->find();
                switch ($user['department']){
                    case 0:
                        $where['community_code'] = $user['community_code'];
                        break;
                    case 1:
                        $where['street_code'] = $user['street_code'];
                        break;
                    case 2:
                        $where['area_code'] = $user['area_code'];
                        break;
                }
            }
            //案件统计过滤的字段
            $field = ['id', 'name', 'case_status', 'add_time', 'trial_time', 'last_instance_time', 'dispatch_time', 'deal_with_time', 'finish_time', 'visit_time', 'birthday', 'turn_relateds'];
            //权限下所有案件
            $cases = handle($case->field($field)->where($where)->order('fill_in_time desc')->select());
            //消息和待处理数量
            $where['stage_status'] = 'complete';
            $messages = handle($case->field($field)->where($where)->order('fill_in_time desc')->select());
            foreach ($messages as &$vs) {
                $vs['case_statuss'] = getStage($vs['case_status']);
                $vs['case_time'] = $vs[statusTime($vs['case_status'])];
            }
            $this->messages = $messages;
            $this->waiting = count($messages);
            //正在处理的数量
            $where['stage_status'] = 'ing';
            $this->handling = handle($case->field($field)->where($where)->order('fill_in_time desc')->select(), false);
            //超时和即将超时数量
            $overtime = $this->countOverTimeCases($cases);
            $this->overtiming = $overtime['overtiming'];
            $this->overtimed = $overtime['overtimed'];
            //超龄
            $age = array();
            $overage = empty(C('OVERAGE_CASE')) ? 18 : C('OVERAGE_CASE');
            foreach ($cases as $val) {
                if (getAge($val['birthday']) > $overage) $age[] = getAge($val['birthday']);
            }
            $this->overaged = count($age);
            //站内公告
            $document = M('document')->field('id,title,category_id,display')->select();
            foreach ($document as $key => &$val) {
                if ($val['display'] != 5) {
                    if (!get_department($val['display'])) {
                        unset($document[$key]);
                    }
                }
            }
            $this->document = $document;
            //短信配置
            $cfg = M('addons')->where(array('name' => 'SMS'))->getField('config');
            $number = 0;
            if ($cfg) {
                $cfg = json_decode($cfg, 1);
                $url = 'http://www.smschinese.cn/web_api/SMS/?Action=SMS_Num&Uid=' . $cfg['Uid'] . '&Key=' . $cfg['Key'];
                $number = file_get_contents($url);
            }
            $this->smsNumber = $number;
            $this->display();
        } else {
            $this->redirect('Public/login');
        }
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
        $rules = M('CaseManage')->field(['node', 'warn_time', 'execute_time'])->where(['status' => 1])->select();
        $rules = rebuildArray($rules, 'node');
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
                        if ($now >= $overtimed) {
                            $overtime['overtimed']++;
                        } else {
                            $overtime['overtiming']++;
                        }
                    }
                }
            }
        }
        return $overtime;
    }
}
