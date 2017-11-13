<?php

namespace Addons\Aliyun;
use Common\Controller\Addon;

/**
 * 阿里大于插件
 * @author lijialin
 */

    class AliyunAddon extends Addon{

        public $info = array(
            'name'=>'Aliyun',
            'title'=>'阿里大于',
            'description'=>'阿里大于短信服务',
            'status'=>1,
            'author'=>'lijialin',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的app_begin钩子方法
        public function app_begin($param){

        }

    }