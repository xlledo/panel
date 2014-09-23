<?php
namespace Ttt\Panel\Repo\Log;

interface LogInterface{

    /**
    * Crea un nuevo módulo
    * @param $data array
    * @param $usuario Cartalyst\Sentry\Users\Eloquent\User
    * @return \Ttt\Panel\Repo\Log\Log
    */
    public function create(array $data, $usuario = NULL);
}
