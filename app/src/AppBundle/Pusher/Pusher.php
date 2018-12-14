<?php

namespace AppBundle\Pusher;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\Wamp\Topic;

class Pusher implements WampServerInterface
{
    private const NEW_NICKNAME_TOPIC_kEY = 'Pusher::onNewNickname';

    private $subscribedTopics = [];

    /**
     * @param ConnectionInterface $conn
     * @param \Ratchet\Wamp\Topic|string $topic
     */
    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $this->subscribedTopics[self::NEW_NICKNAME_TOPIC_kEY] = $topic;
    }

    /**
     * @param string $entry
     */
    public function onNewNickname(string $entry)
    {
        if (\count($this->subscribedTopics) > 0) {
            $topic = $this->subscribedTopics[self::NEW_NICKNAME_TOPIC_kEY];

            $topic->broadcast(
                \json_decode($entry, true)
            );
        }
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
    }

    public function onClose(ConnectionInterface $conn)
    {
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
    }
}
