<?php

namespace Addons\SMS;

use Common\Controller\Addon;

/**
 * 中国网建插件
 * @author c-Jialin
 */
class SMSAddon extends Addon
{

    public $info = array(
        'name' => 'SMS',
        'title' => '中国网建',
        'description' => '这是一个临时描述',
        'status' => 1,
        'author' => 'c-Jialin',
        'version' => '0.1'
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    //实现的AdminIndex钩子方法
    public function AdminIndex($mobile, $text)
    {
        $config = $this->getConfig('SMS');
        if (is_array($config)) {
            $uid = $config['Uid'];
            $key = $config['Key'];
            if (empty($uid) || empty($key)) return array('erron' => 0, 'error' => '插件被禁用');
            if (empty($mobile) || empty($text)) return array('erron' => 0, 'error' => '接收号码或信息不能为空');
            $url = 'http://utf8.api.smschinese.cn/?Uid=' . $uid . '&Key=' . $key . '&smsMob=' . $mobile . '&smsText=' . $text;
            if (function_exists('file_get_contents')) {
                $status = file_get_contents($url);
            } else {
                $ch = curl_init();
                $timeout = 5;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $status = curl_exec($ch);
                curl_close($ch);
            }
            if (strpos($status, '-') != false) {
                switch ($status) {
                    case '-1':
                        return array('erron' => 0, 'error' => '插件Uid参数错误');
                        break;
                    case '-2':
                        return array('erron' => 0, 'error' => '插件Key参数错误');
                        break;
                    default:
                        return array('erron' => 0, 'error' => '未知错误(' . $status . ')');
                        break;
                }
            } else {
                switch ($status) {
                    case '1':
                        return array('erron' => 1, 'error' => '短信发送成功');
                        break;
                    case '0':
                        return array('erron' => 0, 'error' => '短信发送失败');
                        break;
                    default:
                        return array('erron' => 1, 'error' => $status . '条短信发送成功');
                        break;
                }
            }
        } else {
            return array('erron' => 0, 'error' => '插件参数配置错误');
        }
    }
}