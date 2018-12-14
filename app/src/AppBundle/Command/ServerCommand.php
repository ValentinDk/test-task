<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Pusher\Pusher;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use React\Socket\Server;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;

class ServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ws:start')
            ->setDescription('Start the web-socket server.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pusherHost = $this->getContainer()->getParameter('pusher_host');
        $loop = Factory::create();
        $pusher = new Pusher();
        $context = new Context($loop);
        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        $webSock = new Server('0.0.0.0:8080', $loop);

        $pull->bind($pusherHost);
        $pull->on('message', [$pusher, 'onNewNickname']);

        new IoServer(
            new HttpServer(
                new WsServer(
                    new WampServer(
                        $pusher
                    )
                )
            ),
            $webSock
        );

        $loop->run();
    }
}
