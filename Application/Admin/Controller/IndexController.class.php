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
        if (UID) {
            if (IS_ROOT) { //管理员首页
                $this->meta_title = '案件列表';
                $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
                $this->CaseList = M('case')->select();
                $this->uid = $uid;
                $user_info = array(
                    'username' => $_SESSION['onethink_admin']['user_auth']['username'],
                    'last_login_time' => $_SESSION['onethink_admin']['user_auth']['last_login_time'],
                );
                $this->assign('user_info', $user_info);
                $this->display();
            } else { //普通用户首页
                $this->meta_title = '案件列表';
                $user_info = array(
                    'username' => $_SESSION['onethink_admin']['user_auth']['username'],
                    'last_login_time' => $_SESSION['onethink_admin']['user_auth']['last_login_time'],
                );
                //根据用户id，查询用户上次登录ip
                $ucenter_member = M('ucenter_member');
                $where = array(
                    'id' => intval($_SESSION['onethink_admin']['user_auth']['uid']),
                );
                $user_info['last_login_ip'] = $ucenter_member->where($where)->getField('last_login_ip');

                $this->assign('user_info', $user_info);

                $uid = M('auth_group_access')->field('group_id')->where(array('uid' => UID))->select();
                if ($uid == 1) {
                    $where = ('turn_related = 1 and case_status = diaodu');
                }
                if ($uid == 2) {
                    $where = ('case_status = "chushen"');
                }
                if ($uid == 3) {
                    $where = ('case_status = "diaodu" and turn_related = 3 or case_status = "weihuifang"');
                }
                if ($uid == 4) {
                    $where = ('case_status = "caiji" or case_status = "shenpi" or case_status = "chizhi"');
                }
                $this->xiaoxiList = M('case')->where($where)->order('fill_in_time desc')->select();

                $this->CaseList = M('case')->select();
                $this->zaichu = M('case')->where($where)->count();
                $this->uid = $uid;

                if ($uid == 1) {
                    $theTime = M('case')->where(array('turn_related' => 1, 'case_status' => 'diaodu'))->getField('dispatch_time');
                    $starTime = strtotime($theTime) + strtotime("8 day");
                    $endTime = strtotime($theTime) + strtotime("10 day");
                    $where1 = array(
                        'turn_related' => 1,
                        'case_status' => 'diaodu',
                        'dispatch_time' => array('between', array($starTime, $endTime))
                    );
                    $where2 = array(
                        'turn_related' => 1,
                        'case_status' => 'diaodu',
                        'dispatch_time' => array('GT', $endTime)
                    );
                    $this->jijiang = M('case')->where($where1)->count();
                    $this->chaoshi = M('case')->where($where2)->count();
                }
                if ($uid == 2) {
                    $theTime = M('case')->where(array('case_status' => 'chushen'))->getField('trial_time');
                    $starTime = strtotime($theTime) + strtotime("8 day");
                    $endTime = strtotime($theTime) + strtotime("10 day");
                    $where1 = array(
                        'case_status' => 'chushen',
                        'dispatch_time' => array('between', array($starTime, $endTime))
                    );
                    $where2 = array(
                        'case_status' => 'chushen',
                        'dispatch_time' => array('GT', $endTime)
                    );
                    $this->jijiang = M('case')->where($where1)->count();
                    $this->chaoshi = M('case')->where($where2)->count();
                }
                if ($uid == 3) {
                    $theTime = M('case')->where(array('case_status="diaodu" and turn_related=3 or case_status="weihuifang"'))->field('dispatch_time,finish_time')->find();
                    $DDstarTime = strtotime($theTime['dispatch_time']) + strtotime("8 day");
                    $DDendTime = strtotime($theTime['dispatch_time']) + strtotime("10 day");
                    $JAstarTime = strtotime($theTime['finish_time']) + strtotime("8 day");
                    $JAendTime = strtotime($theTime['finish_time']) + strtotime("10 day");
                    $where1 = array(
                        'turn_related' => 3,
                        'case_status' => 'diaodu',
                        'dispatch_time' => array('between', array($DDstarTime, $DDendTime))
                    );
                    $where2 = array(
                        'turn_related' => 3,
                        'case_status' => 'diaodu',
                        'dispatch_time' => array('GT', $DDendTime)
                    );
                    $where3 = array(
                        'case_status' => 'jiean',
                        'dispatch_time' => array('between', array($JAstarTime, $JAendTime))
                    );
                    $where4 = array(
                        'case_status' => 'jiean',
                        'dispatch_time' => array('GT', $JAendTime)
                    );
                    $DDjijiang = M('case')->where($where1)->count();
                    $DDchaoshi = M('case')->where($where2)->count();
                    $JAjijiang = M('case')->where($where3)->count();
                    $JAchaoshi = M('case')->where($where4)->count();
                    $this->jijiang = $DDjijiang + $JAjijiang;
                    $this->chaoshi = $DDchaoshi + $JAchaoshi;
                }
                if ($uid == 4) {
                    $theTime = M('case')->where(array('case_status = "caiji" or case_status = "shenpi" or case_status = "chizhi"'))->field('fill_in_time,last_instance_time,deal_with_time')->find();
                    $CJstarTime = strtotime($theTime['fill_in_time']) + strtotime("8 day");
                    $CJendTime = strtotime($theTime['fill_in_time']) + strtotime("10 day");
                    $SPstarTime = strtotime($theTime['last_instance_time']) + strtotime("8 day");
                    $SPendTime = strtotime($theTime['last_instance_time']) + strtotime("10 day");
                    $CZstarTime = strtotime($theTime['deal_with_time']) + strtotime("8 day");
                    $CZendTime = strtotime($theTime['deal_with_time']) + strtotime("10 day");
                    $where1 = array(
                        'case_status' => 'caiji',
                        'dispatch_time' => array('between', array($CJstarTime, $CJendTime))
                    );
                    $where2 = array(
                        'case_status' => 'caiji',
                        'dispatch_time' => array('GT', $CJendTime)
                    );
                    $where3 = array(
                        'case_status' => 'shenpi',
                        'dispatch_time' => array('between', array($SPstarTime, $SPendTime))
                    );
                    $where4 = array(
                        'case_status' => 'shenpi',
                        'dispatch_time' => array('GT', $SPendTime)
                    );
                    $where3 = array(
                        'case_status' => 'chuzhi',
                        'dispatch_time' => array('between', array($CZstarTime, $CZendTime))
                    );
                    $where4 = array(
                        'case_status' => 'chuzhi',
                        'dispatch_time' => array('GT', $CZendTime)
                    );
                    $CJjijiang = M('case')->where($where1)->count();
                    $CJchaoshi = M('case')->where($where2)->count();
                    $SPjijiang = M('case')->where($where3)->count();
                    $SPchaoshi = M('case')->where($where4)->count();
                    $CZjijiang = M('case')->where($where3)->count();
                    $CZchaoshi = M('case')->where($where4)->count();
                    $this->jijiang = $CJjijiang + $SPjijiang + $CZjijiang;
                    $this->chaoshi = $CJchaoshi + $SPchaoshi + $CZchaoshi;
                }
                $this->display();
            }
        } else {
            $this->redirect('Public/login');
        }
    }
//    public function index()
//    {
//        if (UID) {
//            if (IS_ROOT) { //管理员首页
//                $this->meta_title = '案件列表';
//                $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
//                $this->CaseList = M('case')->select();
//                $this->uid = $uid;
//                $user_info = array(
//                    'username' => $_SESSION['onethink_admin']['user_auth']['username'],
//                    'last_login_time' => $_SESSION['onethink_admin']['user_auth']['last_login_time'],
//                );
//                var_dump($user_info);
//                $this->assign('user_info', $user_info);
//                $this->display();
//            } else { //普通用户首页
//                $this->meta_title = '案件列表';
//                $user_info = array(
//                    'username' => $_SESSION['onethink_admin']['user_auth']['username'],
//                    'last_login_time' => $_SESSION['onethink_admin']['user_auth']['last_login_time'],
//                );
//                //根据用户id，查询用户上次登录ip
//                $ucenter_member = M('ucenter_member');
//                $where = array(
//                    'id' => intval($_SESSION['onethink_admin']['user_auth']['uid']),
//                );
//                $user_info['last_login_ip'] = $ucenter_member->where($where)->getField('last_login_ip');
//
//                $this->assign('user_info', $user_info);
//
//                $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
//                if ($uid == 1) {
//                    $where = ('turn_related = 1 and case_status = diaodu');
//                }
//                if ($uid == 2) {
//                    $where = ('case_status = "chushen"');
//                }
//                if ($uid == 3) {
//                    $where = ('case_status = "diaodu" and turn_related = 3 or case_status = "weihuifang"');
//                }
//                if ($uid == 4) {
//                    $where = ('case_status = "caiji" or case_status = "shenpi" or case_status = "chizhi"');
//                }
//                $this->xiaoxiList = M('case')->where($where)->order('fill_in_time desc')->select();
//
//                $this->CaseList = M('case')->select();
//                $this->zaichu = M('case')->where($where)->count();
//                $this->uid = $uid;
//
//                if ($uid == 1) {
//                    $theTime = M('case')->where(array('turn_related' => 1, 'case_status' => 'diaodu'))->getField('dispatch_time');
//                    $starTime = strtotime($theTime) + strtotime("8 day");
//                    $endTime = strtotime($theTime) + strtotime("10 day");
//                    $where1 = array(
//                        'turn_related' => 1,
//                        'case_status' => 'diaodu',
//                        'dispatch_time' => array('between', array($starTime, $endTime))
//                    );
//                    $where2 = array(
//                        'turn_related' => 1,
//                        'case_status' => 'diaodu',
//                        'dispatch_time' => array('GT', $endTime)
//                    );
//                    $this->jijiang = M('case')->where($where1)->count();
//                    $this->chaoshi = M('case')->where($where2)->count();
//                }
//                if ($uid == 2) {
//                    $theTime = M('case')->where(array('case_status' => 'chushen'))->getField('trial_time');
//                    $starTime = strtotime($theTime) + strtotime("8 day");
//                    $endTime = strtotime($theTime) + strtotime("10 day");
//                    $where1 = array(
//                        'case_status' => 'chushen',
//                        'dispatch_time' => array('between', array($starTime, $endTime))
//                    );
//                    $where2 = array(
//                        'case_status' => 'chushen',
//                        'dispatch_time' => array('GT', $endTime)
//                    );
//                    $this->jijiang = M('case')->where($where1)->count();
//                    $this->chaoshi = M('case')->where($where2)->count();
//                }
//                if ($uid == 3) {
//                    $theTime = M('case')->where(array('case_status="diaodu" and turn_related=3 or case_status="weihuifang"'))->field('dispatch_time,finish_time')->find();
//                    $DDstarTime = strtotime($theTime['dispatch_time']) + strtotime("8 day");
//                    $DDendTime = strtotime($theTime['dispatch_time']) + strtotime("10 day");
//                    $JAstarTime = strtotime($theTime['finish_time']) + strtotime("8 day");
//                    $JAendTime = strtotime($theTime['finish_time']) + strtotime("10 day");
//                    $where1 = array(
//                        'turn_related' => 3,
//                        'case_status' => 'diaodu',
//                        'dispatch_time' => array('between', array($DDstarTime, $DDendTime))
//                    );
//                    $where2 = array(
//                        'turn_related' => 3,
//                        'case_status' => 'diaodu',
//                        'dispatch_time' => array('GT', $DDendTime)
//                    );
//                    $where3 = array(
//                        'case_status' => 'jiean',
//                        'dispatch_time' => array('between', array($JAstarTime, $JAendTime))
//                    );
//                    $where4 = array(
//                        'case_status' => 'jiean',
//                        'dispatch_time' => array('GT', $JAendTime)
//                    );
//                    $DDjijiang = M('case')->where($where1)->count();
//                    $DDchaoshi = M('case')->where($where2)->count();
//                    $JAjijiang = M('case')->where($where3)->count();
//                    $JAchaoshi = M('case')->where($where4)->count();
//                    $this->jijiang = $DDjijiang + $JAjijiang;
//                    $this->chaoshi = $DDchaoshi + $JAchaoshi;
//                }
//                if ($uid == 4) {
//                    $theTime = M('case')->where(array('case_status = "caiji" or case_status = "shenpi" or case_status = "chizhi"'))->field('fill_in_time,last_instance_time,deal_with_time')->find();
//                    $CJstarTime = strtotime($theTime['fill_in_time']) + strtotime("8 day");
//                    $CJendTime = strtotime($theTime['fill_in_time']) + strtotime("10 day");
//                    $SPstarTime = strtotime($theTime['last_instance_time']) + strtotime("8 day");
//                    $SPendTime = strtotime($theTime['last_instance_time']) + strtotime("10 day");
//                    $CZstarTime = strtotime($theTime['deal_with_time']) + strtotime("8 day");
//                    $CZendTime = strtotime($theTime['deal_with_time']) + strtotime("10 day");
//                    $where1 = array(
//                        'case_status' => 'caiji',
//                        'dispatch_time' => array('between', array($CJstarTime, $CJendTime))
//                    );
//                    $where2 = array(
//                        'case_status' => 'caiji',
//                        'dispatch_time' => array('GT', $CJendTime)
//                    );
//                    $where3 = array(
//                        'case_status' => 'shenpi',
//                        'dispatch_time' => array('between', array($SPstarTime, $SPendTime))
//                    );
//                    $where4 = array(
//                        'case_status' => 'shenpi',
//                        'dispatch_time' => array('GT', $SPendTime)
//                    );
//                    $where3 = array(
//                        'case_status' => 'chuzhi',
//                        'dispatch_time' => array('between', array($CZstarTime, $CZendTime))
//                    );
//                    $where4 = array(
//                        'case_status' => 'chuzhi',
//                        'dispatch_time' => array('GT', $CZendTime)
//                    );
//                    $CJjijiang = M('case')->where($where1)->count();
//                    $CJchaoshi = M('case')->where($where2)->count();
//                    $SPjijiang = M('case')->where($where3)->count();
//                    $SPchaoshi = M('case')->where($where4)->count();
//                    $CZjijiang = M('case')->where($where3)->count();
//                    $CZchaoshi = M('case')->where($where4)->count();
//                    $this->jijiang = $CJjijiang + $SPjijiang + $CZjijiang;
//                    $this->chaoshi = $CJchaoshi + $SPchaoshi + $CZchaoshi;
//                }
//                $this->display();
//            }
//        } else {
//            $this->redirect('Public/login');
//        }
//    }

}
