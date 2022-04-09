<?php

declare(strict_types=1);

namespace AuthorsDMS\Providers;

use AuthorsDMS\OutboundAdapters\AuthorCreatedEvent;
use AuthorsDMS\OutboundAdapters\AuthorUpdatedEvent;
use AuthorsDMS\Services\PostEventsService;
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
        'postCreated' => PostEventsService::class,
        'postUpdated' => PostEventsService::class,
        'postDeleted' => PostEventsService::class,
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
