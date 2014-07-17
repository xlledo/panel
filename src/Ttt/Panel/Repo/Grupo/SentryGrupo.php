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
    * @param $data array
    * @return boolean
    */
    public function update(array $data)
    {
        /*$modulo = $this->modulo->with('updater')->findOrFail($data['id']);

        if(! $modulo)
        {
            return FALSE;
        }

        $modulo->actualizado_por   = $data['usuario'];
        $modulo->nombre            = isset($data['nombre']) ? $data['nombre'] : $modulo['nombre'];
        $modulo->slug              = $this->slug($modulo->nombre, $modulo->id);
        $modulo->visible           = $data['visible'];

        $modulo->update();

        //return TRUE;
        return $modulo->id;*/
    }

    /**
    * Elimina un módulo
    * @param $id int
    * @return boolean
    */
    public function delete($id)
    {
        /*$deletedModule = $this->modulo->findOrFail($id)
                                                ->delete();

        return ($deletedModule === TRUE) ? TRUE : FALSE;*/
    }
}
