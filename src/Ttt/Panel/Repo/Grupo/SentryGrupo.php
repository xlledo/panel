<?php
namespace Ttt\Panel\Repo\Grupo;

class SentryGrupo extends \Cartalyst\Sentry\Groups\Eloquent\Provider implements GrupoInterface{

    /**
    * Devuelve el listado de grupos
    * @param $order array los parámetros para la ordenación orderBy y orderDir
    * @return object Collection con el listado de grupos
    */
    public function findAllBy($order)
    {

        $orderBy  = isset($order['ordenPor']) ? $order['ordenPor'] : 'name';
        $orderDir = isset($order['ordenDir']) ? $order['ordenDir'] : 'asc';

        $model = $this->createModel();
        return $model->newQuery()
                            ->orderBy($orderBy, $orderDir)
                            ->get();//->all() con el all nos devuelve un array, vaya tela
    }

    /**
    * Actualiza un módulo
    * @param $group array
    * @throws \Ttt\Panel\Exception\TttException
    *
    * @return void
    */
    public function update(\Cartalyst\Sentry\Groups\GroupInterface $group)
    {
        if(! $group->save())
        {
            throw new \Ttt\Panel\Exception\TttException;
        }
    }
}
