<?php

declare(strict_types=1);

namespace AuthorsDMS\Providers;

use AuthorsDMS\OutboundAdapters\AuthorCreatedEvent;
use AuthorsDMS\OutboundAdapters\AuthorUpdatedEvent;
use AuthorsDMS\Services\EventsService;
use Chassis\Framework\Providers\RoutingServiceProvider;
use AuthorsDMS\OutboundAdapters\AuthorDeletedEvent;
use AuthorsDMS\Services\AuthorService;

class MessageRoutingServiceProvider extends RoutingServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $inboundRoutes = [
        // commands
        'getAuthor' => [AuthorService::class, 'get'],
        'createAuthor' => [AuthorService::class, 'create'],
        'updateAuthor' => [AuthorService::class, 'update'],
        'deleteAuthor' => [AuthorService::class, 'delete'],

        // events
        'postCreated' => EventsService::class,
        'postUpdated' => EventsService::class,
        'postDeleted' => EventsService::class,
    ];

    /**
     * @var array|string[]
     */
    protected array $outboundRoutes = [
        // events
        'authorCreated' => AuthorCreatedEvent::class,
        'authorUpdated' => AuthorUpdatedEvent::class,
        'authorDeleted' => AuthorDeletedEvent::class,
    ];
}
