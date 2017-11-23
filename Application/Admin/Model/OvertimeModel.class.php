<?php

namespace Admin\Model;

use Think\Model;

/**
 * 超时案件模型
 */
class OvertimeModel extends Model
{
    /**
     * 更新
     * @return boolean 更新状态
     */
    public function update($data)
    {
        /* 添加或更新数据 */
        $where = ['case' => $data['case'], 'status' => $data['status']];
        $arr   = $this->where($where)->count();
        if (empty($arr)) {
            $res = $this->add($data);
        } else {
        	unset($data['node']);
            $res = $this->save($data);
        }
        return $res;
    }
}
