<?php

namespace App\Shared\Infrastructure\Symfony\EventSubscriber;

use App\Shared\Domain\Entity\Errors;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onValidationException', 10],
        ];
    }

    public function onValidationException(ExceptionEvent $event) : void
    {
        $exception = $event->getThrowable();


        if(!$exception instanceof UnprocessableEntityHttpException && !$exception instanceof ValidationFailedException)
        {
            return;
        }

        $error = new Errors(message:"Unprocessable Entity Request", code: 422, filename:$exception->getFile(), line:$exception->getLine(), previus:$exception->getPrevious())->toUserReturn();

        if($exception instanceof ValidationFailedException)
        {
            $error["errors"] = $this->getArrayViolations($exception);
        }
        else if($exception->getPrevious() instanceof ValidationFailedException)
        {
            $error["errors"] = $this->getArrayViolations($exception->getPrevious());
        }

        $response = new JsonResponse($error, 422);
        $event->setResponse($response);
    }

    private function getArrayViolations(ValidationFailedException $exception) : array
    {
        $violations = $exception->getViolations();
        $errors  = [];
        foreach($violations as $violation)
        {
            isset($errors[$violation->getPropertyPath()]) ? array_push($errors[$violation->getPropertyPath()], $violation->getMessage()) : $errors[$violation->getPropertyPath()] = [$violation->getMessage()];
        }
        return $errors;
    }
}