## ThinkPHP-PanGu - 盘古ThinkPHP开源索引!
面向 ThinkPHP 的开源工程索引：组件、框架、服务与发布工具，按能力组织，持续整理。
仓库地址：**<https://github.com/indiboy-create/ThinkPHP-PanGu/>**
在线站点：**<https://indiboy-create.github.io/ThinkPHP-PanGu/>**

# think-easywechat

基于 [EasyWeChat](https://github.com/w7corp/easywechat) 6.x 的 ThinkPHP 8 集成包，提供服务注册、配置发布、多账号管理、Facade 以及缓存与日志自动注入。

## 环境要求

- PHP >= 8.0.2
- ThinkPHP >= 8.0
- EasyWeChat >= 6.0

## 安装

```bash
composer require youvision/think-easywechat
```

安装完成后，执行服务发现命令，将本包的服务类注册到 `vendor/services.php`：

```bash
php think service:discover
```

发布配置文件：

```bash
php think vendor:publish
```

命令会生成 `config/easywechat.php`。如果文件已存在，需要覆盖时追加 `--force`：

```bash
php think vendor:publish --force
```

## 配置

配置文件中的每一项都带有中文注释，下面是最常用的部分：

```php
return [
    'default' => 'default',

    'accounts' => [
        'default' => [
            'official_account' => [
                'app_id' => env('WECHAT_OFFICIAL_APP_ID', ''),
                'secret' => env('WECHAT_OFFICIAL_SECRET', ''),
                'token' => env('WECHAT_OFFICIAL_TOKEN', ''),
                'aes_key' => env('WECHAT_OFFICIAL_AES_KEY', ''),
            ],
            'mini_app' => [
                'app_id' => env('WECHAT_MINI_APP_ID', ''),
                'secret' => env('WECHAT_MINI_SECRET', ''),
            ],
            // pay、open_platform、work、open_work 同样配置在账号下
        ],
    ],

    'cache' => [
        'enabled' => true,
        'store' => null,
        'namespace' => 'easywechat',
        'lifetime' => 1500,
    ],

    'log' => [
        'enabled' => true,
        'channel' => null,
    ],
];
```

对应的 `.env` 示例：

```ini
WECHAT_OFFICIAL_APP_ID = wx1234567890
WECHAT_OFFICIAL_SECRET = 0123456789abcdef
WECHAT_OFFICIAL_TOKEN = easywechat
WECHAT_OFFICIAL_AES_KEY = abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG

WECHAT_MINI_APP_ID = wx0987654321
WECHAT_MINI_SECRET = fedcba9876543210
```

`cache.store` 为 `null` 时使用 `config/cache.php` 中的默认缓存驱动；填写驱动名称时使用对应的缓存通道。缓存与日志默认都会注入到 EasyWeChat，用于保存 `access_token`、`jsapi_ticket` 以及记录接口调用。

## 快速开始

### 在控制器中使用

```php
<?php

namespace app\controller;

use Youvision\ThinkEasyWeChat\Facade as WeChat;

class User
{
    public function index()
    {
        $app = WeChat::officialAccount();

        $response = $app->getClient()->get('cgi-bin/user/get');

        return json($response->toArray());
    }
}
```

### 通过容器获取

```php
$wechat = app('easywechat');
$officialAccount = $wechat->officialAccount();

// 或者直接注入
public function __construct(protected \Youvision\ThinkEasyWeChat\WeChat $wechat)
{
}
```

### 公众号消息回调

```php
use Youvision\ThinkEasyWeChat\Facade as WeChat;

public function notify()
{
    $server = WeChat::officialAccount()->getServer();

    $server->with(function ($message) {
        return 'success';
    });

    $response = $server->serve();

    $headers = [];
    foreach ($response->getHeaders() as $name => $values) {
        $headers[$name] = implode(', ', $values);
    }

    return response($response->getBody()->getContents(), $response->getStatusCode(), $headers);
}
```

### 小程序

```php
use Youvision\ThinkEasyWeChat\Facade as WeChat;

$session = WeChat::miniApp()->getUtils()->codeToSession($code);

$phone = WeChat::miniApp()->getUtils()->getPhoneNumber($phoneCode);
```

### 微信支付

```php
use Youvision\ThinkEasyWeChat\Facade as WeChat;

$response = WeChat::pay()->getClient()->post('v3/pay/transactions/jsapi', [
    'json' => [
        'appid' => 'wx1234567890',
        'mchid' => '1900000001',
        'description' => '测试商品',
        'out_trade_no' => date('YmdHis') . mt_rand(1000, 9999),
        'notify_url' => 'https://example.com/pay/notify',
        'amount' => [
            'total' => 1,
            'currency' => 'CNY',
        ],
        'payer' => [
            'openid' => $openid,
        ],
    ],
]);

$result = $response->toArray();
```

### 开放平台与企业微信

```php
use Youvision\ThinkEasyWeChat\Facade as WeChat;

$openPlatform = WeChat::openPlatform();
$work = WeChat::work();
$openWork = WeChat::openWork();
```

各应用的可用方法请参考 [EasyWeChat 文档](https://easywechat.com)。

## 多账号

在 `config/easywechat.php` 的 `accounts` 下新增账号，即可按账号名称获取应用：

```php
'accounts' => [
    'default' => [
        'official_account' => [
            'app_id' => env('WECHAT_OFFICIAL_APP_ID', ''),
            'secret' => env('WECHAT_OFFICIAL_SECRET', ''),
        ],
    ],
    'account2' => [
        'official_account' => [
            'app_id' => env('WECHAT_ACCOUNT2_APP_ID', ''),
            'secret' => env('WECHAT_ACCOUNT2_SECRET', ''),
        ],
    ],
],
```

```php
$app = WeChat::officialAccount('account2');
```

同一个账号下的应用实例在单次请求内复用，重复调用不会重复创建。

## 应用类型对照

| 方法 | 配置键 | EasyWeChat 类 |
| --- | --- | --- |
| `officialAccount()` | `official_account` | `EasyWeChat\OfficialAccount\Application` |
| `miniApp()` | `mini_app` | `EasyWeChat\MiniApp\Application` |
| `openPlatform()` | `open_platform` | `EasyWeChat\OpenPlatform\Application` |
| `pay()` | `pay` | `EasyWeChat\Pay\Application` |
| `work()` | `work` | `EasyWeChat\Work\Application` |
| `openWork()` | `open_work` | `EasyWeChat\OpenWork\Application` |

也可以通过 `WeChat::app('official_account', 'account2')` 按类型和账号名称获取。

## 目录结构

```
config/easywechat.php   配置文件
src/Facade.php          Facade
src/Service.php         服务注册与依赖注入
src/WeChat.php          应用管理器
tests/                  PHPUnit 测试
```

## 测试

```bash
composer install
vendor/bin/phpunit
```

## License

[MIT](LICENSE)