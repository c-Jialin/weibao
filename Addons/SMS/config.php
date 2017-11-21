<?php
return array(
    //Uid=本站用户名&Key=接口安全秘钥
    'Uid' => array(//配置在表单中的键名 ,这个会是config[random]
        'title' => 'Uid:(本站用户名)',//表单的文字
        'type' => 'text',//表单的类型：text、textarea、checkbox、radio、select等
        'value' => null,//表单的默认值
    ),
    'Key' => array(
        'title' => 'Key:(接口安全秘钥)，跳转到 <a target="_blank" href="http://sms.webchinese.com.cn">中国网建</a> 获取接口安全密钥',
        'type' => 'text',
        'value' => null,
    ),
);
?>