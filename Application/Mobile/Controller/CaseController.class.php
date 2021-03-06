<?php

namespace Mobile\Controller;

use Mobile\Model\AuthGroupModel;
use Think\Auth;
use Think\phpexcel;

/**
 * 后台内容控制器
 */
class CaseController extends MobileController
{
    public function caseList()
    {
        $action = I('action');
        switch ($action) {
            case 'suoyou':
                exit($this->suoyou());
                break;
            case 'zaichu':
                exit($this->zaichu());
                break;
            case 'daichu':
                exit($this->daichu());
                break;
            case 'guidang':
                exit($this->guidang());
                break;
            case 'wancheng':
                exit($this->wancheng());
                break;
            case 'jijiang':
                exit($this->jijiang());
                break;
            case 'chaoshi':
                exit($this->chaoshi());
                break;
            case 'chaoling':
                exit($this->chaoling());
                break;
            default:
                exit($this->suoyou());
                break;
        }
    }

    /**
     * 检测需要动态判断的案件类目有关的权限
     */
    protected function checkDynamic()
    {
        if (IS_ROOT) {
            return true;//管理员允许访问任何页面
        }
        $cates = AuthGroupModel::getAuthCategories(UID);
        switch (strtolower(ACTION_NAME)) {
            case 'index':   //文档列表
                $cate_id = I('cate_id');
                break;
            case 'edit':    //编辑
            case 'update':  //更新
                $doc_id = I('id');
                $cate_id = M('Document')->where(array('id' => $doc_id))->getField('category_id');
                break;
            case 'setstatus': //更改状态
            case 'permit':    //回收站
                $doc_id = (array)I('ids');
                $cate_id = M('Document')->where(array('id' => array('in', $doc_id)))->getField('category_id', true);
                $cate_id = array_unique($cate_id);
                break;
        }
        if (!$cate_id) {
            return null;//不明,需checkRule
        } elseif (!is_array($cate_id) && in_array($cate_id, $cates)) {
            return true;//有权限
        } elseif (is_array($cate_id) && $cate_id == array_intersect($cate_id, $cates)) {
            return true;//有权限
        } else {
            return false;//无权限
        }
        return null;//不明,需checkRule
    }

    /**
     * 显示左边菜单，进行权限控制
     */
    protected function getMenu()
    {
        //获取动态分类
        $cate_auth = AuthGroupModel::getAuthCategories(UID);    //获取当前用户所有的内容权限节点
        $cate_auth = $cate_auth == null ? array() : $cate_auth;
        $cate = M('Category')->where(array('status' => 1))->field('id,title,pid,allow_publish')->order('pid,sort')->select();

        //没有权限的分类则不显示
        if (!IS_ROOT) {
            foreach ($cate as $key => $value) {
                if (!in_array($value['id'], $cate_auth)) unset($cate[$key]);
            }
        }
        $cate = list_to_tree($cate);    //生成分类树
        //获取分类id
        $cate_id = I('param.cate_id');
        $this->cate_id = $cate_id;
        //是否展开分类
        $hide_cate = false;
        if (ACTION_NAME != 'recycle' && ACTION_NAME != 'draftbox' && ACTION_NAME != 'mydocument') {
            $hide_cate = true;
        }
        //生成每个分类的url
        foreach ($cate as $key => &$value) {
            $value['url'] = 'Article/index?cate_id=' . $value['id'];
            if ($cate_id == $value['id'] && $hide_cate) {
                $value['current'] = true;
            } else {
                $value['current'] = false;
            }
            if (!empty($value['_child'])) {
                $is_child = false;
                foreach ($value['_child'] as $ka => &$va) {
                    $va['url'] = 'Article/index?cate_id=' . $va['id'];
                    if (!empty($va['_child'])) {
                        foreach ($va['_child'] as $k => &$v) {
                            $v['url'] = 'Article/index?cate_id=' . $v['id'];
                            $v['pid'] = $va['id'];
                            $is_child = $v['id'] == $cate_id ? true : false;
                        }
                    }
                    //展开子分类的父分类
                    if ($va['id'] == $cate_id || $is_child) {
                        $is_child = false;
                        if ($hide_cate) {
                            $value['current'] = true;
                            $va['current'] = true;
                        } else {
                            $value['current'] = false;
                            $va['current'] = false;
                        }
                    } else {
                        $va['current'] = false;
                    }
                }
            }
        }
        $this->assign('nodes', $cate);
        $this->assign('cate_id', $this->cate_id);

        //获取面包屑信息
        $nav = get_parent_category($cate_id);
        $this->assign('rightNav', $nav);

        //获取回收站权限
        $show_recycle = $this->checkRule('Admin/article/recycle');
        $this->assign('show_recycle', IS_ROOT || $show_recycle);
        //获取草稿箱权限
        $this->assign('show_draftbox', C('OPEN_DRAFTBOX'));
    }

    //案件列表
    public function case_list()
    {
        $id = $_GET['id'];
        $list = array();
        $img = array();
        if (!empty($id)) {
            $list = M('case')->field('id,name,fill_in_person,case_number,case_status,dispatch_person')->where(array('id' => $id))->find();
            $img = array(
                'caiji' => 2,
                'bohuiC' => 1,
                'chushen' => 3,
                'bohuiCs' => 2,
                'shenpi' => 4,
                'diaodu' => 5,
                'bohuiCz' => 5,
                'chuzhi' => 6,
                'weihuifang' => 7,
                'jiean' => 7,
                'huifang' => 8,
            );
        }
        exit(json_encode(array('case' => $list, 'code' => $img[$list['case_status']])));
    }

    //
    public function getMessages()
    {
        $case = M('case');
        $auth = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $auth['status'])],
        ];
        if (UID != 1) {
            $user = M('member')->where(array('uid' => UID))->find();
            switch ($user['department']) {
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
        $field = ['id', 'name', 'case_status', 'fill_in_time', 'case_number', 'add_time', 'trial_time', 'last_instance_time', 'dispatch_time', 'deal_with_time', 'finish_time', 'visit_time'];
        //消息中心
        $count = $case->field($field)->where($where)->count();
        $pagesize = 20;
        $p = I('pageIndex') ? I('pageIndex') : 1;
        /* 计算分页信息 */
        $totalPages = ceil($count / $pagesize); //总页数
        if (!empty($totalPages) && $p > $totalPages) {
            $p = $totalPages;
        }
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $messages = $case->field($field)->where($where)->order('fill_in_time desc')->limit($limit)->select();
        foreach ($messages as &$vs) {
            $vs['case_statuss'] = getStage($vs['case_status'], true);
            $vs['case_time'] = $vs[statusTime($vs['case_status'])];
        }
        exit(json_encode(array('messages' => $messages)));
    }

    //所有案件
    private function suoyou()
    {
        $where = array();
        if (UID != 1) {
            $user = M('member')->where(array('uid' => UID))->find();
            switch ($user['department']) {
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
        $count = handle(M('case')->where($where)->select(), false);
        $pagesize = 20;
        $p = I('pageIndex') ? I('pageIndex') : 1;
        /* 计算分页信息 */
        $totalPages = ceil($count / $pagesize); //总页数
        if (!empty($totalPages) && $p > $totalPages) {
            $p = $totalPages;
        }
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $caseList = handle(M('case')->where($where)->order('fill_in_time desc')->limit($limit)->select());
        $usid = array();
        $uid = M('auth_group_access')->where(array('uid' => UID))->field('group_id')->select();
        foreach ($uid as $key => $value) {
            if (is_array($value)) {
                $usid[$key] = $value['group_id'];
            } else {
                $usid[$key] = $value['group_id'];
            }
        }
        return json_encode(array('list' => $caseList, 'uid' => $usid));
    }

    //待处理
    private function daichu()
    {
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $auth = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $auth['status'])],
            'stage_status' => 'complete',
        ];
        if (UID != 1) {
            $user = M('member')->where(array('uid' => UID))->find();
            switch ($user['department']) {
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
        $case = M('case');
        $count = handle($case->where($where)->select(), false);
        $pagesize = 20;
        $p = I('pageIndex') ? I('pageIndex') : 1;
        /* 计算分页信息 */
        $totalPages = ceil($count / $pagesize); //总页数
        if (!empty($totalPages) && $p > $totalPages) {
            $p = $totalPages;
        }
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $caseList = handle($case->where($where)->order('fill_in_time desc')->limit($limit)->select());
        $this->shequ = M('area_top')->where(array('type_id' => 5))->select();
        return json_encode(array('list' => $caseList, 'uid' => $uid));
    }

    //正在处理
    private function zaichu()
    {
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $auth = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $auth['status'])],
            'stage_status' => 'ing',
        ];
        if (UID != 1) {
            $user = M('member')->where(array('uid' => UID))->find();
            switch ($user['department']) {
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
        $case = M('case');
        $count = handle($case->where($where)->select() , false);
        $pagesize = 20;
        $p = I('pageIndex') ? I('pageIndex') : 1;
        /* 计算分页信息 */
        $totalPages = ceil($count / $pagesize); //总页数
        if (!empty($totalPages) && $p > $totalPages) {
            $p = $totalPages;
        }
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $caseList = handle($case->where($where)->order('fill_in_time desc')->limit($limit)->select());
        return json_encode(array('list' => $caseList, 'uid' => $uid));
    }

    //归档   
    private function guidang()
    {
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $where = [
            'case_status' => ['in', 'jiean,huifang'],
        ];
        if (UID != 1) {
            $user = M('member')->where(array('uid' => UID))->find();
            switch ($user['department']) {
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
        $count = handle(M('case')->where($where)->order('fill_in_time desc')->select(), false);
        $pagesize = 20;
        $p = I('pageIndex') ? I('pageIndex') : 1;
        /* 计算分页信息 */
        $totalPages = ceil($count / $pagesize); //总页数
        if (!empty($totalPages) && $p > $totalPages) {
            $p = $totalPages;
        }
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $caseList = handle(M('case')->where($where)->limit($limit)->order('fill_in_time desc')->select());
        return json_encode(array('list' => $caseList, 'uid' => $uid));
    }

    //完成处理
    private function wancheng()
    {
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $where = [
            'case_status' => ['in', 'jiean,huifang'],
        ];
        if (UID != 1) {
            $user = M('member')->where(array('uid' => UID))->find();
            switch ($user['department']) {
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
        $count = handle(M('case')->where($where)->order('fill_in_time desc')->select(), false);
        $pagesize = 20;
        $p = I('pageIndex') ? I('pageIndex') : 1;
        /* 计算分页信息 */
        $totalPages = ceil($count / $pagesize); //总页数
        if (!empty($totalPages) && $p > $totalPages) {
            $p = $totalPages;
        }
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $caseList = handle(M('case')->where($where)->order('fill_in_time desc')->limit($limit)->select());
        return json_encode(array('list' => $caseList, 'uid' => $uid));
    }

    //即将超时
    private function jijiang()
    {
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $status = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $status['status'])],
        ];
        if (UID != 1) {
            $user = M('member')->where(array('uid' => UID))->find();
            switch ($user['department']) {
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
        $list = handle(M('case')->where($where)->order('fill_in_time desc')->select());
        $count = $this->countOverTimeCases($list, false);
        $pagesize = 25;
        $p = I('pageIndex') ? I('pageIndex') : 1;
        $start = ($p - 1) * $pagesize;
        $caseList = array();
        $s = 0;
        for ($i = $start; $i < count($count); $i++, $s++) {
            if ($s < $pagesize) $caseList[] = $count[$i];
        }
        return json_encode(array('list' => $caseList, 'uid' => $uid));
    }

    //超时案件
    private function chaoshi()
    {
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $status = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $status['status'])],
        ];
        if (UID != 1) {
            $user = M('member')->where(array('uid' => UID))->find();
            switch ($user['department']) {
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
        $list = handle(M('case')->where($where)->order('fill_in_time desc')->select());
        $count = $this->countOverTimeCases($list, true);
        $pagesize = 25;
        $p = I('pageIndex') ? I('pageIndex') : 1;
        $start = ($p - 1) * $pagesize;
        $caseList = array();
        $s = 0;
        for ($i = $start; $i < count($count); $i++, $s++) {
            if ($s < $pagesize) $caseList[] = $count[$i];
        }
        return json_encode(array('list' => $caseList, 'uid' => $uid));
    }

    /**
     * 计算 超时/即将超时 案件
     * @param array $cases 案件数组
     * @param boolen $action 默认true查询超时案件
     * @param boolen $ifpost 默认true 非流程节点的提交
     * return array $list 返回数组包含键值overtiming, overtimed
     * overtiming为即将超时, overtimed为已经超时
     */
    private function countOverTimeCases($cases, $action = true, $ifpost = true)
    {
        //初始化返回结果
        $list = [];
        $now = time();
        $execute = $ifpost ? '' : $now;
        $rules = M('CaseManage')->field(['node', 'warn_time', 'execute_time'])->where(['status' => 1])->select();
        $rules = rebuildArray($rules, 'node');
        $model = D('overtime');
        $case = M('case');
        foreach ($cases as $k => $v) {
            $status = $v['case_status'];
            $key = translate($status);
            //现阶段该执行的状态的时间
            $caseTime = strtotime($v[$key['now'] . '_time']);
            if (!empty($caseTime)) {
                //下一阶段该执行的状态的时间
                $nextTime = strtotime($v[$key['next'] . '_time']);
                //下一阶段为false为驳回
                $time = empty($nextTime) ? $caseTime : $nextTime;
                $overtiming = $time + $rules[$status]['warn_time'] * 3600;
                $overtimed = $time + $rules[$status]['execute_time'] * 3600;
                if ($action) {
                    if ($now >= $overtimed) {
                        $list[] = $v;//超时
                        $save = [
                            'case' => $v['id'],
                            'status' => $status,
                            'terminal' => $overtimed,
                            'execute' => $execute,
                            'node' => $v['stage_status'],
                        ];
                        $model->update($save);

                        //如果为流程节点的提交则不更改为超时
                        if ($ifpost) $case->save(['id' => $v['id'], 'stage_status' => 'overtime']);
                    }
                } else {
                    if ($now >= $overtiming && $now <= $overtimed) $list[] = $v;//即将超时
                }
            }
        }
        return $list;
    }

    //超龄案件
    private function chaoling()
    {
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $auth = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $auth['status'])],
        ];
        if (UID != 1) {
            $user = M('member')->where(array('uid' => UID))->find();
            switch ($user['department']) {
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
        $list = handle(M('case')->where($where)->order('fill_in_time desc')->select());
        $count = array();
        $overage = empty(C('OVERAGE_CASE')) ? 18 : C('OVERAGE_CASE');
        foreach ($list as $val) {
            if (getAge($val['birthday']) > $overage) $count[] = $val;
        }
        $pagesize = 25;
        $p = I('pageIndex') ? I('pageIndex') : 1;
        $start = ($p - 1) * $pagesize;
        $caseList = array();
        $s = 0;
        for ($i = $start; $i < count($count); $i++, $s++) {
            if ($s < $pagesize) $caseList[] = $count[$i];
        }
        return json_encode(array('list' => $caseList, 'uid' => $uid));
    }

    //处理数据
    public function Handle($list)
    {
        $list_top = M('area_top')->select();
        $arr = array();
        foreach ($list_top as $k => $v) {
            if ($list['area_code'] == $v['region_id']) {
                $arr['area_code']['k'] = $v['region_id'];
                $arr['area_code']['v'] = $v['region_name'];
            }
            if ($list['street_code'] == $v['region_id']) {
                $arr['street_code']['k'] = $v['region_id'];
                $arr['street_code']['v'] = $v['region_name'];
            }
            if ($list['community_code'] == $v['region_id']) {
                $arr['community_code']['k'] = $v['region_id'];
                $arr['community_code']['v'] = $v['region_name'];
            }
        }
        $lists = M('area')->select();
        foreach ($lists as $k => $v) {
            if ($list['household_pro_code'] == $v['region_id']) {
                $arr['household_pro_code']['k'] = $v['region_id'];
                $arr['household_pro_code']['v'] = $v['region_name'];
            }
            if ($list['household_city_code'] == $v['region_id']) {
                $arr['household_city_code']['k'] = $v['region_id'];
                $arr['household_city_code']['v'] = $v['region_name'];
            }
            if ($list['household_area_code'] == $v['region_id']) {
                $arr['household_area_code']['k'] = $v['region_id'];
                $arr['household_area_code']['v'] = $v['region_name'];
            }
            if ($list['home_pro_code'] == $v['region_id']) {
                $arr['home_pro_code']['k'] = $v['region_id'];
                $arr['home_pro_code']['v'] = $v['region_name'];
            }
            if ($list['home_city_code'] == $v['region_id']) {
                $arr['home_city_code']['k'] = $v['region_id'];
                $arr['home_city_code']['v'] = $v['region_name'];
            }
            if ($list['home_area_code'] == $v['region_id']) {
                $arr['home_area_code']['k'] = $v['region_id'];
                $arr['home_area_code']['v'] = $v['region_name'];
            }
        }
        $list = array_merge($list, $arr);
        $Case = array();
        $str = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/Uploads/case/user/photo/';
        $strs = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/Uploads/case/user/file/';
        $field = array(
            'birthday',
            'health',
            'family_jianhuren',
            'family_members',
            'enjoy_relief_type',
            'inner_predicament',
            'growth_dilemma',
            'growth_dilemmas',
            'checkbox_status',
            'management_record',
            'visit_suggestion',
            'turn_relateds'
        );
        foreach ($list as $k => $v) {
            if (in_array($k, $field)) {
                if ($k == 'health' && $v == 1) {
                    $Case[$k] = $v;
                } else {
                    $Case[$k] = unserialize($list[$k]);
                }
            } else if ($k == 'photo') {
                $Case[$k] = !empty($v) ? $str . $list[$k] : '';
            } else if ($k == 'enclosure') {
                $enclosure = unserialize($list[$k]);
                foreach ($enclosure as $vv) {
                    $Case[$k][] = !empty($vv) ? $strs . $vv : '';
                }
            } else {
                $Case[$k] = $v;
            }
        }
        return $Case;
    }

    //区街社
    public function Related()
    {
        $list_top = M('area_top')->where('region_id!=0')->select();
        $ac = array(
            '1' => '区县民政',
            '2' => '街道民政',
            '3' => '社区民政',
        );
        return linkages($list_top, $ac);
    }

    //地区
    public function Lage()
    {
        $list = M('area')->where('parent_id!=0')->select();
        $are = array();
        $town = array();
        $village = array();
        foreach ($list as $k => &$v) {
            if ($v['region_type'] == 1) $are[] = $v;
            if ($v['region_type'] == 2) $town[] = $v;
            if ($v['region_type'] == 3) $village[] = $v;
        }
        $area = linkage($are, $town, $village, 1);
        $list_top = M('area_top')->where('region_id!=0')->select();
        $are = array();
        $town = array();
        $village = array();
        foreach ($list_top as $k => &$v) {
            if ($v['type_id'] == 3) $are[] = $v;
            if ($v['type_id'] == 4) $town[] = $v;
            if ($v['type_id'] == 5) $village[] = $v;
        }
        $top = linkage($are, $town, $village, 1);
        return array('area' => $area, 'area_top' => $top);
    }

    //信息采集
    public function index()
    {
        if (IS_POST && IS_AUTH) {
            if (!$_POST['case_number']) $data['case_number'] = date("Ymd", time()) . rand(1000, 9999);
            if ($_POST['area_code']) $data['area_code'] = $_POST['area_code'];
            if ($_POST['street_code']) $data['street_code'] = $_POST['street_code'];
            if ($_POST['community_code']) $data['community_code'] = $_POST['community_code'];
            if ($_POST['name']) $data['name'] = $_POST['name'];
            if ($_POST['sex']) $data['sex'] = $_POST['sex'];
            if ($_POST['nation']) $data['nation'] = $_POST['nation'];
            if ($_POST['year']) $data['year'] = $_POST['year'];
            if ($_POST['month']) $data['month'] = $_POST['month'];
            $data['birthday'] = serialize(array($data['year'], $data['month']));
            if ($_POST['household_pro_code']) $data['household_pro_code'] = $_POST['household_pro_code'];
            if ($_POST['household_city_code']) $data['household_city_code'] = $_POST['household_city_code'];
            if ($_POST['household_area_code']) $data['household_area_code'] = $_POST['household_area_code'];
            if ($_POST['home_pro_code']) $data['home_pro_code'] = $_POST['home_pro_code'];
            if ($_POST['home_city_code']) $data['home_city_code'] = $_POST['home_city_code'];
            if ($_POST['home_area_code']) $data['home_area_code'] = $_POST['home_area_code'];
            if ($_POST['home_address']) $data['home_address'] = $_POST['home_address'];
            if ($_POST['enclosure']) $data['enclosure'] = $_POST['enclosure'];
            if ($_POST['health'] == 1) {
                $data['health'] = $_POST['health'];
            } else {
                $data['health'] = serialize(explode(',', $_POST['health']));
            }
            if ($_POST['health_other']) $data['health_other'] = $_POST['health_other'];
            if ($_POST['character']) $data['character'] = $_POST['character'];
            if ($_POST['admission_status']) $data['admission_status'] = $_POST['admission_status'];
            if ($_POST['admission_status'] == 2) $data['entrance'] = $_POST['entrance'];
            //家庭成员分类
            $data['family_jianhuren'] = serialize(explode(',', $_POST['family_jianhuren']));
            $data['family_members'] = serialize(explode(',', $_POST['family_members']));
            if ($_POST['family_structure']) $data['family_structure'] = $_POST['family_structure'];
            if ($_POST['family_other']) $data['family_other'] = $_POST['family_other'];
            if ($_POST['guardianship']) $data['guardianship'] = $_POST['guardianship'];
            if ($_POST['life_status']) $data['life_status'] = $_POST['life_status'];
            if ($_POST['enjoy_relief_type']) $data['enjoy_relief_type'] = serialize(explode(',', $_POST['enjoy_relief_type']));
            if ($_POST['housing_type']) $data['housing_type'] = $_POST['housing_type'];
            if ($_POST['inner_predicament']) $data['inner_predicament'] = serialize(explode(',', $_POST['inner_predicament']));
            if ($_POST['inner_predicament'] == 7) $data['Heart_other'] = rtrim($_POST['Heart_other'], '"');
            //成长困境及成长等级
            if ($_POST['growth_dilemma1']) $grow['growth_dilemma1'] = $_POST['growth_dilemma1'];
            if ($_POST['growth_dilemma2']) $grow['growth_dilemma2'] = $_POST['growth_dilemma2'];
            if ($_POST['growth_dilemma3']) $grow['growth_dilemma3'] = $_POST['growth_dilemma3'];
            if ($_POST['growth_dilemma4']) $grow['growth_dilemma4'] = $_POST['growth_dilemma4'];
            if ($_POST['growth_dilemma5']) $grow['growth_dilemma5'] = $_POST['growth_dilemma5'];
            if ($_POST['growth_dilemma6']) $grow['growth_dilemma6'] = $_POST['growth_dilemma6'];
            if ($_POST['growth_dilemma7']) $grow['growth_dilemma7'] = $_POST['growth_dilemma7'];
            $data['growth_dilemma'] = serialize($grow);
            if ($_POST['main_dilemma']) $data['main_dilemma'] = $_POST['main_dilemma'];
            $data['fill_in_time'] = time();
            if ($_POST['fill_in_person']) $data['fill_in_person'] = $_POST['fill_in_person'];
            //图片上传
            if ($_POST['photo'] != '') {
                $uploadPath = './Uploads/case/user/photo/';
                $base = explode(',', $_POST['photo'])[1];
                $base = preg_replace("/\s/", "+", $base);
                $base64 = base64_decode($base);
                $fileName = rand(1, 9) . rand(0, 9) . rand(0, 9) . time();
                $res = file_put_contents($uploadPath . $fileName . ".jpg", $base64);
                if ($res) {
                    $data['photo'] = $fileName . ".jpg";
                }
            }
            $enclosure = array();
            $files = explode('@@', $_POST['files']);
            if ($files) {
                foreach ($files as $v) {
                    if ($v != '') {
                        $uploadPath = './Uploads/case/user/file/';
                        $base = explode(',', $v)[1];
                        $base = preg_replace("/\s/", "+", $base);
                        $base64 = base64_decode($base);
                        $fileName = rand(1, 9) . rand(0, 9) . rand(0, 9) . time();
                        $res = file_put_contents($uploadPath . $fileName . ".jpg", $base64);
                        if ($res) {
                            $enclosure[] = $fileName . ".jpg";
                        }
                    }
                }
            }
            if ($enclosure) $data['enclosure'] = serialize($enclosure);
            $data['case_status'] = 'caiji';
            $data['add_time'] = date("Y-m-d H:i:s", time());
            $Case = D('case');
            if (!$Case->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                exit(json_encode(array('erron' => 0, 'error' => $Case->getError())));
            }
            if ($_POST['id']) $data['id'] = $_POST['id'];
            // 采集 or 重新采集完成 更改stage_status
            $data['stage_status'] = 'complete';
            $cases = $Case->where(['id' => $data['id']])->select();
            $this->updateOvertime($cases, strtotime($data['add_time']));
            $case = $Case->update($data);
            if ($case) {
                //发送短信提醒
                $res = $this->smsSend('trial', 'caiji', true);
                exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
            } else {
                exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
            }
        } else {
            $area = $this->Lage();
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            // 点击采集 更改stage_status
            $this->updateStatus($id, $case['case_status']);
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            exit(json_encode(array('id' => $id, 'caseList' => $case, 'member' => $member, 'area' => $area, 'is_auth' => IS_AUTH)));
        }
    }

    //案件初审
    public function trial()
    {
        if (IS_POST && IS_AUTH) {
            $data = $_POST;
            $case = M('case');
            $data['case_status'] = 'chushen';
            //成长困境及成长等级
            if ($_POST['growth_dilemma1']) $grow['growth_dilemma1'] = $_POST['growth_dilemma1'];
            if ($_POST['growth_dilemma2']) $grow['growth_dilemma2'] = $_POST['growth_dilemma2'];
            if ($_POST['growth_dilemma3']) $grow['growth_dilemma3'] = $_POST['growth_dilemma3'];
            if ($_POST['growth_dilemma4']) $grow['growth_dilemma4'] = $_POST['growth_dilemma4'];
            if ($_POST['growth_dilemma5']) $grow['growth_dilemma5'] = $_POST['growth_dilemma5'];
            if ($_POST['growth_dilemma6']) $grow['growth_dilemma6'] = $_POST['growth_dilemma6'];
            if ($_POST['growth_dilemma7']) $grow['growth_dilemma7'] = $_POST['growth_dilemma7'];
            $data['growth_dilemmas'] = serialize($grow);
            if ($_POST['trial_person']) $data['trial_person'] = $_POST['trial_person'];
            if ($_POST['trial_status']) $data['trial_status'] = $_POST['trial_status'];
            $data['trial_time'] = date("Y-m-d H:i:s", time());
            //提交后检测该案件是否超时
            $cases = $case->where(['id' => $data['id']])->select();
            $this->countOverTimeCases($cases, true, false);
            if ($data['trial_status'] == 1) {
                $data['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $data['id']))->save($data);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('last_instance', 'chushen', true);
                    exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
                }
            } elseif ($data['trial_status'] == 2) {
                $arr = array('trial_time' => date("Y-m-d H:i:s", time()), 'case_status' => 'bohuiC');
                $arr['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $data['id']))->save($arr);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('index', 'bohuiC', false);
                    exit(json_encode(array('erron' => 1, 'error' => '案件被驳回')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '案件驳回失败')));
                }
            }
        } else {
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            // 点击初审 更改stage_status
            $this->updateStatus($id, $case['case_status']);
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            exit(json_encode(array('id' => $id, 'caseList' => $case, 'member' => $member, 'is_auth' => IS_AUTH)));
        }
    }

    //案件终审
    public function last_instance()
    {
        if (IS_POST && IS_AUTH) {
            $data = $_POST;
            $case = M('case');
            $data['case_status'] = 'shenpi';
            if ($_POST['last_instance_person']) $data['last_instance_person'] = $_POST['last_instance_person'];
            $data['last_instance_time'] = date("Y-m-d H:i:s", time());
            if ($_POST['last_instance_status']) $data['last_instance_status'] = $_POST['last_instance_status'];
            //提交后检测该案件是否超时
            $cases = $case->where(['id' => $data['id']])->select();
            $this->countOverTimeCases($cases, true, false);
            if ($data['last_instance_status'] == 1) {
                $data['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $data['id']))->save($data);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('dispatch', 'shenpi', true);
                    exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
                }
            } elseif ($data['last_instance_status'] == 2) {
                $arr = array('last_instance_time' => date("Y-m-d H:i:s", time()), 'case_status' => 'bohuiCs');
                $arr['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $data['id']))->save($arr);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('trial', 'bohuiCs', false);
                    exit(json_encode(array('erron' => 1, 'error' => '案件被驳回')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '案件驳回失败')));
                }
            }
        } else {
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            // 点击审批 更改stage_status
            $this->updateStatus($id, $case['case_status']);
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            exit(json_encode(array('id' => $id, 'caseList' => $case, 'member' => $member, 'is_auth' => IS_AUTH)));
        }
    }

    //案件调度
    public function dispatch()
    {
        if (IS_POST && IS_AUTH) {
            $case = M('case');
            $id = $_POST['id'];
            //提交后检测该案件是否超时
            $cases = $case->where(['id' => $id])->select();
            $this->countOverTimeCases($cases, true, false);
            $data['case_status'] = 'diaodu';
            $box = $_POST['checkbox3'];
            if ($box == 1) {
                $arr = array('dispatch_time' => date("Y-m-d H:i:s", time()), 'case_status' => 'chuzhi', 'stage_status' => 'complete');
                $saveCase = $case->where(array('id' => $id))->save($arr);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('finish', 'chuzhi', true);
                    exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
                }
            } elseif ($box == 3) {
                $data['checkbox_status'] = $_POST['checkbox3'];
                $data['turn_professional'] = $_POST['turn_professional'];
                $data['dispatch_instance'] = $_POST['dispatch_instance'];
                if ($_POST['dispatch_person']) $data['dispatch_person'] = $_POST['dispatch_person'];
                $data['dispatch_time'] = date("Y-m-d H:i:s", time());
                $data['stage_status'] = 'complete';
                $data['turn_related'] = $_POST['turn_related'];
                $data['turn_relateds'] = serialize(explode(',', $_POST['turn_relateds']));
                $saveCase = $case->where(array('id' => $id))->save($data);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('deal_with', 'diaodu', true, $_POST['turn_related']);
                    exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
                }
            }
        } else {
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            // 点击调度 更改stage_status
            $this->updateStatus($id, $case['case_status']);
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            exit(json_encode(array('id' => $id, 'caseList' => $case, 'member' => $member, 'top' => $this->Related(), 'is_auth' => IS_AUTH)));
        }
    }

    //案件处置
    public function deal_with()
    {
        if (IS_POST && IS_AUTH) {
            $case = M('case');
            //提交后检测该案件是否超时
            $cases = $case->where(array('id' => $_POST['id']))->select();
            $this->countOverTimeCases($cases, true, false);
            $data['management_status'] = $_POST['management_status'];
            if ($data['management_status'] == 1) {
                $data['case_status'] = 'shenpi';
                if ($_POST['deal_person']) $data['deal_person'] = $_POST['deal_person'];
                $data['deal_with_time'] = date("Y-m-d H:i:s", time());
                $data['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $_POST['id']))->save($data);
                if ($saveCase) {
                    //发送短信提醒finish
                    $res = $this->smsSend('dispatch', 'shenpi', true);
                    exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
                }
            } elseif ($data['management_status'] == 2) {
                $arr['stage_status'] = 'complete';
                $arr = array('deal_with_time' => date("Y-m-d H:i:s", time()), 'case_status' => 'bohuiCz');
                $saveCase = $case->where(array('id' => $_POST['id']))->save($arr);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('dispatch', 'bohuiCz', false);
                    exit(json_encode(array('erron' => 1, 'error' => '案件被驳回')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '案件驳回失败')));
                }
            }
        } else {
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $str = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/Uploads/case/user/deal/';
            $record = $case['management_record'];
            unset($case['management_record']);
            if (!empty($record)) {
                foreach ($record as &$v) {
                    $v['file'] = empty($v['file']) ? '' : $str . $v['file'];
                }
            } else {
                $record = array();
            }
            // 点击处置 更改stage_status
            $this->updateStatus($id, $case['case_status']);
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            exit(json_encode(array('id' => $id, 'caseList' => $case, 'member' => $member, 'record' => $record, 'is_auth' => IS_AUTH)));
        }
    }

    //处置提交
    public function tichu()
    {
        $post = array();
        $urse = M('ucenter_member');
        $member = $urse->where(array('id' => UID))->getField('username');
        $post['type'] = empty($_POST['management_type']) ? '' : $_POST['management_type'];
        if ($_POST['management_file'] != '') {
            $uploadPath = './Uploads/case/user/deal/';
            $base = explode(',', $_POST['management_file'])[1];
            $base = preg_replace("/\s/", "+", $base);
            $base64 = base64_decode($base);
            $fileName = rand(1, 9) . rand(0, 9) . rand(0, 9) . time();
            $res = file_put_contents($uploadPath . $fileName . ".jpg", $base64);
            if ($res) {
                $post['file'] = $fileName . ".jpg";
            }
        }
        $post['department'] = empty($_POST['department']) ? '' : $_POST['department'];
        $post['deal_person'] = empty($_POST['deal_person']) ? '' : $_POST['deal_person'];
        $post['record'] = empty($_POST['management_record']) ? '' : $_POST['management_record'];
        $post['date'] = empty($_POST['management_riqi']) ? date('Y-m-d H:i:s', time()) : $_POST['management_riqi'];
        $post['person'] = empty($_POST['management_person']) ? $member : $_POST['management_person'];
        $id = $_POST['id'];
        if (!empty($id) && !empty($post['record'])) {
            $management_record = M('case')->where(array('id' => $id))->getField('management_record');
            if (empty($management_record)) {
                $list[] = $post;
                $data['management_record'] = serialize($list);
            } else {
                $arr = unserialize($management_record);
                $list[count($arr)] = $post;
                $arr = array_merge($arr, $list);
                $data['management_record'] = serialize($arr);
            }
            $saveData = M('case')->where(array('id' => $id))->save($data);
            if ($saveData) {
                exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
            } else {
                exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
            }
        } else {
            exit(json_encode(array('erron' => 0, 'error' => '处置记录或目标不能为空')));
        }
    }

    //结案
    public function finish()
    {
        if (IS_POST && IS_AUTH) {
            $case = M('case');
            if (!empty($_POST['finish_suggestion'])) $data['finish_suggestion'] = $_POST['finish_suggestion'];
            if (!empty($_POST['visit_status'])) $data['visit_status'] = $_POST['visit_status'];
            if (!empty($_POST['visit_form'])) $data['visit_form'] = $_POST['visit_form'];
            if (!empty($_POST['dilemma_review'])) $data['dilemma_review'] = $_POST['dilemma_review'];
            if (!empty($_POST['help_situation'])) $data['help_situation'] = $_POST['help_situation'];
            if (!empty($_POST['professional_Reflect'])) $data['professional_Reflect'] = $_POST['professional_Reflect'];
            if (!empty($_POST['recommendations'])) $data['recommendations'] = $_POST['recommendations'];
            //成长困境及成长等级
            if ($_POST['growth_dilemma1']) $grow['growth_dilemma1'] = $_POST['growth_dilemma1'];
            if ($_POST['growth_dilemma2']) $grow['growth_dilemma2'] = $_POST['growth_dilemma2'];
            if ($_POST['growth_dilemma3']) $grow['growth_dilemma3'] = $_POST['growth_dilemma3'];
            if ($_POST['growth_dilemma4']) $grow['growth_dilemma4'] = $_POST['growth_dilemma4'];
            if ($_POST['growth_dilemma5']) $grow['growth_dilemma5'] = $_POST['growth_dilemma5'];
            if ($_POST['growth_dilemma6']) $grow['growth_dilemma6'] = $_POST['growth_dilemma6'];
            if ($_POST['growth_dilemma7']) $grow['growth_dilemma7'] = $_POST['growth_dilemma7'];
            $data['growth_dilemmass'] = serialize($grow);
            if ($_POST['finish_person']) $data['finish_person'] = $_POST['finish_person'];
            $data['visit_status'] = empty($_POST['visit_status']) ? '' : $_POST['visit_status'];
            $data['finish_time'] = date("Y-m-d H:i:s", time());
            //提交后检测该案件是否超时
            $cases = $case->where(array('id' => $_POST['id']))->select();
            $this->countOverTimeCases($cases, true, false);
            if ($data['visit_status'] == 1) {
                if ($data['visit_form'] == 1) {
                    $data['visit_way'] = $_POST['visit_form1'];
                } else if ($data['visit_form'] == 2) {
                    $way[] = $_POST['visit_form2_1'];
                    $way[] = $_POST['visit_form2_2'];
                    $data['visit_way'] = serialize($way);
                }
                $data['case_status'] = 'weihuifang';
                $data['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $_POST['id']))->save($data);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('visit', 'weihuifang', true);
                    exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
                }
            } else if ($data['visit_status'] == null) {
                $data['case_status'] = 'jiean';
                $data['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $_POST['id']))->save($data);
                if ($saveCase) {
                    exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => '信息未填写完整')));
                }
            } else {
                exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
            }
        } else {
            $area = $this->Lage();
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $birthday = unserialize($case['birthday']);
            $str = array();
            foreach ($case['growth_dilemma'] as $v1) {
                $str[] = getFengxian($v1);
            }
            $arr = array();
            foreach ($case['growth_dilemmas'] as $v1) {
                $arr[] = getFengxian($v1);
            }
            $growth['growth_dilemma'] = max($str);
            $growth['growth_dilemmas'] = max($arr);
            // 点击结案 更改stage_status
            $this->updateStatus($id, $case['case_status']);
            $this->finishList = M('case')->where(array('id' => $id))->find();
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            exit(json_encode(array('id' => $id, 'caseList' => $case, 'member' => $member, 'area' => $area, 'birthday' => $birthday, 'growth' => $growth, 'is_auth' => IS_AUTH)));
        }
    }

    //回访
    public function visit()
    {
        if (IS_POST && IS_AUTH) {
            $case = M('case');
            $data['case_status'] = 'huifang';
            $data['visit_time'] = date("Y-m-d H:i:s", time());
            $data['stage_status'] = 'complete';
            $id = $_POST['id'];
            //提交后检测该案件是否超时
            $cases = $case->where(['id' => $id])->select();
            $this->countOverTimeCases($cases, true, false);
            $saveCase = $case->where(array('id' => $id))->save($data);
            if ($saveCase) {
                exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
            } else {
                exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
            }
        } else {
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $str = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/Uploads/case/user/deal/';
            $suggestion = $case['visit_suggestion'];
            unset($case['visit_suggestion']);
            if (!empty($suggestion)) {
                foreach ($suggestion as &$v) {
                    $v['file'] = empty($v['file']) ? '' : $str . $v['file'];
                }
            } else {
                $suggestion = array();
            }
            // 点击回访 更改stage_status
            $this->updateStatus($id, $case['case_status']);
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            exit(json_encode(array('id' => $id, 'caseList' => $case, 'member' => $member, 'suggestion' => $suggestion, 'is_auth' => IS_AUTH)));
        }
    }

    //回访提交
    public function tihui()
    {
        $post = array();
        $urse = M('ucenter_member');
        $member = $urse->where(array('id' => UID))->getField('username');
        $post['file'] = empty($_POST['file_name']) ? '' : $_POST['file_name'];
        if ($_POST['file_name'] != '') {
            $uploadPath = './Uploads/case/user/deal/';
            $base = explode(',', $_POST['file_name'])[1];
            $base = preg_replace("/\s/", "+", $base);
            $base64 = base64_decode($base);
            $fileName = rand(1, 9) . rand(0, 9) . rand(0, 9) . time();
            $res = file_put_contents($uploadPath . $fileName . ".jpg", $base64);
            if ($res) {
                $post['file'] = $fileName . ".jpg";
            }
        }
        $post['record'] = empty($_POST['visit_record']) ? '' : $_POST['visit_record'];
        $post['date'] = empty($_POST['visit_riqi']) ? date('Y-m-d H:i:s', time()) : $_POST['visit_riqi'];
        $post['person'] = empty($_POST['visit_person']) ? $member : $_POST['visit_person'];
        $id = $_POST['id'];
        if (!empty($id) && !empty($post['record'])) {
            $management_record = M('case')->where(array('id' => $id))->getField('visit_suggestion');
            if (empty($management_record)) {
                $list[] = $post;
                $data['visit_suggestion'] = serialize($list);
            } else {
                $arr = unserialize($management_record);
                $list[count($arr)] = $post;
                $arr = array_merge($arr, $list);
                $data['visit_suggestion'] = serialize($arr);
            }
            $saveData = M('case')->where(array('id' => $id))->save($data);
            if ($saveData) {
                exit(json_encode(array('erron' => 1, 'error' => '操作成功')));
            } else {
                exit(json_encode(array('erron' => 0, 'error' => '操作失败')));
            }
        } else {
            exit(json_encode(array('erron' => 0, 'error' => '回访记录或目标不能为空')));
        }
    }

    //短信发送$case英文节点名，$node中文节点名
    public function smsSend($case = '', $node = '', $action = true, $turn_related = '')
    {
        $Auth = new \Think\Auth();
        $user = $Auth->getCaseUserList($case);
        $uid = array();
        foreach ($user as $val) {
            $uid[] = $val['uid'];
        }
        $mobiles = M()
            ->table('onethink_ucenter_member a')
            ->where(array("id" => array("in", implode(',', $uid))))
            ->join('onethink_member g on a.id=g.uid')
            ->field('a.mobile as mobiles,g.mobile,a.username,g.nickname,g.status,g.department')
            ->select();
        foreach ($mobiles as $kk => &$vv) {
            if ($vv['status'] == '-1') unset($mobiles[$kk]);
            //判断部门
            if ($node == 'diaodu') if ($vv['department'] != $turn_related) unset($mobiles[$kk]);
            //判断姓名
            if (in_array($node, array('bohuiC', 'bohuiCs'))) if ($vv['nickname'] != $turn_related) unset($mobiles[$kk]);
        }
        $execute = M('caseManage')->where("node='" . $node . "'")->field('node_name,execute_time')->find();
        if (empty($execute)) {
            return array('erron' => 0, 'error' => '案件执行行为的时间没有设置');
        }
        $mobile = array();
        $nickname = array();
        foreach ($mobiles as $k => $v) {
            $mobile[$k] = empty($v['mobile']) ? $v['mobiles'] : $v['mobile'];
            if (!preg_match("/^1[34578]\d{9}$/", $mobile[$k])) {
                return array('erron' => 0, 'error' => '手机号码不正确');
            }
            if (empty($mobile[$k])) unset($mobile[$k]);
            $nickname[$k] = empty($v['nickname']) ? $v['username'] : $v['nickname'];
        }
        $time = time() + $execute['execute_time'] * 3600;
        if ($action) {
            $message = '您好：您有一份新的案件需要在（' . date('Y年m月d日H:i:s', $time) . '）之前处理，请及时登录平台进行' . $execute['node_name'] . '操作。';
        } else {
            $message = '您好：您有一份案件被驳回需要在（' . date('Y年m月d日H:i:s', $time) . '）之前重新处理，请及时登录平台进行' . $execute['node_name'] . '操作。';
        }
        file_put_contents('./message.txt', implode(',', array_unique($mobile)) . $message . implode(',', $nickname), FILE_APPEND);
        return array(implode(',', array_unique($mobile)), $message, implode(',', $nickname));
        $SMS = new SMSAddon();
        return $SMS->AdminIndex(implode(',', array_unique($mobile)), $message);
    }

    /**
     * 根据权限判断是否在权限内访问该案件, 若在则认为处理中并更改状态..
     */
    private function updateStatus($id, $status)
    {
        if ($id <= 0)
            return false;
        $auth = getStatusFromAuth();
        if (in_array($status, $auth['status'])) {
            $data = ['id' => $id, 'stage_status' => 'ing'];
            D('case')->update($data);
        }
    }

    private function updateOvertime($cases, $time)
    {
        $res = $this->countOverTimeCases($cases, true);
        $case = M('CaseManage');
        $model = D('overtime');
        if (!empty($res)) {
            //不为空则已超时
            $arr = ['bohuiC', 'bohuiCs', 'bohuiCz'];
            foreach ($res as $k => $v) {
                $status = $v['case_status'];
                $save = ['case' => $v['id'], 'status' => $status];
                $key = translate($status);
                //现阶段该执行的状态的时间
                if (in_array($status, $arr))
                    $caseTime = strtotime($v[$key['next'] . '_time']);
                else
                    $caseTime = strtotime($v[$key['now'] . '_time']);
                $save['terminal'] = $caseTime + $case->field('execute_time a')->where(['node' => $status])->find()['a'] * 3600;
                $save['execute'] = $time;
                $save['node'] = $v['stage_status'];
                $model->update($save);
            }
        }
    }
}