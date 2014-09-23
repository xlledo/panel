<?php
namespace Ttt\Panel\Repo\Log;

use Illuminate\Database\Eloquent\Model;

class EloquentLog implements LogInterface{

    protected $log;

    public function __construct(Model $log)
    {
        $this->log = $log;
    }

    /**
    * Crea un nuevo módulo
    * @param $data array
    * @param $usuario Cartalyst\Sentry\Users\Eloquent\User
    * @return boolean
    */
    public function create(array $data, $usuario = NULL)
    {
        //crea el módulo
        $thisLog = $this->log->create($data);

        $cleanLog = $this->log->find($thisLog->id);
        if(! is_null($usuario))
        {
            $cleanLog->usuario()->associate($usuario)->save();
        }

        return $cleanLog;
    }
}
