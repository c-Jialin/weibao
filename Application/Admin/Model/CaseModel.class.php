<?php

namespace Admin\Model;

use Think\Model;

/**
 * 分类模型
 */
class CaseModel extends Model
{

    protected $_validate = array(
        array('area_code', 'require', '县/市/区不能为空'),
        array('street_code', 'require', '街/镇不能为空'),
        array('community_code', 'require', '村/社区不能为空'),
        array('name', 'require', '姓名'),
        array('sex', 'require', '性别'),
    );

    /**
     * 更新分类信息
     * @return boolean 更新状态
     */
    public function update($data)
    {
        /* 添加或更新数据 */
        if (empty($data['id'])) {
            $res = $this->add($data);
        } else {
            $res = $this->save($data);
        }
        return $res;
    }
}
