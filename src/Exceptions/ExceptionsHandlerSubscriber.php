<?php 

namespace App\Exceptions;

use App\Logging\Log;
use App\Utils\Http\HttpStatus;
use Exception;
use PDOException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionsHandlerSubscriber implements EventSubscriberInterface
{
    /**
     * @param array<class-string, array{log_level: string|null, status_code: int<100,599>|null, log_channel: string|null}> $exceptionsMapping
     */
    public function __construct(
        // protected string|object|array|null $controller,
        protected ?DebugLoggerInterface $logger = null,
        protected bool $debug = false,
        protected array $exceptionsMapping = [],
        protected array $loggers = [],
    ) {
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
                $code = HttpStatus::tryFrom($ex->getCode());
            else $code=null;
        }catch(Exception $e)
        {
            $code=null;
        }

        if($ex instanceof PDOException)
        {
            $erro->setMessage("Ocorreu um erro ao processar a solicitação no banco de dados, tente novamente mais tarde ou acione o administrador do sistema.");
        }

        if(!($ex instanceof InvalidEmailOrPasswordException) && !($ex instanceof UnauthenticatedException)) new Log($ex);

        $event->setResponse(new JsonResponse($erro->toUserReturn(), $code == null ? HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value : $code->value));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
   
}