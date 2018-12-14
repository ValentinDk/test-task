<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZMQ;
use ZMQContext;

class MainController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('/main/index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \ZMQSocketException
     */
    public function getResponseAction(Request $request): JsonResponse
    {
        $context = new ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH);

        $socket->connect($this->getParameter('pusher_host'));
        $socket->send($request->getContent());

        return new JsonResponse();
    }
}
