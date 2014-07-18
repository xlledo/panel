<?php
namespace Ttt\Panel\Repo\Grupo;

interface GrupoInterface extends \Cartalyst\Sentry\Groups\ProviderInterface{

    /**
    * Devuelve el listado de grupos
    * @param $order array los parámetros para la ordenación orderBy y orderDir
    * @return object Collection con el listado de grupos
    */
    public function findAllBy($order);

    /**
    * Actualiza un grupo
    * @param $group array
    * @return boolean
    */
    public function update(\Cartalyst\Sentry\Groups\GroupInterface $group);
}
