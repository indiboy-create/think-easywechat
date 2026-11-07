<?php

declare(strict_types=1);

namespace Youvision\ThinkEasyWeChat\Tests;

use EasyWeChat\MiniApp\Application as MiniAppApplication;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Youvision\ThinkEasyWeChat\WeChat;

class WeChatTest extends TestCase
{
    public function testDefaultAccountIsUsed(): void
    {
        $wechat = new WeChat($this->config());

        $app = $wechat->officialAccount();

        self::assertInstanceOf(OfficialAccountApplication::class, $app);
        self::assertSame('wx-default', $app->getAccount()->getAppId());
        self::assertSame('default', $wechat->getDefaultAccount());
    }

    public function testNamedAccountIsUsed(): void
    {
        $wechat = new WeChat($this->config());

        $app = $wechat->officialAccount('second');

        self::assertSame('wx-second', $app->getAccount()->getAppId());
    }

    public function testMiniAppIsCreated(): void
    {
        $wechat = new WeChat($this->config());

        self::assertInstanceOf(MiniAppApplication::class, $wechat->miniApp());
        self::assertSame('wx-mini', $wechat->miniApp()->getAccount()->getAppId());
    }

    public function testApplicationInstanceIsReused(): void
    {
        $wechat = new WeChat($this->config());

        self::assertSame($wechat->officialAccount(), $wechat->officialAccount());
        self::assertSame($wechat->app(WeChat::OFFICIAL_ACCOUNT), $wechat->officialAccount());
    }

    public function testDifferentAccountsUseDifferentInstances(): void
    {
        $wechat = new WeChat($this->config());

        self::assertNotSame($wechat->officialAccount(), $wechat->officialAccount('second'));
    }

    public function testLoggerIsInjected(): void
    {
        $logger = new NullLogger();
        $wechat = new WeChat($this->config(), $logger);

        $property = new \ReflectionProperty($wechat->officialAccount(), 'logger');
        $property->setAccessible(true);

        self::assertSame($logger, $property->getValue($wechat->officialAccount()));
        self::assertSame($logger, $wechat->getLogger());
    }

    public function testCacheIsInjected(): void
    {
        $cache = new ArrayCache();
        $wechat = new WeChat($this->config(), null, $cache);

        $app = $wechat->officialAccount();

        self::assertSame($cache, $app->getCache());
        self::assertSame(7200, $app->getCacheLifetime());
    }

    public function testUnknownApplicationTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported WeChat application type: unknown.');

        (new WeChat($this->config()))->app('unknown');
    }

    public function testMissingApplicationConfigurationThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('WeChat application "pay" of account "default" is not configured.');

        (new WeChat($this->config()))->pay();
    }

    public function testMissingAccountConfigurationThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('WeChat application "official_account" of account "missing" is not configured.');

        (new WeChat($this->config()))->officialAccount('missing');
    }

    private function config(): array
    {
        return [
            'default' => 'default',
            'accounts' => [
                'default' => [
                    'official_account' => [
                        'app_id' => 'wx-default',
                        'secret' => 'secret-default',
                        'token' => 'token',
                        'aes_key' => str_repeat('a', 43),
                    ],
                    'mini_app' => [
                        'app_id' => 'wx-mini',
                        'secret' => 'secret-mini',
                    ],
                ],
                'second' => [
                    'official_account' => [
                        'app_id' => 'wx-second',
                        'secret' => 'secret-second',
                    ],
                ],
            ],
            'cache' => [
                'lifetime' => 7200,
            ],
        ];
    }
}