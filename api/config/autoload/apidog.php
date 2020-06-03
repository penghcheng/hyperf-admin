<?php

return [
    // enable false 将不会生成 swagger 文件
    'enable' => env('APP_ENV') !== 'production',
    // swagger 配置的输出文件
    'output_file' => BASE_PATH . '/public/swagger/swagger.json',
    // 忽略的hook, 非必须 用于忽略符合条件的接口, 将不会输出到上定义的文件中
    'ignore' => function($controller, $action) {
        return false;
    },
    // swagger 的基础配置
    'swagger' => [
        'swagger' => '2.0',
        'info' => [
            'description' => '服装定制 swagger',
            'version' => '1.0.0',
            'title' => 'FZDZ API',
        ],
        'host' => '',
        'schemes' => ['http'],
    ],
];
