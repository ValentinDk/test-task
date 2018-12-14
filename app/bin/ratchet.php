#!/usr/bin/env php
<?php
// bin/ratchet.php

require __DIR__.'/../vendor/autoload.php';

use Ratchet\ConnectionInterface;

class Pusher implements \Ratchet\Wamp\WampServerInterface {
    /**
     * A lookup of all the topics clients have subscribed to
     */
    protected $subscribedTopics = array();

    public function onSubscribe(ConnectionInterface $conn, $topic) {
        $this->subscribedTopics['Pusher::onNewNickname'] = $topic;
    }

    /**
     * @param string JSON'ified string we'll receive from ZeroMQ
     */
    public function onBlogEntry($entry) {
        $entryData = json_decode($entry, true);


        $topic = $this->subscribedTopics['Pusher::onNewNickname'];
        $topic->broadcast($entryData);
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        // TODO: Implement onCall() method.
    }

    public function onClose(ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
    }
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }
    public function onOpen(ConnectionInterface $conn)
    {
        // TODO: Implement onOpen() method.
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        // TODO: Implement onPublish() method.
    }
    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        // TODO: Implement onUnSubscribe() method.
    }
}

$loop   = React\EventLoop\Factory::create();
$pusher = new Pusher();

$context = new React\ZMQ\Context($loop);
$pull = $context->getSocket(ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555');
$pull->on('message', array($pusher, 'onBlogEntry'));

$webSock = new React\Socket\Server('0.0.0.0:8080', $loop);
$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new Ratchet\Wamp\WampServer(
                $pusher
            )
        )
    ),
    $webSock
);

$loop->run();