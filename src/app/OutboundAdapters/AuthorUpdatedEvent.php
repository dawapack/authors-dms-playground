<?php

declare(strict_types=1);

namespace AuthorsDMS\OutboundAdapters;

use Chassis\Framework\Adapters\Operations\AbstractOperationsAdapter;

class AuthorUpdatedEvent extends AbstractOperationsAdapter
{
    /**
     * Use a dedicated channel for events calls - fire and forget call type
     *
     * @var string
     */
    protected string $channelName = "authors/outbound/events";

    /**
     * Events must provide the routing key - see exchange bindings of the channel
     *
     * @var string
     */
    protected string $routingKey = "";

    /**
     * To be more specific, we can set the message type here and will be filled by the
     * outbound adapter. The other way is to set up the message type property.
     *
     * @var string
     */
    protected string $operation = "authorUpdated";
}
