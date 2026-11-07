<?php

declare(strict_types=1);

namespace Youvision\ThinkEasyWeChat;

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use think\Cache;
use think\Service as ThinkService;

class Service extends ThinkService
{
    public function register(): void
    {
        $this->app->bind(WeChat::class, function () {
            $config = (array) $this->app->config->get('easywechat', []);

            return new WeChat(
                $config,
                $this->resolveLogger($config),
                $this->resolveCache($config)
            );
        });

        $this->app->bind('easywechat', function () {
            return $this->app->make(WeChat::class);
        });
    }

    protected function resolveLogger(array $config): ?LoggerInterface
    {
        $options = (array) ($config['log'] ?? []);

        if (($options['enabled'] ?? true) === false) {
            return null;
        }

        $logger = $this->app->make(LoggerInterface::class);
        $channel = $options['channel'] ?? null;

        if (is_string($channel) && $channel !== '' && method_exists($logger, 'channel')) {
            return $logger->channel($channel);
        }

        return $logger;
    }

    protected function resolveCache(array $config): ?CacheInterface
    {
        $options = (array) ($config['cache'] ?? []);

        if (($options['enabled'] ?? true) === false) {
            return null;
        }

        $cache = $this->app->make(Cache::class);
        $store = $options['store'] ?? null;

        if (is_string($store) && $store !== '' && method_exists($cache, 'store')) {
            return $cache->store($store);
        }

        return $cache;
    }
}