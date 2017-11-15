<?php

namespace Admin\Model;

use Think\Model;

/**
 * 导航模型
 */
class CaseManageModel extends Model
{
    protected $_validate = array(
        array('node', 'require', '节点不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('execute_time', 'require', '执行行为的时间不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('warn_time', 'require', '提醒时间不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('status', '1', self::MODEL_BOTH),
    );
}
