<?php

namespace App\Shared\Infrastructure\Symfony\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsSubscriber implements EventSubscriberInterface
{
    public function __construct(private string $allowOrigin, private LoggerInterface $logger)
    {}

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 1000],
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    public function onRequest(RequestEvent $event) : void
    {
        if(!$event->isMainRequest()) return;

        $request = $event->getRequest();
        if($request->getMethod() !== "OPTIONS") return;

        $response = new JsonResponse("", Response::HTTP_OK);

        $this->addCorsheader($request, $response);

        $event->setResponse($response);
    }

    public function onResponse(ResponseEvent $event)
    {
        if(!$event->isMainRequest()) return;

        $request = $event->getRequest();
        $response = $event->getResponse();
        
        $this->addCorsheader($request, $response);
    }

    public function addCorsheader(Request $request, Response $response) : void
    {
        $origin = $request->headers->get("Origin");

        if($origin !== $this->allowOrigin) return;

        $response->headers->set("Access-Control-Allow-Origin", $origin);

        $response->headers->set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
        $response->headers->set("Access-Control-Allow-Headers", "Content-Type, Authorization");
        $response->headers->set("Access-Control-Allow-Credentials", "true");
        $response->headers->set("Access-Control-Max-Age", "86400");
    }
}