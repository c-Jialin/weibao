<?php

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

/* 解析列表定义规则*/

function get_list_field($data, $grid, $model)
{

    // 获取当前字段数据
    foreach ($grid['field'] as $field) {
        $array = explode('|', $field);
        $temp = $data[$array[0]];
        // 函数支持
        if (isset($array[1])) {
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]] = $temp;
    }
    if (!empty($grid['format'])) {
        $value = preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use ($data2) {
            return $data2[$match[1]];
        }, $grid['format']);
    } else {
        $value = implode(' ', $data2);
    }

    // 链接支持
    if (!empty($grid['href'])) {
        $links = explode(',', $grid['href']);
        foreach ($links as $link) {
            $array = explode('|', $link);
            $href = $array[0];
            if (preg_match('/^\[([a-z_]+)\]$/', $href, $matches)) {
                $val[] = $data2[$matches[1]];
            } else {
                $show = isset($array[1]) ? $array[1] : $value;
                // 替换系统特殊字符串
                $href = str_replace(
                    array('[DELETE]', '[EDIT]', '[MODEL]'),
                    array('del?ids=[id]&model=[MODEL]', 'edit?id=[id]&model=[MODEL]', $model['id']),
                    $href);

                // 替换数据变量
                $href = preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use ($data) {
                    return $data[$match[1]];
                }, $href);

                $val[] = '<a href="' . U($href) . '">' . $show . '</a>';
            }
        }
        $value = implode(' ', $val);
    }
    return $value;
}

// 获取模型名称
function get_model_by_id($id)
{
    return $model = M('Model')->getFieldById($id, 'title');
}

// 获取属性类型信息
function get_attribute_type($type = '')
{
    // TODO 可以加入系统配置
    static $_type = array(
        'num' => array('数字', 'int(10) UNSIGNED NOT NULL'),
        'string' => array('字符串', 'varchar(255) NOT NULL'),
        'textarea' => array('文本框', 'text NOT NULL'),
        'datetime' => array('时间', 'int(10) NOT NULL'),
        'bool' => array('布尔', 'tinyint(2) NOT NULL'),
        'select' => array('枚举', 'char(50) NOT NULL'),
        'radio' => array('单选', 'char(10) NOT NULL'),
        'checkbox' => array('多选', 'varchar(100) NOT NULL'),
        'editor' => array('编辑器', 'text NOT NULL'),
        'picture' => array('上传图片', 'int(10) UNSIGNED NOT NULL'),
        'file' => array('上传附件', 'int(10) UNSIGNED NOT NULL'),
    );
    return $type ? $_type[$type][0] : $_type;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 */
function get_status_title($status = null)
{
    if (!isset($status)) {
        return false;
    }
    switch ($status) {
        case -1 :
            return '已删除';
            break;
        case 0  :
            return '禁用';
            break;
        case 1  :
            return '正常';
            break;
        case 2  :
            return '待审核';
            break;
        default :
            return false;
            break;
    }
}

// 获取数据的状态操作
function show_status_op($status)
{
    switch ($status) {
        case 0  :
            return '启用';
            break;
        case 1  :
            return '禁用';
            break;
        case 2  :
            return '审核';
            break;
        default :
            return false;
            break;
    }
}

/**
 * 获取文档的类型文字
 * @param string $type
 * @return string 状态文字 ，false 未获取到
 */
function get_document_type($type = null)
{
    if (!isset($type)) {
        return false;
    }
    switch ($type) {
        case 1  :
            return '目录';
            break;
        case 2  :
            return '主题';
            break;
        case 3  :
            return '段落';
            break;
        default :
            return false;
            break;
    }
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type = 0)
{
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group = 0)
{
    $list = C('CONFIG_GROUP_LIST');
    return $group ? $list[$group] : '';
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map 映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data, $map = array('status' => array(1 => '正常', -1 => '删除', 0 => '禁用', 2 => '未审核', 3 => '草稿')))
{
    if ($data === false || $data === null) {
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row) {
        foreach ($map as $col => $pair) {
            if (isset($row[$col]) && isset($pair[$row[$col]])) {
                $data[$key][$col . '_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 动态扩展左侧菜单,base.html里用到
 */
function extra_menu($extra_menu, &$base_menu)
{
    foreach ($extra_menu as $key => $group) {
        if (isset($base_menu['child'][$key])) {
            $base_menu['child'][$key] = array_merge($base_menu['child'][$key], $group);
        } else {
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 获取参数的所有父级分类
 * @param int $cid 分类id
 * @return array 参数分类和父类的信息集合
 */
function get_parent_category($cid)
{
    if (empty($cid)) {
        return false;
    }
    $cates = M('Category')->where(array('status' => 1))->field('id,title,pid')->order('sort')->select();
    $child = get_category($cid);    //获取参数分类的信息
    $pid = $child['pid'];
    $temp = array();
    $res[] = $child;
    while (true) {
        foreach ($cates as $key => $cate) {
            if ($cate['id'] == $pid) {
                $pid = $cate['pid'];
                array_unshift($res, $cate);    //将父分类插入到数组第一个元素前
            }
        }
        if ($pid == 0) {
            break;
        }
    }
    return $res;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1)
{
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 获取当前分类的文档类型
 * @param int $id
 * @return array 文档类型数组
 */
function get_type_bycate($id = null)
{
    if (empty($id)) {
        return false;
    }
    $type_list = C('DOCUMENT_MODEL_TYPE');
    $model_type = M('Category')->getFieldById($id, 'type');
    $model_type = explode(',', $model_type);
    foreach ($type_list as $key => $value) {
        if (!in_array($key, $model_type)) {
            unset($type_list[$key]);
        }
    }
    return $type_list;
}

/**
 * 获取当前文档的分类
 * @param int $id
 * @return array 文档类型数组
 */
function get_cate($cate_id = null)
{
    if (empty($cate_id)) {
        return false;
    }
    $cate = M('Category')->where('id=' . $cate_id)->getField('title');
    return $cate;
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string)
{
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

// 获取子文档数目
function get_subdocument_count($id = 0)
{
    return M('Document')->where('pid=' . $id)->count();
}


// 分析枚举类型字段值 格式 a:名称1,b:名称2
// 暂时和 parse_config_attr功能相同
// 但请不要互相使用，后期会调整
function parse_field_attr($string)
{
    if (0 === strpos($string, ':')) {
        // 采用函数定义
        return eval(substr($string, 1) . ';');
    }
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 */
function get_action($id = null, $field = null)
{
    if (empty($id) && !is_numeric($id)) {
        return false;
    }
    $list = S('action_list');
    if (empty($list[$id])) {
        $map = array('status' => array('gt', -1), 'id' => $id);
        $list[$id] = M('Action')->where($map)->field(true)->find();
    }
    return empty($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 根据条件字段获取数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 */
function get_document_field($value = null, $condition = 'id', $field = null)
{
    if (empty($value)) {
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M('Model')->where($map);
    if (empty($field)) {
        $info = $info->field(true)->find();
    } else {
        $info = $info->getField($field);
    }
    return $info;
}

/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 */
function get_action_type($type, $all = false)
{
    $list = array(
        1 => '系统',
        2 => '用户',
    );
    if ($all) {
        return $list;
    }
    return $list[$type];
}

//显示性别
function getSex($sex)
{
    if ($sex == 2) {
        return "男";
    } else if ($sex == 1) {
        return "女";
    }
}

//在字符串指定位置插入"-"
function str_insert($str, $i, $substr)
{
    $startstr = '';
    for ($j = 0; $j < $i; $j++) {
        $startstr .= $str[$j];
    }
    $laststr = '';
    for ($j = $i; $j < strlen($str); $j++) {
        $laststr .= $str[$j];
    }
    $str = ($startstr . $substr . $laststr);
    return $str;
}

//显示年龄
function getAge($age)
{
    // a:2:{i:0;s:4:"2000";i:1;s:1:"8";}
    $age = unserialize($age);
    $year = explode("-", date("Y-m", time()));
    $res = $year[1] - $age[1];
    if ($res >= 0) {
        $nl = $year[0] - $age[0];
    } else {
        $nl = $year[0] - $age[0] - 1;
    }
    if ($nl > 100) {
        $nl = rand(0, 17);
    }
    return $nl;
}

//显示成长困境及风险等级
function getGrowth()
{

}

//获取省市区名称
function getPraName($id)
{
    return $PraName = M('area')->where(array('region_id' => $id))->getField('region_name');
}

//显示健康状况
function getHealth($id, $v)
{
    $res = '';
    if ($id == 1) {
        $res = '健康';
    } else if ($id == 2) {
        $res = '肢体残疾';
    } else if ($id == 3) {
        $res = '智力缺陷';
    } else if ($id == 4) {
        $res = '精神疾病';
    } else if ($id == 5) {
        $res = '传染病';
    } else if ($id == 6) {
        $res = '盲人';
    } else if ($id == 7) {
        $res = '聋哑';
    } else if ($id == 8) {
        $res = '吸毒史';
    } else if ($id == 9) {
        $str = M('case')->where(array('id' => $v))->getField('health_other');
        $res = $str;
    }
    return $res;
}

//当前阶段
function getStage($str, $isExcel = false)
{
    $res = '';
    $excel = '';//若是excel表格使用 则返回此
    switch ($str) {
        case 'bohuiC':
            $excel = '采集';
            $res = '<font style="color:red">采集</font>';
            break;

        case 'bohuiCs':
        case 'caiji':
            $excel = '初审';
            $res = '<font style="color:red">初审</font>';
            break;

        case 'chushen':
            $excel = '审批';
            $res = '<font style="color:red">审批</font>';
            break;

        case 'bohuiCz':
        case 'shenpi':
            $excel = '调度';
            $res = '<font style="color:red">调度</font>';
            break;

        case 'diaodu':
            $excel = '处置';
            $res = '<font style="color:red">处置</font>';
            break;

        case 'chuzhi':
            $excel = '结案';
            $res = '<font style="color:red">结案</font>';
            break;

        case 'weihuifang':
            $excel = '回访';
            $res = '<font style="color:red">回访</font>';
            break;

        case 'jiean':
        case 'huifang':
            $excel = '完成处理';
            $res = '<font style="color:red">完成处理</font>';
            break;
    }
    return $isExcel ? $excel : $res;
}


//初级评估风险
function getFengxian($str)
{
    $arr1 = array(5, 11, 17, 23, 29, 35, 41);
    $arr2 = array(4, 10, 16, 22, 28, 34, 40);
    $arr3 = array(3, 9, 15, 21, 27, 33, 39);
    $arr4 = array(2, 8, 14, 20, 26, 32, 38);
    $arr5 = array(1, 7, 13, 19, 25, 31, 37);
    $arr6 = array(6, 12, 18, 24, 30, 36, 42);
    $res = '';
    if (in_array($str, $arr1)) {
        $res = 5;
    } else if (in_array($str, $arr2)) {
        $res = 4;
    } else if (in_array($str, $arr3)) {
        $res = 3;
    } else if (in_array($str, $arr4)) {
        $res = 2;
    } else if (in_array($str, $arr5)) {
        $res = 1;
    } else if (in_array($str, $arr6)) {
        $res = 0;
    }
    return $res;
}

//初级评估风险类
function getFxian($str)
{
    $res = '';
    $arr1 = array(1, 2, 3, 4, 5);
    $arr2 = array(7, 8, 9, 10, 11);
    $arr3 = array(13, 14, 15, 16, 17);
    $arr4 = array(19, 20, 21, 22, 23);
    $arr5 = array(25, 26, 27, 28, 29);
    $arr6 = array(31, 32, 33, 34, 35);
    $arr7 = array(37, 38, 39, 40, 41);
    if (in_array($str, $arr1)) {
        $res = '一类:';
    } else if (in_array($str, $arr2)) {
        $res = '二类:';
    } else if (in_array($str, $arr3)) {
        $res = '三类:';
    } else if (in_array($str, $arr4)) {
        $res = '四类:';
    } else if (in_array($str, $arr5)) {
        $res = '五类:';
    } else if (in_array($str, $arr6)) {
        $res = '六类:';
    } else if (in_array($str, $arr7)) {
        $res = '七类:';
    }
    return $res;
}

//内心困境
function getNeixin($str, $id)
{
    $res = '';
    $Heart_other = M('case')->where(array('id' => $id))->getField('Heart_other');
    if ($str == 1) {
        $res = '父母关系不好';
    } else if ($str == 2) {
        $res = '缺少亲友陪伴';
    } else if ($str == 3) {
        $res = '朋辈关系不好';
    } else if ($str == 4) {
        $res = '学习成绩不佳';
    } else if ($str == 5) {
        $res = '无人理解自己';
    } else if ($str == 6) {
        $res = '感觉自己不如别人';
    } else if ($str == 7) {
        $res = '1111';
    }
    return $res;
}

//获取社区名称
function getShequ($id)
{
    return $res = M('area_top')->where(array('region_id' => $id))->getField('region_name');
}


/**
 * 重构一个二维数组, 以一维数组中某个value为键值
 * @param array $arr 源数组
 * @param string $key 指定数组中一维数组的某个key
 * return array    $res 重构后的数组
 */
function rebuildArray($arr, $key)
{
    $res = [];
    foreach ($arr as $k => $v) {
        $res[$v[$key]] = $v;
    }
    return $res;
}

/**
 * 案件状态对应的本阶段 英文 和 下一阶段的英文
 * @param string $chn 中文拼音案件状态
 * @param boolen $next 若为true 则获取下一阶段的中文和英文
 * return array
 */
function translate($chn, $next = false)
{
    if ($next)
        $arr = [
            'caiji' => ['ch' => 'chushen', 'en' => 'trial'],
            'bohuiC' => ['ch' => '', 'en' => 'index'],
            'bohuiCs' => ['ch' => 'chushen', 'en' => 'trial'],
            'chushen' => ['ch' => 'zhongshen', 'en' => 'last_instance'],
            'shenpi' => ['ch' => 'diaodu', 'en' => 'dispatch'],
            'diaodu' => ['ch' => 'chuzhi', 'en' => 'deal_with'],
            'chuzhi' => ['ch' => 'jiean', 'en' => 'finish'],
            'bohuiCz' => ['ch' => 'diaodu', 'en' => 'dispatch'],
            'weihuifang' => ['ch' => 'huifang', 'en' => 'visit'],
            'jiean' => ['ch' => '', 'en' => ''],
            'huifang' => ['ch' => '', 'en' => ''],
        ];
    else
        $arr = [
            'caiji' => ['now' => 'add', 'next' => 'trial'],
            'bohuiC' => ['now' => 'add', 'next' => 'trial'],
            'bohuiCs' => ['now' => 'trial', 'next' => 'last_instance'],
            'chushen' => ['now' => 'trial', 'next' => 'last_instance'],
            'shenpi' => ['now' => 'last_instance', 'next' => 'dispatch'],
            'diaodu' => ['now' => 'dispatch', 'next' => 'deal_with'],
            'chuzhi' => ['now' => 'deal_with', 'next' => 'finish'],
            'bohuiCz' => ['now' => 'deal_with', 'next' => 'finish'],
            'weihuifang' => ['now' => 'visit', 'next' => ''],
            'jiean' => ['now' => 'finish', 'next' => ''],
            'huifang' => ['now' => 'visit', 'next' => ''],
        ];
    return $arr[$chn];
}

/**
 * 根据权限获取可查询的案件状态
 * return array $arr
 */
function getStatusFromAuth()
{
    //初始化返回结果
    $arr = ['auth' => [], 'status' => [], 'next' => []];
    $next = ['add' => 1, 'trial' => 2, 'last_instance' => 3, 'dispatch' => 4, 'deal_with' => 5, 'finish' => 6, 'visit' => 7];
    //根据权限获得英文节点
    if (empty(IS_NODE)) {
        $arr['auth'] = ['add', 'trial', 'last_instance', 'dispatch', 'deal_with', 'finish', 'visit'];
    } else {
        $arr['auth'] = explode(',', IS_NODE);
    }
    foreach ($arr['auth'] as $v) {
        $arr['next'][] = $arr['auth'][$next[$v]];
        switch ($v) {
            case 'add':
                $arr['status'][] = 'bohuiC';
                break;
            case 'trial':
                $arr['status'][] = 'bohuiCs';
                $arr['status'][] = 'caiji';
                break;
            case 'last_instance':
                $arr['status'][] = 'chushen';
                break;
            case 'dispatch':
                $arr['status'][] = 'bohuiCz';
                $arr['status'][] = 'shenpi';
                break;
            case 'deal_with':
                $arr['status'][] = 'diaodu';
                break;
            case 'finish':
                $arr['status'][] = 'chuzhi';
                break;
            case 'visit':
                $arr['status'][] = 'weihuifang';
                break;
            default:
                break;
        }
    }
    return $arr;
}

/**
 * 根据案件状态和阶段状态获取流程节点
 * @param string $status 案件状态
 * @param string $stage 阶段状态
 * return string
 */
function getProcess($status, $stage)
{
    $arr = [
        'caiji' => ['complete' => '待初审', 'ing' => '初审中', 'overtime' => '初审超时'],
        'chushen' => ['complete' => '待审批', 'ing' => '审批中', 'overtime' => '审批超时'],
        'bohuiC' => ['complete' => '待重新采集', 'ing' => '重新采集中', 'overtime' => '重新采集超时'],
        'shenpi' => ['complete' => '待调度', 'ing' => '调度中', 'overtime' => '调度超时'],
        'bohuiCs' => ['complete' => '待重新初审', 'ing' => '重新初审中', 'overtime' => '重新初审超时'],
        'diaodu' => ['complete' => '待处置', 'ing' => '处置中', 'overtime' => '处置超时'],
        'chuzhi' => ['complete' => '待结案', 'ing' => '结案中', 'overtime' => '结案超时'],
        'bohuiCz' => ['complete' => '待重新调度', 'ing' => '重新调度中', 'overtime' => '重新调度超时'],
        'weihuifang' => ['complete' => '待回访', 'ing' => '回访中', 'overtime' => '回访超时'],
        'jiean' => ['complete' => '结案完成'],
        'huifang' => ['complete' => '回访完成'],
    ];
    return $arr[$status][$stage];
}

/**
 * 案件状态对应的时间
 * @param string $chn 中文拼音案件状态
 * return array
 */
function statusTime($chn)
{
    $arr = [
        'caiji' => 'add_time',
        'bohuiC' => 'trial_time',
        'chushen' => 'trial_time',
        'bohuiCs' => 'last_instance_time',
        'shenpi' => 'last_instance_time',
        'diaodu' => 'dispatch_time',
        'bohuiCz' => 'deal_with_time',
        'chuzhi' => 'deal_with_time',
        'weihuifang' => 'finish_time',
        'jiean' => 'finish_time',
        'huifang' => 'visit_time',
    ];
    return $arr[$chn];
}