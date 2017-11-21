<?php

namespace Mobile\Controller;

use Think\Controller;
use User\Api\UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class PublicController extends Controller
{
    /* 登录页面 */
    public function login()
    {
        if (IS_POST) { //登录验证
            $username = $_POST['username'];
            $password = $_POST['password'];
            /* 调用UC登录接口登录 */
            $user = new UserApi;
            $uid = $user->login($username, $password);
            if (0 < $uid) { //UC登录成功
                /* 登录用户 */
                $Member = D('Member');
                if ($Member->login($uid)) { //登录用户
                    exit(json_encode(array('erron' => 1, 'error' => '登录成功')));
                } else {
                    exit(json_encode(array('erron' => 0, 'error' => $Member->getError())));
                }
            } else { //登录失败
                switch ($uid) {
                    case -1:
                        $error = '用户不存在或被禁用！';
                        break; //系统级别禁用
                    case -2:
                        $error = '密码错误！';
                        break;
                    default:
                        $error = '未知错误！';
                        break; // 0-接口参数错误（调试阶段使用）
                }
                exit(json_encode(array('erron' => 0, 'error' => $error)));
            }
        }
        //显示登录表单
//        $this->display();
    }

    /* 退出登录 */
    public function logout()
    {
        if (is_login()) {
            D('Member')->logout();
            session('[destroy]');
            exit(json_encode(array('erron' => 1, 'error' => '退出成功')));
        } else {
            exit(json_encode(array('erron' => 1, 'error' => '没有登录')));
        }
    }

    public function verify()
    {
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

}
