<?php

declare(strict_types=1);

namespace AuthorsDMS\OutboundAdapters;

use AuthorsDMS\Exceptions\NotFoundException;
use Cache\Adapter\Common\CacheItem;
use Chassis\Framework\Adapters\Outbound\Cache\CacheFactoryInterface;
use Chassis\Framework\Adapters\Outbound\Cache\RedisCache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Ramsey\Uuid\Uuid;

use function Chassis\Helpers\app;

class RedisRepository implements RepositoryInterface
{
    private const NOT_FOUND_MESSAGE = 'requested resource not found';
    private RedisCache $cache;

    /**
     * @param CacheFactoryInterface|null $cache
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(CacheFactoryInterface $cache = null)
    {
        $this->cache = is_null($cache) ? app(CacheFactoryInterface::class) : $cache;
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $id): array
    {
        $key = $this->cache->keyPrefix() . $id;

        // get resource
        $item = $this->cache->pool()->getItem($key);

        // handle not found exception
        if (!$item->isHit()) {
            throw new NotFoundException(self::NOT_FOUND_MESSAGE, 404);
        }

        return $item->get();

    }

    /**
     * @inheritDoc
     */
    public function saveItem(array $values): string
    {
        $id = Uuid::uuid4()->toString();
        $key = $this->cache->keyPrefix() . $id;

        // create resource
        $this->cache->pool()->save(new CacheItem($key, true, $values));

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function updateItem(string $id, array $values): void
    {
        $key = $this->cache->keyPrefix() . $id;
        $item = $this->cache->pool()->getItem($key);

        if (!$item->isHit()) {
            throw new NotFoundException(self::NOT_FOUND_MESSAGE, 404);
        }

        // update resource data
        $item->set(array_merge($item->get(), $values));
        $this->cache->pool()->save($item);
    }

    /**
     * @inheritDoc
     */
    public function deleteItem(string $id): void
    {
        $key = $this->cache->keyPrefix() . $id;
        $item = $this->cache->pool()->getItem($key);

        if (!$item->isHit()) {
            throw new NotFoundException(self::NOT_FOUND_MESSAGE, 404);
        }

        // delete resource
        $this->cache->pool()->deleteItem($key);
    }
}
