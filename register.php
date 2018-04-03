<?php
/**
 * 服务注册中心的配置文件。
 *
 * service: 注册中心服务器地址
 * timeout: 连接超时时间，单位 秒，默认 30
 */
return [
    'default' => [
        'autoRegister' => true,
        'service'      => 'http://127.0.0.1:8001',
        'timeout'      => 30,
    ],
];
