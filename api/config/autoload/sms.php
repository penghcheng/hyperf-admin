<?php
/**
 * 配置文件
 *
 * @link     http://www.swoole.red
 * @contact  1712715552@qq.com
 */

return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \HyperfLibraries\Sms\Strategy\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            //'qcloud',
            'aliyun'
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        //'qcloud' => [
        //    'sdk_app_id' => '', // SDK APP ID
        //    'app_key' => '', // APP KEY
        //    'sign_name' => '', // 短信签名，如果使用默认签名，该字段可缺省（对应官方文档中的sign）
        //],
        'aliyun' => [
            'access_key_id' => '',
            'access_key_secret' => '',
            'sign_name' => '',
        ],
    ],
];
