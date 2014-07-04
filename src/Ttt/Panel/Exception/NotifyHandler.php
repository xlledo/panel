<?php
namespace Ttt\Panel\Exception;

use Ttt\Panel\Service\Notification\NotifierInterface;

class NotifyHandler implements HandlerInterface
{

    protected $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
    * Controla las Excepciones de la librería
    *
    * @param Ttt\Exception\ImplException
    * @return void
    */
    public function handle(\Exception $exception)
    {
        $this->sendException($exception);
    }

    /**
    * Envía excepción al notificador
    *
    * @param \Exception $e
    * @return void
    */
    protected function sendException(\Exception $e)
    {
        $this->notifier->notify('Error: ' . get_class($e), array(
            'mensaje' => $e->getMessage()
        ));
    }
}
