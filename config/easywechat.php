<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 默认账号
    |--------------------------------------------------------------------------
    | 调用 WeChat::officialAccount() 等方法且不指定账号名称时使用的账号。
    | 这里填写的值必须存在于下方 accounts 数组中。
    */
    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | 账号列表
    |--------------------------------------------------------------------------
    | 每个账号下可以分别配置公众号、小程序、开放平台、微信支付、企业微信等，
    | 各应用类型之间相互独立。需要多账号时复制一份并修改账号名称即可，例如
    | 新增 'account2' => [...]，然后通过 WeChat::officialAccount('account2') 获取。
    */
    'accounts' => [
        'default' => [
            /*
            | 公众号配置
            | app_id、secret 为必填项；token、aes_key 仅在接收消息回调或使用
            | 加密模式时需要填写。
            */
            'official_account' => [
                'app_id' => env('WECHAT_OFFICIAL_APP_ID', ''),
                'secret' => env('WECHAT_OFFICIAL_SECRET', ''),
                'token' => env('WECHAT_OFFICIAL_TOKEN', ''),
                'aes_key' => env('WECHAT_OFFICIAL_AES_KEY', ''),
                'http' => [
                    'timeout' => 10.0,
                ],
            ],

            /*
            | 小程序配置
            | app_id、secret 为必填项；token、aes_key 仅在消息推送回调时需要填写。
            */
            'mini_app' => [
                'app_id' => env('WECHAT_MINI_APP_ID', ''),
                'secret' => env('WECHAT_MINI_SECRET', ''),
                'token' => env('WECHAT_MINI_TOKEN', ''),
                'aes_key' => env('WECHAT_MINI_AES_KEY', ''),
                'http' => [
                    'timeout' => 10.0,
                ],
            ],

            /*
            | 开放平台配置
            | app_id、secret 为必填项；token、aes_key 用于接收开放平台推送。
            */
            'open_platform' => [
                'app_id' => env('WECHAT_OPEN_PLATFORM_APP_ID', ''),
                'secret' => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
                'token' => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
                'aes_key' => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),
                'http' => [
                    'timeout' => 10.0,
                ],
            ],

            /*
            | 微信支付配置
            | mch_id、private_key、certificate、secret_key 为必填项。
            | private_key 与 certificate 既可以是文件路径，也可以是文件内容；
            | platform_certs 用于配置平台证书（证书序列号 => 证书内容或路径）。
            | v2_secret_key 仅在使用 API v2 时填写。
            */
            'pay' => [
                'mch_id' => env('WECHAT_PAY_MCH_ID', ''),
                'secret_key' => env('WECHAT_PAY_SECRET_KEY', ''),
                'private_key' => env('WECHAT_PAY_PRIVATE_KEY', ''),
                'certificate' => env('WECHAT_PAY_CERTIFICATE', ''),
                'platform_certs' => [],
                'v2_secret_key' => env('WECHAT_PAY_V2_SECRET_KEY', ''),
                'http' => [
                    'timeout' => 10.0,
                ],
            ],

            /*
            | 企业微信配置
            | corp_id、secret 为必填项；token、aes_key 用于接收回调消息。
            */
            'work' => [
                'corp_id' => env('WECHAT_WORK_CORP_ID', ''),
                'secret' => env('WECHAT_WORK_SECRET', ''),
                'token' => env('WECHAT_WORK_TOKEN', ''),
                'aes_key' => env('WECHAT_WORK_AES_KEY', ''),
                'http' => [
                    'timeout' => 10.0,
                ],
            ],

            /*
            | 企业微信服务商配置
            | corp_id、provider_secret、suite_id、suite_secret 为必填项；
            | token、aes_key 用于接收服务商回调消息。
            */
            'open_work' => [
                'corp_id' => env('WECHAT_OPEN_WORK_CORP_ID', ''),
                'provider_secret' => env('WECHAT_OPEN_WORK_PROVIDER_SECRET', ''),
                'suite_id' => env('WECHAT_OPEN_WORK_SUITE_ID', ''),
                'suite_secret' => env('WECHAT_OPEN_WORK_SUITE_SECRET', ''),
                'token' => env('WECHAT_OPEN_WORK_TOKEN', ''),
                'aes_key' => env('WECHAT_OPEN_WORK_AES_KEY', ''),
                'http' => [
                    'timeout' => 10.0,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 缓存配置
    |--------------------------------------------------------------------------
    | enabled: 是否把框架缓存注入 EasyWeChat，用于缓存 access_token、jsapi_ticket 等
    | store:   使用的缓存驱动名称，null 表示使用 config/cache.php 中的默认驱动
    | namespace: EasyWeChat 内部缓存命名空间，仅使用内置文件缓存时生效
    | lifetime: access_token 等数据的缓存有效期（秒）
    */
    'cache' => [
        'enabled' => true,
        'store' => null,
        'namespace' => 'easywechat',
        'lifetime' => 1500,
    ],

    /*
    |--------------------------------------------------------------------------
    | 日志配置
    |--------------------------------------------------------------------------
    | enabled: 是否把框架日志注入 EasyWeChat，用于记录接口请求与异常
    | channel: 使用的日志通道名称，null 表示使用 config/log.php 中的默认通道
    */
    'log' => [
        'enabled' => true,
        'channel' => null,
    ],
];