<?php 

namespace App\Shared\Infrastructure\Symfony\EventSubscriber;

use App\Exceptions\EmailInvalidatedException;
use App\Exceptions\InvalidEmailOrPasswordException;
use App\Exceptions\UnauthenticatedException;
use App\Shared\Domain\Entity\Errors;
use Exception;
use PDOException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionsHandlerSubscriber implements EventSubscriberInterface
{
    /**
     * @param array<class-string, array{log_level: string|null, status_code: int<100,599>|null, log_channel: string|null}> $exceptionsMapping
     */
    public function __construct(
        protected ?LoggerInterface $logger = null,
        protected bool $debug = false,
        protected array $exceptionsMapping = [],
        protected array $loggers = [],
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 5],
        ];
    }

    public function onKernelException(ExceptionEvent $event) : void
    {

        if (!$this->debug && $event->isKernelTerminating()) {
            return;
        }

        $ex = $event->getThrowable(); 

        $erro = new Errors(
            message: $ex->getMessage(),
            code:$ex->getCode(),
            filename:$ex->getFile(),
            line:$ex->getLine(),
            previus: $ex->getPrevious()
            );
        
        if($ex instanceof EmailInvalidatedException){
            $erro->setMessage($erro->getMessage() . " Enviamos um novo email com o link para confirmação.");
        }
        try{
            if(is_int($ex->getCode()))
                $code = $ex->getCode() < 600 ? $ex->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
            else $code=null;
        }catch(Exception $e)
        {
            $code=null;
        }

        if($ex instanceof PDOException)
        {
            $erro->setMessage("Ocorreu um erro ao processar a solicitação no banco de dados, tente novamente mais tarde ou acione o administrador do sistema.");
        }
        
        if($ex instanceof MethodNotAllowedHttpException)
        {
            $code = 405;
        }

        $erro->setCode($code == null ? Response::HTTP_INTERNAL_SERVER_ERROR : $code);

        if(!($ex instanceof InvalidEmailOrPasswordException) && !($ex instanceof UnauthenticatedException))
        {
            $this->logger->error($erro->getMessage(), $erro->toLogReturn());
        }
        $event->setResponse(new JsonResponse($erro->toUserReturn(), $erro->getCode()));
    }
   
}