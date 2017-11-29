<?php

namespace Admin\Controller;

use Admin\Model\AuthGroupModel;
use Think\Page;
use Think\phpexcel;
use Addons\SMS\SMSAddon;

/**
 * 后台内容控制器
 */
class CaseController extends AdminController
{
    /**
     * 检测需要动态判断的案件类目有关的权限
     *
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
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
        $this->meta_title = '案件列表';
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        if (UID == 1) {
            $CaseList = M('case')->select();
        }
        if ($uid == 1) {
            $where = array('turn_related' => $uid, 'management_status' => '');
        }
        if ($uid == 2) {
            $where = array('trial_status' => 1, 'last_instance_status' => '',);
        }
        if ($uid == 3) {
            $where = ('turn_related = ' . $uid . ' AND management_status ="" or visit_status = 1 AND visit_suggestion = ""');
        }
        if ($uid == 4) {
            $where = ('trial_status = "" OR last_instance_status = 1 AND dispatch_instance = "" OR management_status = 1 AND finish_suggestion = ""');
        }
        $this->CaseList = M('case')->where($where)->select();
        $this->uid = $uid;
        $this->display();
    }

    //所有案件
    public function suoyou()
    {
        $this->meta_title = '案件列表';
        if (UID == 1 && I('id')) {
            $res = M('case')->delete(I('id'));
            if ($res) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
        $uid = M('auth_group_access')->where(array('uid' => UID))->field('group_id')->select();

        $where = $assign = [];
        $start = $end = time();
        $type = I('type');
        if (I('submit') || $type == 'enroll') {
            //筛选条件 / 或导出
            $post = I('get.');
            //dump($post);exit;
            //是否手动日期
            if ($post['date'] == 1) {
                $start = strtotime($post['start']);
                $end = strtotime($post['end']);
                $where['fill_in_time'] = ['between', [$start, $end]];
            }
            //根据案件状态选择
            $result = empty($post['status']) ? false : getStatusFromAuth($post['status']);

            $where['name'] = empty($post['name']) ? ['neq', ''] : ['like', "%{$post['name']}%"];
            $where['case_status']    = empty($result) ? ['neq', ''] : ['in', implode(',', $result['status'])];
            $where['area_code']      = empty($post['city']) ? ['neq', 0] : $post['city'];
            $where['street_code']    = empty($post['town']) ? ['neq', 0] : $post['town'];
            $where['community_code'] = empty($post['country']) ? ['neq', 0] : $post['country'];
            $where['sex']            = empty($post['sex']) ? ['neq', 0] : $post['sex'];
            $assign['city']          = $post['city'];
            $assign['town']          = $post['town'];
            $assign['country']       = $post['country'];
            $assign['sex']           = $post['sex'];
            $assign['name']          = $post['name'];
            $assign['date']          = $post['date'];
            $assign['status']        = $post['status'];

            if ($type == 'enroll') {
                //导出..
                $count = M('case')->where($where)->count();
                if ($count > 0)
                    $result = ['result' => 1, 'msg' => '准备下载,请稍等片刻', 'url' => U('Case/enrollListExcel', ['data' => serialize($where)])];
                else
                    $result = ['result' => 0, 'msg' => '无数据可导出'];

                exit(json_encode($result, JSON_UNESCAPED_UNICODE));
            }
        }
        $count = M('case')->where($where)->count();
        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $page = new Page($count, $pagesize);
        $this->Page = $page->show();

        $this->CaseList = M('case')->where($where)->order('fill_in_time desc')->limit($limit)->select();

        $this->shequ = M('area_top')->where(array('type_id' => 4))->select();
        $usid = array();
        foreach ($uid as $key => $value) {
            if (is_array($value)) {
                $usid[$key] = $value['group_id'];
            } else {
                $usid[$key] = $value['group_id'];
            }
        }
        $statuses = [
            ['en' => 'add',             'ch' =>'采集'],
            ['en' => 'trial',           'ch' =>'初审'],
            ['en' => 'last_instance',   'ch' =>'审批'],
            ['en' => 'dispatch',        'ch' =>'调度'],
            ['en' => 'deal_with',       'ch' =>'处置'],
            ['en' => 'finish',          'ch' =>'结案'],
            ['en' => 'visit',           'ch' =>'回访'],
        ];
        $assign['statuses'] = $statuses;
        $assign['start'] = $start;
        $assign['end'] = $end;
        $this->Lage();
        $this->uid = $usid;
        $this->assign($assign);
        $this->display();
    }

    //报名列表excel导出操作

    /**
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function enrollListExcel()
    {
        set_time_limit(0);   //防止时间php脚本处理过期
        $where = unserialize(I('data'));
        $case = M('case');
        $enroll_arr = $case->where($where)->limit('0,3000')->select();
        vendor('phpexcel');
        // 首先创建一个新的对象  PHPExcel object
        $objPHPExcel = new \PHPExcel();
        // 给表格添加数据

        //设置默认 垂直水平居中
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical('center')->setHorizontal('center');


        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '编号')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '性别')
            ->setCellValue('D1', '年龄')
            ->setCellValue('E1', '民族')
            ->setCellValue('F1', '区/县')
            ->setCellValue('G1', '街/镇')
            ->setCellValue('H1', '家庭住址')
            ->setCellValue('I1', '健康状况')
            ->setCellValue('J1', '联系方式')
            ->setCellValue('K1', '摸底上报风险等级')
            ->setCellValue('L1', '机构评估风险等级')
            ->setCellValue('M1', '帮扶后最高风险等级')
            ->setCellValue('N1', '流程');
        //                    ->setCellValue( 'H1', '支付方式' )
        //                    ->setCellValue( 'I1', '支付状态' )
        //                    ->setCellValue( 'J1', '备注' );

        //设置单元格长度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(17);
        //循环插入数据
        foreach ($enroll_arr as $k => $v) {
            $tel = unserialize($v['family_members']);
            $tels = $tel['6'];
            $fengxian = unserialize($v['growth_dilemma']);
            foreach ($fengxian as $v1) {
                $str1[] = getFengxian($v1);
            }
            $mdfx = max($str1);
            $fengxians = unserialize($v['growth_dilemmas']);
            foreach ($fengxians as $v2) {
                $str2[] = getFengxian($v2);
            }
            $jgfx = max($str2);
            $fengxianss = unserialize($v['growth_dilemmass']);
            foreach ($fengxianss as $v3) {
                $str3[] = getFengxian($v3);
            }
            $bffx = max($str3);
            $qu = M('area_top')->where(array('region_id' => $v['area_code']))->getField('region_name');
            $zhen = M('area_top')->where(array('region_id' => $v['street_code']))->getField('region_name');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A' . ($k + 2), $v['case_number'], 's')
                ->setCellValueExplicit('B' . ($k + 2), $v['name'])
                ->setCellValueExplicit('C' . ($k + 2), getSex($v['sex']))
                ->setCellValueExplicit('D' . ($k + 2), getAge($v['birthday']))
                ->setCellValueExplicit('E' . ($k + 2), $v['nation'])
                ->setCellValueExplicit('F' . ($k + 2), $qu)
                ->setCellValueExplicit('G' . ($k + 2), $zhen)
                ->setCellValueExplicit('H' . ($k + 2), $v['home_address'])
                ->setCellValueExplicit('I' . ($k + 2), getHealth($v['health'], $v['id']))
                ->setCellValueExplicit('J' . ($k + 2), $tels, 's')
                ->setCellValueExplicit('K' . ($k + 2), $mdfx)
                ->setCellValueExplicit('L' . ($k + 2), $jgfx)
                ->setCellValueExplicit('M' . ($k + 2), $bffx)
                ->setCellValueExplicit('N' . ($k + 2), getStage($v['case_status'], TRUE));
            //                        ->setCellValue( 'G'.($k+2), $v['enroll_company'])
            //                        ->setCellValue( 'H'.($k+2), $v['enroll_payment'])
            //                        ->setCellValue( 'I'.($k+2), $v['enroll_pay_status'])
            //                        ->setCellValue( 'J'.($k+2), $v['beizhu']);
            $objPHPExcel->getActiveSheet()->getStyle('A' . ($k + 2))->getNumberFormat()
                ->setFormatCode('@');
            $objPHPExcel->getActiveSheet()->getStyle('J' . ($k + 2))->getNumberFormat()
                ->setFormatCode('@');
        }
        // 生成2003excel格式的xls文件
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel;charset=UTF-8');
        header('Content-Disposition: attachment;filename="enroll_' . time() . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //待处理
    public function daichu()
    {
        $this->meta_title = '待处理案件';
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $auth = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $auth['status'])],
            'stage_status' => 'complete',
        ];
        $case = M('case');
        $count = $case->where($where)->count();
        // import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $page = new Page($count, $pagesize);
        $this->Page = $page->show();

        $this->CaseList = $case->where($where)->order('fill_in_time desc')->limit($limit)->select();
//        dump(M('case')->getLastSql());
        $this->shequ = M('area_top')->where(array('type_id' => 5))->select();
        $this->uid = $uid;
        $this->display();
    }

    //正在处理
    public function zaichu()
    {
        $this->meta_title = '正在处理';
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $auth = getStatusFromAuth();
        $where = [
            'case_status' => ['in', implode(',', $auth['status'])],
            'stage_status' => 'ing',
        ];
        $case = M('case');
        $count = $case->where($where)->count();
        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $page = new Page($count, $pagesize);
        $this->Page = $page->show();

        $this->CaseList = $case->where($where)->order('fill_in_time desc')->limit($limit)->select();
        $this->shequ = M('area_top')->where(array('type_id' => 5))->select();
        $this->uid = $uid;
        $this->display();
    }

    //归档
    public function guidang()
    {
        $this->meta_title = '归档案件';
        $count = M('case')->where('case_status = "jiean" or case_status = "huifang"')->order('fill_in_time desc')->count();
        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $page = new Page($count, $pagesize);
        $this->Page = $page->show();
        $this->CaseList = M('case')->where('case_status = "jiean" or case_status = "huifang"')->limit($limit)->order('fill_in_time desc')->select();
        // echo M()->getLastSql();
        $this->shequ = M('area_top')->where(array('type_id' => 5))->select();
        $this->display();
    }

    //完成处理
    public function wancheng()
    {
        $this->meta_title = '完成处理';
        $where = ('case_status = "jiean" or case_status = "huifang"');
        $count = M('case')->where($where)->order('fill_in_time desc')->count();
        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $page = new Page($count, $pagesize);
        $this->Page = $page->show();
        $this->CaseList = M('case')->where($where)->order('fill_in_time desc')->limit($limit)->select();
        // echo M()->getLastSql();
        $this->shequ = M('area_top')->where(array('type_id' => 5))->select();
        $this->display();
    }

    //即将超时
    public function jijiang()
    {
        $this->meta_title = '即将超时案件';
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $status = getStatusFromAuth();
        $where = array("case_status" => array("in", implode(',', $status['status'])));
        $list = M('case')->where($where)->order('fill_in_time desc')->select();
        $count = $this->countOverTimeCases($list, false);
        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $start = ($p - 1) * $pagesize;
        $page = new Page(count($count), $pagesize);
        $this->Page = $page->show();
        $list = array();
        $s = 0;
        for ($i = $start; $i < count($count); $i++, $s++) {
            if ($s < $pagesize) {
                $list[] = $count[$i];
            }
        }
        $this->CaseList = $list;
        $this->uid = $uid;
        $this->display();
    }

    //超时案件
    public function chaoshi()
    {
        $this->meta_title = '超时案件';
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $status = getStatusFromAuth();
        $where = array("case_status" => array("in", implode(',', $status['status'])));
        $list = M('case')->where($where)->order('fill_in_time desc')->select();
        $count = $this->countOverTimeCases($list, true);
        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $start = ($p - 1) * $pagesize;
        $page = new Page(count($count), $pagesize);
        $this->Page = $page->show();
        $list = array();
        $s = 0;
        for ($i = $start; $i < count($count); $i++, $s++) {
            if ($s < $pagesize) {
                $list[] = $count[$i];
            }
        }
        $this->CaseList = $list;
        $this->uid = $uid;
        $this->display();
    }

    /**
     * 计算 超时/即将超时 案件
     * @param array  $cases  案件数组
     * @param boolen $action 默认true查询超时案件
     * @param boolen $ifpost 默认true 非流程节点的提交
     * return array $list 返回数组包含键值overtiming, overtimed
     * overtiming为即将超时, overtimed为已经超时
     */
    private function countOverTimeCases($cases, $action = true, $ifpost = true)
    {
        //初始化返回结果
        $list    = [];
        $now     = time();
        $execute = $ifpost ? '' : $now;
        $rules   = M('CaseManage')->field(['node', 'warn_time', 'execute_time'])->where(['status' => 1])->select();
        $rules  = rebuildArray($rules, 'node');
        $model  = D('overtime');
        $case   = M('case');
        foreach ($cases as $k => $v) {
            $status = $v['case_status'];
            $key = translate($status);
            //现阶段该执行的状态的时间
            $caseTime = strtotime($v[$key['now'] . '_time']);
            if (!empty($caseTime)) {
                //下一阶段该执行的状态的时间
                $nextTime = strtotime($v[$key['next'] . '_time']);
                //下一阶段为false为驳回
                $time       = empty($nextTime) ? $caseTime : $nextTime;
                $overtiming = $time + $rules[$status]['warn_time'] * 3600;
                $overtimed  = $time + $rules[$status]['execute_time'] * 3600;
                if ($action) {
                    if ($now >= $overtimed) {
                        $list[] = $v;//超时
                        $save  = [
                            'case'      => $v['id'], 
                            'status'    => $status,
                            'terminal'  => $overtimed,
                            'execute'   => $execute,
                            'node'      => $v['stage_status'],
                        ];
                        $model->update($save);

                        //如果为流程节点的提交则不更改为超时
                        if($ifpost)
                            $case->save(['id' => $v['id'], 'stage_status' => 'overtime']);
                    }
                } else {
                    if ($now >= $overtiming && $now <= $overtimed) $list[] = $v;//即将超时
                }
            }
        }
        return $list;
    }

    //超龄案件
    public function chaoling()
    {
        $this->meta_title = '超龄案件';
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        $list = M('case')->order('fill_in_time desc')->select();
        $count = array();
        $overage = empty(C('OVERAGE_CASE')) ? 18 : C('OVERAGE_CASE');
        foreach ($list as $val) {
            if  (getAge($val['birthday']) > $overage) $count[] = $val;
        }
        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $start = ($p - 1) * $pagesize;
        $page = new Page(count($count), $pagesize);
        $this->Page = $page->show();
        $list = array();
        $s = 0;
        for ($i = $start; $i < count($count); $i++, $s++) {
            if ($s < $pagesize) {
                $list[] = $count[$i];
            }
        }
        $this->CaseList = $list;
        $this->uid = $uid;
        $this->display();
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
        return array_merge($list, $arr);
    }

    //地区
    public function Lage()
    {
        $list = M('area')->where(array('region_type' => 1))->select();
        $this->assign('list', $list);
        $list_top = M('area_top')->where(array('parent_id' => 1))->select();
        $this->assign('list_top', $list_top);
    }

    //联动菜单
    public function Linkage()
    {
        $id = $_POST['id'];
        $type = $_POST['type'];
        $list = M('area')->where(array('parent_id' => $id))->select();
        if ($type == 1) {
            $str = "<option value='' >请选择市</option>";
        } else if ($type == 2) {
            $str = "<option value='' >请选择区</option>";
        }
        foreach ($list as $v) {
            $str .= "<option value='{$v['region_id']}'>{$v['region_name']}</option>";
        }
        echo $str;
    }

    //联动菜单
    public function Linkage_top()
    {
        $id = $_POST['id'];
        $type = $_POST['type'];
        $list_top = M('area_top')->where(array('parent_id' => $id))->select();
        if ($type == 3) {
            $str = "<option value='' >请选择街/镇</option>";
        } else if ($type == 4) {
            $str = "<option value='' >请选择村/社区</option>";
        }
        foreach ($list_top as $v) {
            $str .= "<option value='{$v['region_id']}'>{$v['region_name']}</option>";
        }
        echo $str;
    }

    //页面刷新
    public function changeYe()
    {
        // $name = $_GET['ACTION_NAME'];
        // echo $name;
    }

    //信息采集
    public function index()
    {
        if (IS_POST) {
            $db = M('case');
            if ($_POST['case_number'] == '后台自动生成') $data['case_number'] = date("Ymd", time()) . rand(1000, 9999);
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
            if ($_POST['health'] == 1) {
                $data['health'] = $_POST['health'];
            } else {
                $data['health'] = serialize($_POST['health']);
            }
            if ($_POST['health_other']) $data['health_other'] = $_POST['health_other'];
            if ($_POST['character']) $data['character'] = $_POST['character'];
            if ($_POST['admission_status']) $data['admission_status'] = $_POST['admission_status'];
            if ($_POST['admission_status'] == 2) $data['entrance'] = $_POST['entrance'];
            //家庭成员分类
            $data['family_jianhuren'] = serialize($_POST['family_jianhuren']);
            $data1 = array_merge($_POST['family_members'], $_POST['family_members1']);
            $data['family_members'] = serialize($data1);
            if ($_POST['family_structure']) $data['family_structure'] = $_POST['family_structure'];
            if ($_POST['family_other']) $data['family_other'] = $_POST['family_other'];
            if ($_POST['guardianship']) $data['guardianship'] = $_POST['guardianship'];
            if ($_POST['life_status']) $data['life_status'] = $_POST['life_status'];
            if ($_POST['enjoy_relief_type']) $data['enjoy_relief_type'] = serialize($_POST['enjoy_relief_type']);
            if ($_POST['housing_type']) $data['housing_type'] = $_POST['housing_type'];
            if ($_POST['inner_predicament']) $data['inner_predicament'] = serialize($_POST['inner_predicament']);
            if (in_array(7, $_POST['inner_predicament'])) $data['Heart_other'] = $_POST['Heart_other'];
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
            if ($_FILES['photo']['name'] != '') {
                $uploadPath = 'case/user/photo';
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath);
                    chmod($uploadPath, 0777);
                }
                $upload = new \Think\Upload(array(
                    'savePath' => $uploadPath,
                    'subName' => false,
                    'uploadReplace' => true // 覆盖同名文件
                ));
                $uploadInfo = $upload->uploadOne($_FILES['photo']);
                $data['photo'] = $uploadInfo['savename'];
            }
            $data['case_status'] = 'caiji';
            $data['add_time'] = date("Y-m-d H:i:s", time());
            $Case = D('case');
            if (!$Case->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($Case->getError(), '', 1);
            } else {
                if ($_POST['xId']) $data['id'] = $_POST['xId'];
                // 采集 or 重新采集完成 更改stage_status
                $data['stage_status'] = 'complete';
              
                //提交后检测该案件是否超时
                $cases = $Case->where(['id' => $data['id']])->select();
                $this->countOverTimeCases($cases, true, false);
              
                $case = $Case->update($data);
                if ($case) {
                    //发送短信提醒
                    $res = $this->smsSend('trial', 'caiji', true);
//                    $this->error(implode(',', $res), '', 10);
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('操作失败');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            }
        } else {
            $this->Lage();
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $this->Clist = $case;
            // 点击采集 更改stage_status
            $this->updateStatus($id, $case['case_status']);
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            $this->assign('member', $member);
            $this->display();
        }
    }

    //案件初审
    public function trial()
    {
        if (IS_POST) {
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

                    $this->error(implode(',', $res), '', 10);
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('操作失败');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            } else {
                $arr = array('trial_time' => date("Y-m-d H:i:s", time()), 'case_status' => 'bohuiC');
                $arr['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $data['id']))->save($arr);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('index', 'bohuiC', false);
                    $this->error(implode(',', $res), '', 10);
                    echo "<script>alert('案件被驳回');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('案件驳回失败');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            }
        } else {
            $this->Lage();
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }

            // 点击初审 更改stage_status
            $this->updateStatus($id, $case['case_status']);

            $this->assign('shi', $shi);
            $this->act = $_GET['act'];
            $this->id = $id;
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            $this->assign('member', $member);
            $this->display();
        }
    }

    //案件终审
    public function last_instance()
    {
        if (IS_POST) {
            $data = $_POST;
            $case = M('case');
            $data['case_status'] = 'shenpi';
            if ($_POST['last_instance_person']) $data['last_instance_person'] = $_POST['last_instance_person'];
            $data['last_instance_time'] = date("Y-m-d H:i:s", time());

            //提交后检测该案件是否超时
            $cases = $case->where(['id' => $data['id']])->select();
            $this->countOverTimeCases($cases, true, false);

            if ($data['last_instance_status'] == 1) {
                $saveCase = $case->where(array('id' => $data['id']))->save($data);
                $data['stage_status'] = 'complete';
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('dispatch', 'shenpi', true);
                    $this->error(implode(',', $res), '', 10);
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('操作失败');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            } else if ($data['last_instance_status'] == 2) {
                $arr = array('last_instance_time' => date("Y-m-d H:i:s", time()), 'case_status' => 'bohuiCs');
                $arr['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $data['id']))->save($arr);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('trial', 'bohuiCs', false);
                    $this->error(implode(',', $res), '', 10);
                    echo "<script>alert('案件被驳回');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('案件驳回失败');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            }
        } else {
            $this->Lage();
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }

            // 点击审批 更改stage_status
            $this->updateStatus($id, $case['case_status']);

            $this->assign('shi', $shi);
            $this->act = $_GET['act'];
            $this->id = $id;
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            $this->assign('member', $member);
            $this->display();
        }
    }

    //案件调度
    public function dispatch()
    {
        if (IS_POST) {
            $case = M('case');
            $data['case_status'] = 'diaodu';
            if ($_POST['checkbox1']) $data['police_station'] = $_POST['police_station'];
            if ($_POST['checkbox2']) $data['turn_professional'] = $_POST['turn_professional'];
            if ($_POST['checkbox3']) $data['turn_related'] = $_POST['turn_related'];
            $check[] = $_POST['checkbox1'];
            $check[] = $_POST['checkbox2'];
            $check[] = $_POST['checkbox3'];
            $data['checkbox_status'] = serialize($check);
            $data['dispatch_instance'] = $_POST['dispatch_instance'];
            if ($_POST['dispatch_person']) $data['dispatch_person'] = $_POST['dispatch_person'];
            $data['dispatch_time'] = date("Y-m-d H:i:s", time());
            $data['stage_status'] = 'complete';

            $id = I('id');
            //提交后检测该案件是否超时
            $cases = $case->where(['id' => $id])->select();
            $this->countOverTimeCases($cases, true, false);

            $saveCase = $case->where(array('id' => $id))->save($data);
            if ($saveCase) {
                //发送短信提醒
                $res = $this->smsSend('deal_with', 'diaodu', true);
                $this->error(implode(',', $res), '', 10);
                echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
            } else {
                echo "<script>alert('信息未填写完整');window.location.href='index.php?s=/Index/index.html';</script>";
            }
        } else {
            $this->Lage();
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }

            // 点击调度 更改stage_status
            $this->updateStatus($id, $case['case_status']);

            $this->assign('shi', $shi);

            $this->act = $_GET['act'];
            $this->id = $id;
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            $this->assign('member', $member);
            $this->display();
        }
    }

    //案件处置
    public function deal_with()
    {
        if (IS_POST) {
            $case = M('case');
            $data['case_status'] = 'chuzhi';
            $data['management_status'] = $_POST['management_status'];
            $data['management_record'] = serialize($_POST['management_record']);
            if ($_POST['deal_person']) $data['deal_person'] = $_POST['deal_person'];
            $data['deal_with_time'] = date("Y-m-d H:i:s", time());

            $id = I('id');
            //提交后检测该案件是否超时
            $cases = $case->where(['id' => $id])->select();
            $this->countOverTimeCases($cases, true, false);

            if ($data['management_status'] == 1) {
                $data['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $id))->save($data);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('finish', 'chuzhi', true);
                    $this->error(implode(',', $res), '', 10);
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('信息未填写完整');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            } else if ($data['management_status'] == 2) {
                $arr = array('deal_with_time' => date("Y-m-d H:i:s", time()), 'case_status' => 'bohuiCz');
                $arr['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $id))->save($arr);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('dispatch', 'bohuiCz', false);
                    $this->error(implode(',', $res), '', 10);
                    echo "<script>alert('案件被驳回');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('案件驳回失败');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            }
        } else {
            $this->Lage();
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }
            $this->assign('shi', $shi);

            // 点击处置 更改stage_status
            $this->updateStatus($id, $case['case_status']);

            $this->act = $_GET['act'];
            $this->id = $id;
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            $this->assign('member', $member);
            $this->display();
        }
    }

    //处置提交
    public function tichu()
    {
        $id = $_POST['id'];
        $va = $_POST['va'];
        $post = array();
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id', 'va'))) {
                $post[$k] = $v;
            }
        }
        $list[$va] = $post;
        $management_record = M('case')->where(array('id' => $id))->getField('management_record');
        if (empty($management_record)) {
            $data['management_record'] = serialize($list);
        } else {
            $arr = unserialize($management_record);
            if (is_array($arr)) {
                if (in_array($va, array_keys($arr))) {
                    foreach ($arr as $k => &$v) {
                        if ($k == $va) $v = $post;
                    }
                } else {
                    $arr = array_merge($arr, $list);
                }
                $data['management_record'] = serialize($arr);
            }
        }
        $saveData = M('case')->where(array('id' => $id))->save($data);
        if ($saveData) {
            echo '1';
        } else {
            echo '2';
        }
    }

    //处置文件提交
    public function ajax_upload()
    {
        $photo = false;
        if ($_FILES['management_file']['name'] != '') {
            $uploadPath = 'case/user/deal/';
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath);
                chmod($uploadPath, 0777);
            }
            $upload = new \Think\Upload(array(
                'savePath' => $uploadPath,
                'subName' => false,
                'uploadReplace' => true // 覆盖同名文件
            ));
            $upload->maxSize = 5120000;// 设置附件上传大小
            $upload->exts = array('jpg', 'png', 'jpeg');// 设置附件上传类型
            $uploadInfo = $upload->uploadOne($_FILES['management_file']);
            if (!$uploadInfo) {
                $photo = $upload->getError();
            } else {
                $photo = $uploadInfo;
            }
        }
        echo json_encode($photo);
    }

    //结案
    public function finish()
    {
        if (IS_POST) {
            $case = M('case');
            if (!empty($_POST['finish_suggestion'])) $data['finish_suggestion'] = $_POST['finish_suggestion'];
            if (!empty($_POST['visit_status'])) $data['visit_status'] = $_POST['visit_status'];
            if (!empty($_POST['visit_form'])) $data['visit_form'] = $_POST['visit_form'];
            if (!empty($_POST['dilemma_review'])) $data['dilemma_review'] = $_POST['dilemma_review'];
            if (!empty($_POST['help_situation'])) $data['help_situation'] = $_POST['help_situation'];
            if (!empty($_POST['professional_Reflect'])) $data['professional_Reflect'] = $_POST['professional_Reflect'];
            if (!empty($_POST['recommendations'])) $data['recommendations'] = $_POST['recommendations'];
            if (!empty($_POST['growth_dilemmass'])) $data['growth_dilemmass'] = $_POST['growth_dilemmass'];
            if ($_POST['finish_person']) $data['finish_person'] = $_POST['finish_person'];

            $id = I('id');
            //提交后检测该案件是否超时
            $cases = $case->where(['id' => $id])->select();
            $this->countOverTimeCases($cases, true, false);

            $data['finish_time'] = date("Y-m-d H:i:s", time());
            if ($data['visit_status'] == 1) {
                if ($data['visit_form'] == 1) {
                    $data['visit_way'] = $_POST['visit_form1'];
                } else if ($data['visit_form'] == 2) {
                    $way[] = $_POST['visit_form2_1'];
                    $way[] = $_POST['visit_form2_2'];
                    $data['visit_way'] = serialize($way);
                }
                $data['case_status']  = 'weihuifang';
                $data['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $id))->save($data);
                if ($saveCase) {
                    //发送短信提醒
                    $res = $this->smsSend('visit', 'weihuifang', true);
                    $this->error(implode(',', $res), '', 10);
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('信息未填写完整');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            } else if ($data['visit_status'] == null) {
                $data['case_status'] = 'jiean';
                $data['stage_status'] = 'complete';
                $saveCase = $case->where(array('id' => $id))->save($data);
                if ($saveCase) {
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('信息未填写完整');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            } else {
                echo "<script>alert('操作失败');window.location.href='index.php?s=/Index/index.html';</script>";
            }
        } else {
            $this->Lage();
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }
            $this->assign('shi', $shi);

            // 点击结案 更改stage_status
            $this->updateStatus($id, $case['case_status']);

            $this->id = $id;
            $this->act = $_GET['act'];
            $this->finishList = M('case')->where(array('id' => $id))->find();
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            $this->assign('member', $member);
            $this->display();
        }
    }

    //回访
    public function visit()
    {
        if (IS_POST) {
            $case = M('case');
            $id = I('id');
            $way = M('case')->where(array('id' => $id))->getField('visit_form');
            if ($way == 1) {
                $data['visit_suggestion'] = $_POST['visit_suggestion'];
            } else if ($way == 2) {
                $data['visit_suggestion'] = serialize($_POST['visit_suggestion']);
            }
            $data['case_status']  = 'huifang';
            $data['visit_time']   = date("Y-m-d H:i:s", time());
            $data['stage_status'] = 'complete';

            //提交后检测该案件是否超时
            $cases = $case->where(['id' => $id])->select();
            $this->countOverTimeCases($cases, true, false);

            $saveCase = $case->where(array('id' => $id))->save($data);
            if ($saveCase) {
                echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
            } else {
                echo "<script>alert('信息未填写完整');window.location.href='index.php?s=/Index/index.html';</script>";
            }
        } else {
            $this->Lage();
            $id = $_GET['id'];
            $case = $this->Handle(M('case')->where(array('id' => $id))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }
            $this->assign('shi', $shi);

            // 点击回访 更改stage_status
            $this->updateStatus($id, $case['case_status']);

            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            $this->assign('member', $member);
            $this->id = $id;
            $this->act = $_GET['act'];
            $this->huifang = M('case')->where(array('id' => $id))->getField('visit_form');
            if ($this->huifang == 2) {
                $cishu1 = M('case')->where(array('id' => $id))->getField('visit_way');
                $cishu2 = unserialize($cishu1);
                $this->cishu = $cishu2[1];
            }
            $this->display();
        }
    }

    //回访提交
    public function tihui()
    {
        $id = $_POST['id'];
        $va = $_POST['va'];
        $visit_suggestion = M('case')->where(array('id' => $id))->getField('visit_suggestion');
        if (is_array(unserialize($visit_suggestion))) {
            $arr = unserialize($visit_suggestion);
            array_push($arr, $_POST['str']);
            $data['visit_suggestion'] = serialize($arr);
        } else if (!empty($visit_suggestion)) {
            $arr[] = unserialize($visit_suggestion);
            array_push($arr, $_POST['str']);
            $data['visit_suggestion'] = serialize($arr);
        } else {
            $data['visit_suggestion'] = serialize($_POST['str']);
        }
        $saveData = M('case')->where(array('id' => $id))->save($data);
        if ($saveData) {
            echo '1';
        } else {
            echo '2';
        }
    }

    //短信发送$case英文节点名，$node中文节点名
    public function smsSend($case = 'trial', $node ='chushen', $action = true)
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
            ->field('a.mobile as mobiles,g.mobile,a.username,g.nickname,g.status')
            ->select();
        foreach ($mobiles as $kk => &$vv) {
            if ($vv['status'] == '-1') unset($mobiles[$kk]);
        }
        $execute = M('caseManage')->where("node='" . $node . "'")->field('node_name,execute_time')->find();
        if (empty($execute)) {
            return array('erron' => 0, 'error' => '案件执行行为的时间没有设置');
        }
        $mobile = array();
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
        return array(implode(',', array_unique($mobile)), $message, implode(',', $nickname));
        $SMS = new SMSAddon();
        return $SMS->AdminIndex(implode(',', array_unique($mobile)), $message);
    }

    //
    public function statistics()
    {
        $this->display();
    }

    /**
      * 根据权限判断是否在权限内访问该案件, 若在则认为处理中并更改状态..
    */
    private function updateStatus($id, $status)
    {
        if($id <= 0)
            return false;
        $auth = getStatusFromAuth();
        if(in_array($status, $auth['status'])){
            $data = ['id' => $id, 'stage_status' => 'ing'];
            D('case')->update($data);
        }
    }

/*    private function updateOvertime($cases, $time){
        $res   = $this->countOverTimeCases($cases, true);
        $case  = M('CaseManage');
        $model = D('overtime');
        if(!empty($res)){
            //不为空则已超时
            $arr = ['bohuiC', 'bohuiCs', 'bohuiCz'];
            foreach ($res as $k => $v) {
                $status = $v['case_status'];
                $save   = ['case' => $v['id'], 'status' => $status];
                $key    = translate($status);
                //现阶段该执行的状态的时间
                if(in_array($status, $arr))
                    $caseTime = strtotime($v[$key['next'] . '_time']);
                else
                    $caseTime = strtotime($v[$key['now'] . '_time']);
                $save['terminal']   = $caseTime + $case->field('execute_time a')->where(['node' => $status])->find()['a'] * 3600;
                $save['execute']    = $time;
                $save['node']       = $v['stage_status'];
                $model->update($save);
            }
        }
    }*/
    //搜索
//    public function search(){
//        $this->CaseList = M('case')->where(array('name'=>array('like',"%".$_POST['search']."%")))->select();
//        $this->shequ = M('area_top')->where(array('type_id'=>5))->select();
//        $this->display('Case/'.$_POST['action']);
//    }
}