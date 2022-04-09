<?php

declare(strict_types=1);

namespace AuthorsDMS\Services;

use Chassis\Framework\Adapters\Message\InboundMessageInterface;
use Chassis\Framework\Logger\Logger;

class PostEventsService
{
    /**
     * Nobody cares about implementation
     *
     * @operation somethingDeleted
     *
     * @param InboundMessageInterface $message
     *
     * @return void
     */
    public function __invoke(InboundMessageInterface $message): void
    {
        Logger::info(
            'got event from posts microservices',
            [
                'component' => 'author_events_service_info',
                'message' => [
                    'properties' => $message->getProperties(),
                    'headers' => $message->getHeaders(),
                    'payload' => $message->getBody(),
                ],
            ]
        );
    }
}
