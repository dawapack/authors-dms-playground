<?php

declare(strict_types=1);

namespace AuthorsDMS\Services;

use Chassis\Framework\Adapters\Message\InboundMessageInterface;
use Chassis\Framework\Bus\Exceptions\MessageBusException;
use Chassis\Framework\Logger\Logger;
use JsonException;

class EventsService
{
    /**
     * Nobody cares about implementation
     *
     * @operation somethingDeleted
     *
     * @param InboundMessageInterface $message
     *
     * @return void
     *
     * @throws MessageBusException
     * @throws JsonException
     */
    public function __invoke(InboundMessageInterface $message): void
    {
        switch ($message->getProperty('type')) {
            case "postCreated":
            case "postUpdated":
            case "postDeleted":
                // nothing to do - just skip
                break;
            default:
                Logger::info(
                    'got unhandled event type',
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
}
