<?php
namespace Ttt\Panel\Repo\Usuario;

class SentryUsuario extends \Cartalyst\Sentry\Users\Eloquent\Provider implements UsuarioInterface{

    /**
    * Devuelve el listado de módulos y el total de los mismos
    * @param $page int
    * @param $limit int
    * @param $params array los parámetros para los filtros y la ordenación
    * @return object Object con Items y totalItems para la paginación
    */
    public function byPage($page = 1, $limit = 10, $params = array())
    {
        $result = new \StdClass;
        $result->page       = $page;
        $result->limit      = $limit;
        $result->totalItems = 0;
        $result->items      = array();

        $orderBy  = isset($params['ordenPor']) ? $params['ordenPor'] : 'nombre';
        $orderDir = isset($params['ordenDir']) ? $params['ordenDir'] : 'asc';

        if($orderBy == 'nombre')
        {
            $orderBy = 'last_name';
        }

        $query = $this->getQuery($params);

        $usuarios = $query->with('groups')
                    ->orderBy($orderBy, $orderDir)
                    ->skip($limit * ($page - 1))
                    ->take($limit)
                    ->get();

        $result->items      = $usuarios->all();
        $result->totalItems = $this->totalUsuarios($params);

        return $result;
    }

    /**
    * Actualiza un módulo
    * @param $group array
    * @throws \Ttt\Panel\Exception\TttException
    *
    * @return void
    */
    public function update(\Cartalyst\Sentry\Users\UserInterface $user)
    {
        if(! $user->save())
        {
            throw new \Ttt\Panel\Exception\TttException;
        }
    }

    /**
    * Devuelve el total de elementos de una consulta según parámetros
    *
    * @param $params array
    * @return int
    */
    protected function totalUsuarios(array $params)
    {
        return $this->getQuery($params)->count();
    }

    /**
    * Devuelve la consulta con las condiciones establecidas
    *
    * @param array $params
    * @return Illuminate\Database\Query\Builder $query
    */
    protected function getQuery(array $params)
    {
        $model = $this->createModel();

        $query = $model->newQuery();
        if(! is_null($params['nombre']))
        {
            $query->where(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $params['nombre'] . '%');
        }

        if(! is_null($params['email']))
        {
            $query->where('email', 'LIKE', '%' . $params['email'] . '%');
        }

        return $query;
    }
}
