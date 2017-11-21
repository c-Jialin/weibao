<?php

namespace Mobile\Controller;

use Mobile\Model\AuthGroupModel;
use Think\Page;
use Think\phpexcel;

/**
 * 后台内容控制器
 */
class CaseController extends MobileController
{
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
        $this->meta_title = '案件列表';
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        if (UID == 1) {
            $CaseList = M('case')->select();
        }
        if ($uid == 1) {
            $where = array(
                'turn_related' => $uid,
                'management_status' => ''
            );
        }
        if ($uid == 2) {
            $where = array(
                'trial_status' => 1,
                'last_instance_status' => '',
            );
        }
        if ($uid == 3) {
            $where = ( 'turn_related = ' . $uid . ' AND management_status ="" or visit_status = 1 AND visit_suggestion = ""');
        }
        if ($uid == 4) {
            $where = ( 'trial_status = "" OR last_instance_status = 1 AND dispatch_instance = "" OR management_status = 1 AND finish_suggestion = ""');
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
        if ($_POST) {
            $where['community_code'] = $_POST['shequ'];
            $where['name'] = array('like', '%' . $_POST['search'] . '%');
        }
        $count = M('case')->where($where)->order('fill_in_time desc')->count();
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
        $this->uid = $usid;
        $this->display();
    }

    //报名列表excel导出操作

    /**
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function enrollListExcel()
    {
        if (I('post.')) {
            set_time_limit(0);   //防止时间php脚本处理过期
            $leix = I('leix');
            $case = M('case');
//            $meet_id = I('meet_id');
            switch ($leix) {
                case 'all' :
                    $enroll_arr = $case->limit('0,3000')->select();
                    break;
            }

            if ($leix != "all") {
                $enroll_arr = $case->where(array('street_code' => $leix))->limit('0,3000')->select();
            }

            if (!empty($enroll_arr)) {
                vendor('phpexcel');
                // 首先创建一个新的对象  PHPExcel object
                $objPHPExcel = new \PHPExcel();
                // 给表格添加数据
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
                        ->setCellValue('A' . ($k + 2), $v['case_number'])
                        ->setCellValue('B' . ($k + 2), $v['name'])
                        ->setCellValue('C' . ($k + 2), getSex($v['sex']))
                        ->setCellValue('D' . ($k + 2), getAge($v['birthday']))
                        ->setCellValue('E' . ($k + 2), $v['nation'])
                        ->setCellValue('F' . ($k + 2), $qu)
                        ->setCellValue('G' . ($k + 2), $zhen)
                        ->setCellValue('H' . ($k + 2), $v['home_address'])
                        ->setCellValue('I' . ($k + 2), getHealth($v['health'], $v['id']))
                        ->setCellValue('J' . ($k + 2), $tels)
                        ->setCellValue('K' . ($k + 2), $mdfx)
                        ->setCellValue('L' . ($k + 2), $jgfx)
                        ->setCellValue('M' . ($k + 2), $bffx)
                        ->setCellValue('N' . ($k + 2), getStage($v['case_status']));
//                        ->setCellValue( 'G'.($k+2), $v['enroll_company'])
//                        ->setCellValue( 'H'.($k+2), $v['enroll_payment'])
//                        ->setCellValue( 'I'.($k+2), $v['enroll_pay_status'])
//                        ->setCellValue( 'J'.($k+2), $v['beizhu']);
                }

                // 生成2003excel格式的xls文件
                ob_end_clean();//清除缓冲区,避免乱码
                header('Content-Type: application/vnd.ms-excel;charset=UTF-8');
                header('Content-Disposition: attachment;filename="enroll_' . time() . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
            } else {
                exit('无数据!');
                $this->error('无数据!');
            }
        }
    }

    //待处理
    public function daichu()
    {
        $this->meta_title = '待处理案件';
        $uid = M('auth_group_access')->where(array('uid' => UID))->getField('group_id');
        if (UID == 1) {
            $where = ('finish_suggestion = "" OR visit_status = 1 AND visit_suggestion = ""');
        }
        if ($uid == 1) {
            $where = ('case_status = "diaodu" and turn_related = 1');
        }
        if ($uid == 2) {
            $where = ('case_status = "chushen"');
        }
        if ($uid == 3) {
            $where = ('case_status = "diaodu" and turn_related = 3 or case_status = "weihuifang"');
        }
        if ($uid == 4) {
            $where = ('case_status = "caiji" or case_status = "shenpi" or case_status = "chuzhi"');
        }

        $count = M('case')->where($where)->order('fill_in_time desc')->count();

        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $page = new Page($count, $pagesize);
        $this->Page = $page->show();

        $this->CaseList = M('case')->where($where)->order('fill_in_time desc')->limit($limit)->select();
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
        if (UID == 1) {
            $where = (
            'finish_suggestion = ""
    		OR
    		visit_status = 1
    		AND
    		visit_suggestion = ""
    		'
            );
        }
        if ($uid == 1) {
            $where = (
            'case_status = "diaodu"
    			and
    			turn_related = 1
    			'
            );
        }
        if ($uid == 2) {
            $where = (
            'case_status = "chushen"'
            );
        }
        if ($uid == 3) {
            $where = (
            'case_status = "diaodu"
    			and
    			turn_related = 3
    			or
    			case_status = "weihuifang"
    			'
            );
        }
        if ($uid == 4) {
            $where = (
            'case_status = "caiji"
    			or
    			case_status = "shenpi"
    			or
    			case_status = "chuzhi"
    			'
            );
        }

        $count = M('case')->where($where)->order('fill_in_time desc')->count();

        import('Think', 'ThinkPHP/Library/Think/Page');
        $pagesize = 25;
        $p = I('p') ? I('p') : 1;
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $page = new Page($count, $pagesize);
        $this->Page = $page->show();

        $this->CaseList = M('case')->where($where)->order('fill_in_time desc')->limit($limit)->select();
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
        $where = (
        'case_status = "jiean"
    			 or 
    			 case_status = "huifang"
    			 '
        );

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
                $uploadPath = 'case/user/';
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
            if ($_POST['xId']) {
                $case = $db->where(array('id' => $_POST['xId']))->save($data);
            } else {
                $case = $db->add($data);
            }
            if ($case) {
                echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
            } else {
                echo "<script>alert('操作失败');window.location.href='index.php?s=/Index/index.html';</script>";
            }
        } else {
            $this->Lage();
            $case = $this->Handle(M('case')->where(array('id' => $_GET['id']))->find());
//            $this->Clist = $case;
            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            echo json_encode($member);
            $this->assign('member', $member);
//            $this->display();
        }
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
            $data['trial_time'] = date("Y-m-d", time());
            if ($data['trial_status'] == 1) {
                $saveCase = $case->where(array('id' => $data['id']))->save($data);
                if ($saveCase) {
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                    // $this->success('操作成功',U('index',array('id'=>$id,'act'=>$act)));
                } else {
                    echo "<script>alert('操作失败');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            } else {
                $case->where(array('id' => $data['id']))->setField('case_status', 'bohuiC');
                echo "<script>alert('案件被驳回');window.location.href='index.php?s=/Index/index.html';</script>";
            }
        } else {
            $this->Lage();
            $case = $this->Handle(M('case')->where(array('id' => $_GET['id']))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }
            $this->assign('shi', $shi);
            $this->act = $_GET['act'];
            $this->id = $_GET['id'];
            $urse = M('ucenter_member');
            $this->member = $urse->where(array('id' => UID))->getField('username');
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
            $data['last_instance_time'] = date("Y-m-d", time());
            if ($data['last_instance_status'] == 1) {
                $saveCase = $case->where(array('id' => $data['id']))->save($data);
                if ($saveCase) {
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('操作失败');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            } else if ($data['last_instance_status'] == 2) {
                $case->where(array('id' => $data['id']))->setField('case_status', 'bohuiCs');
                echo "<script>alert('案件被驳回');window.location.href='index.php?s=/Index/index.html';</script>";
            }
        } else {
            $this->Lage();
            $case = $this->Handle(M('case')->where(array('id' => $_GET['id']))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }
            $this->assign('shi', $shi);
            $this->act = $_GET['act'];
            $this->id = $_GET['id'];
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
            $data['dispatch_time'] = date("Y-m-d", time());
            $saveCase = $case->where(array('id' => $_POST['id']))->save($data);
            if ($saveCase) {
                echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
            } else {
                echo "<script>alert('信息未填写完整');window.location.href='index.php?s=/Index/index.html';</script>";
            }
        } else {
            $this->Lage();
            $case = $this->Handle(M('case')->where(array('id' => $_GET['id']))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }
            $this->assign('shi', $shi);

            $this->act = $_GET['act'];
            $this->id = $_GET['id'];
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
            $data['deal_with_time'] = empty($_POST['deal_with_time']) ? date("Y-m-d", time()) : $_POST['deal_with_time'];
            if ($data['management_status'] == 1) {
                $saveCase = $case->where(array('id' => $_POST['id']))->save($data);
                if ($saveCase) {
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('信息未填写完整');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            } else if ($data['management_status'] == 2) {
                $case->where(array('id' => $_POST['id']))->setField('case_status', 'bohuiCz');
                echo "<script>alert('案件被驳回');window.location.href='index.php?s=/Index/index.html';</script>";
            }
        } else {
            $this->Lage();
            $case = $this->Handle(M('case')->where(array('id' => $_GET['id']))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }
            $this->assign('shi', $shi);

            $this->act = $_GET['act'];
            $this->id = $_GET['id'];
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
        $management_record = M('case')->where(array('id' => $id))->getField('management_record');
        if (is_array(unserialize($management_record))) {
            $arr = unserialize($management_record);
            array_push($arr, $_POST['str']);
            $data['management_record'] = serialize($arr);
        } else if (!empty($management_record)) {
            $arr[] = unserialize($management_record);
            array_push($arr, $_POST['str']);
            $data['management_record'] = serialize($arr);
        } else {
            $data['management_record'] = serialize($_POST['str']);
        }
        $saveData = M('case')->where(array('id' => $id))->save($data);
        if ($saveData) {
            echo '1';
        } else {
            echo '2';
        }
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
            $data['finish_time'] = empty($_POST['finish_time']) ? date("Y-m-d", time()) : $_POST['finish_time'];
            if ($data['visit_status'] == 1) {
                if ($data['visit_form'] == 1) {
                    $data['visit_way'] = $_POST['visit_form1'];
                } else if ($data['visit_form'] == 2) {
                    $way[] = $_POST['visit_form2_1'];
                    $way[] = $_POST['visit_form2_2'];
                    $data['visit_way'] = serialize($way);
                }
                $data['case_status'] = 'weihuifang';
                $saveCase = $case->where(array('id' => $_POST['id']))->save($data);
                if ($saveCase) {
                    echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
                } else {
                    echo "<script>alert('信息未填写完整');window.location.href='index.php?s=/Index/index.html';</script>";
                }
            } else if ($data['visit_status'] == null) {
                $data['case_status'] = 'jiean';
                $saveCase = $case->where(array('id' => $_POST['id']))->save($data);
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
            $case = $this->Handle(M('case')->where(array('id' => $_GET['id']))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }
            $this->assign('shi', $shi);

            $this->id = $_GET['id'];
            $this->act = $_GET['act'];
            $this->finishList = M('case')->where(array('id' => $_GET['id']))->find();
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
            $way = M('case')->where(array('id' => $_POST['id']))->getField('visit_form');
            if ($way == 1) {
                $data['visit_suggestion'] = $_POST['visit_suggestion'];
            } else if ($way == 2) {
                $data['visit_suggestion'] = serialize($_POST['visit_suggestion']);
            }
            $data['visit_time'] = empty($_POST['visit_time']) ? date("Y-m-d H:i:s", time()) : $_POST['visit_time'];
            $data['case_status'] = 'huifang';
            $saveCase = $case->where(array('id' => $_POST['id']))->save($data);
            if ($saveCase) {
                echo "<script>alert('操作成功');window.location.href='index.php?s=/Index/index.html';</script>";
            } else {
                echo "<script>alert('信息未填写完整');window.location.href='index.php?s=/Index/index.html';</script>";
            }
        } else {
            $this->Lage();
            $case = $this->Handle(M('case')->where(array('id' => $_GET['id']))->find());
            $this->Clist = $case;
            if (empty($case['household_pro_code'])) {
                $shi = M('area')->where(array('parent_id' => 14))->select();
            } else {
                $shi = M('area')->where(array('parent_id' => $case['household_pro_code']['k']))->select();
            }
            $this->assign('shi', $shi);

            $urse = M('ucenter_member');
            $member = $urse->where(array('id' => UID))->getField('username');
            $this->assign('member', $member);
            $this->id = $_GET['id'];
            $this->act = $_GET['act'];
            $this->huifang = M('case')->where(array('id' => $this->id))->getField('visit_form');
            if ($this->huifang == 2) {
                $cishu1 = M('case')->where(array('id' => $this->id))->getField('visit_way');
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

    //搜索
//    public function search(){
//        $this->CaseList = M('case')->where(array('name'=>array('like',"%".$_POST['search']."%")))->select();
//        $this->shequ = M('area_top')->where(array('type_id'=>5))->select();
//        $this->display('Case/'.$_POST['action']);
//    }
}