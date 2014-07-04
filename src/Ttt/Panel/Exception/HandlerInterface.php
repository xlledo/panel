<?php
namespace Ttt\Panel\Exception;

interface HandlerInterface
{
    /**
    * Controla las Excepciones de la librería
    *
    * @param Ttt\Exception\ImplException
    * @return void
    */
    public function handle(\Exception $exception);
}
