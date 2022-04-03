<?php

declare(strict_types=1);

namespace AuthorsDMS\Providers;

use Chassis\Framework\Providers\RoutingServiceProvider;
use AuthorsDMS\OutboundAdapters\DemoOperationDelete;
use AuthorsDMS\OutboundAdapters\DemoOperationDeletedEvents;
use AuthorsDMS\OutboundAdapters\DemoOperationGetAsync;
use AuthorsDMS\OutboundAdapters\DemoOperationGetSync;
use AuthorsDMS\Services\DemoDeleteEventService;
use AuthorsDMS\Services\DemoMoreService;
use AuthorsDMS\Services\DemoService;

class MessageRoutingServiceProvider extends RoutingServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $inboundRoutes = [
        'createSomething' => [DemoService::class, 'create'],
        'getSomething' => [DemoService::class, 'get'],
        'getSomethingResponse' => [DemoMoreService::class, 'complete'],
        'deleteSomething' => [DemoService::class, 'delete'],
        'somethingDeleted' => DemoDeleteEventService::class,
    ];

    /**
     * @var array|string[]
     */
    protected array $outboundRoutes = [
        'getSomethingSync' => DemoOperationGetSync::class,
        'getSomethingAsync' => DemoOperationGetAsync::class,
        'deleteSomething' => DemoOperationDelete::class,
        'deleteSomethingEvent' => DemoOperationDeletedEvents::class,
    ];
}
