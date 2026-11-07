<?php

declare(strict_types=1);

namespace Youvision\ThinkEasyWeChat;

use think\Facade as ThinkFacade;

/**
 * @method static \EasyWeChat\OfficialAccount\Application officialAccount(?string $name = null)
 * @method static \EasyWeChat\MiniApp\Application miniApp(?string $name = null)
 * @method static \EasyWeChat\OpenPlatform\Application openPlatform(?string $name = null)
 * @method static \EasyWeChat\Pay\Application pay(?string $name = null)
 * @method static \EasyWeChat\Work\Application work(?string $name = null)
 * @method static \EasyWeChat\OpenWork\Application openWork(?string $name = null)
 * @method static object app(string $type, ?string $name = null)
 * @method static string getDefaultAccount()
 * @method static array getConfig()
 *
 * @see \Youvision\ThinkEasyWeChat\WeChat
 */
class Facade extends ThinkFacade
{
    protected static function getFacadeClass(): string
    {
        return WeChat::class;
    }
}