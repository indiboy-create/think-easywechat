<?php

declare(strict_types=1);

namespace Youvision\ThinkEasyWeChat;

use EasyWeChat\MiniApp\Application as MiniAppApplication;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use EasyWeChat\OpenPlatform\Application as OpenPlatformApplication;
use EasyWeChat\OpenWork\Application as OpenWorkApplication;
use EasyWeChat\Pay\Application as PayApplication;
use EasyWeChat\Work\Application as WorkApplication;
use InvalidArgumentException;

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class WeChat
{
    public const OFFICIAL_ACCOUNT = 'official_account';

    public const MINI_APP = 'mini_app';

    public const OPEN_PLATFORM = 'open_platform';

    public const PAY = 'pay';

    public const WORK = 'work';

    public const OPEN_WORK = 'open_work';

    protected const APPLICATIONS = [
        self::OFFICIAL_ACCOUNT => OfficialAccountApplication::class,
        self::MINI_APP => MiniAppApplication::class,
        self::OPEN_PLATFORM => OpenPlatformApplication::class,
        self::PAY => PayApplication::class,
        self::WORK => WorkApplication::class,
        self::OPEN_WORK => OpenWorkApplication::class,
    ];

    protected array $config;

    protected ?LoggerInterface $logger;

    protected ?CacheInterface $cache;

    protected array $instances = [];

    public function __construct(array $config = [], ?LoggerInterface $logger = null, ?CacheInterface $cache = null)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    public function officialAccount(?string $name = null): OfficialAccountApplication
    {
        return $this->app(self::OFFICIAL_ACCOUNT, $name);
    }

    public function miniApp(?string $name = null): MiniAppApplication
    {
        return $this->app(self::MINI_APP, $name);
    }

    public function openPlatform(?string $name = null): OpenPlatformApplication
    {
        return $this->app(self::OPEN_PLATFORM, $name);
    }

    public function pay(?string $name = null): PayApplication
    {
        return $this->app(self::PAY, $name);
    }

    public function work(?string $name = null): WorkApplication
    {
        return $this->app(self::WORK, $name);
    }

    public function openWork(?string $name = null): OpenWorkApplication
    {
        return $this->app(self::OPEN_WORK, $name);
    }

    public function app(string $type, ?string $name = null): object
    {
        if (! isset(self::APPLICATIONS[$type])) {
            throw new InvalidArgumentException(sprintf('Unsupported WeChat application type: %s.', $type));
        }

        $name = $name ?: $this->getDefaultAccount();
        $key = $type . ':' . $name;

        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        $class = self::APPLICATIONS[$type];
        $instance = new $class($this->resolveConfig($type, $name));

        $this->configure($instance);

        return $this->instances[$key] = $instance;
    }

    public function getDefaultAccount(): string
    {
        $default = $this->config['default'] ?? 'default';

        return is_string($default) && $default !== '' ? $default : 'default';
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    public function getCache(): ?CacheInterface
    {
        return $this->cache;
    }

    protected function resolveConfig(string $type, string $name): array
    {
        $config = $this->config['accounts'][$name][$type] ?? null;

        if (! is_array($config)) {
            throw new InvalidArgumentException(
                sprintf('WeChat application "%s" of account "%s" is not configured.', $type, $name)
            );
        }

        return $config;
    }

    protected function configure(object $instance): void
    {
        if ($this->logger && method_exists($instance, 'setLogger')) {
            $instance->setLogger($this->logger);
        }

        if (! $this->cache) {
            return;
        }

        if (method_exists($instance, 'setCache')) {
            $instance->setCache($this->cache);
        }

        if (method_exists($instance, 'setCacheLifetime')) {
            $lifetime = (int) ($this->config['cache']['lifetime'] ?? 1500);

            if ($lifetime > 0) {
                $instance->setCacheLifetime($lifetime);
            }
        }
    }
}