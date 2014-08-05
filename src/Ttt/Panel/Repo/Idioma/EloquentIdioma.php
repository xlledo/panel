<?php
namespace Ttt\Panel\Repo\Idioma;

use Illuminate\Database\Eloquent\Model;

class EloquentIdioma implements IdiomaInterface{

    protected $idioma;

    public function __construct(Model $idioma)
    {
        $this->idioma = $idioma;
    }

    /**
    * Devuelve un módulo por su ID
    * @param $id int
    * @return object Object con la información del módulo
    */
    public function byId($id)
    {
        return $this->idioma->findOrFail($id);
    }

    /**
    * Devuelve los módulos paginados
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

        $query = $this->getQuery($params);

        $idiomas = $query->orderBy($orderBy, $orderDir)
                    ->skip($limit * ($page - 1))
                    ->take($limit)
                    ->get();

        $result->items      = $idiomas->all();
        $result->totalItems = $this->totalIdiomas($params);

        return $result;
    }

    /**
    * Crea un nuevo módulo
    * @param $data array
    * @return boolean
    */
    public function create(array $data)
    {
        //crea el módulo
        $idioma = $this->idioma->create(array(
            'nombre'          => $data['nombre'],
            'codigo_iso_2'    => $data['codigo_iso_2'],
            'codigo_iso_3'    => $data['codigo_iso_3'],
            'visible'         => $data['visible'],
            'principal'       => $data['principal']
        ));

        if(! $idioma)
        {
            //¿deberíamos lanzar una excepción?
            return FALSE;
        }

        $this->checkUniquePrincipal($idioma);//solamente puede haber un idioma principal

        //return TRUE;
        return $idioma;
    }

    /**
    * Actualiza un módulo
    * @param $data array
    * @return boolean
    */
    public function update(array $data)
    {
        $idioma = $this->idioma->findOrFail($data['id']);

        if(! $idioma)
        {
            return FALSE;
        }

        $idioma->nombre            = isset($data['nombre']) ? $data['nombre'] : $idioma['nombre'];
        $idioma->codigo_iso_2      = isset($data['codigo_iso_2']) ? $data['codigo_iso_2'] : $idioma['codigo_iso_2'];
        $idioma->codigo_iso_3      = isset($data['codigo_iso_3']) ? $data['codigo_iso_3'] : $idioma['codigo_iso_3'];
        $idioma->visible           = isset($data['visible']) ? $data['visible'] : $idioma['visible'];
        $idioma->principal         = isset($data['principal']) ? $data['principal'] : $idioma['principal'];

        $idioma->update();

        $this->checkUniquePrincipal($idioma);//solamente puede haber un idioma principal

        //return TRUE;
        return $idioma;
    }

    /**
    * Marca como visible un módulo
    * @param $id int
    * @return boolean
    */
    public function visible($id)
    {
        return $this->update(array(
            'id'      => $id,
            'visible' => 1
        ));
    }

    /**
    * Marca como NO visible un módulo
    * @param $id int
    * @return boolean
    */
    public function noVisible($id)
    {
        return $this->update(array(
            'id'      => $id,
            'visible' => 0
        ));
    }

    /**
    * Elimina un módulo
    * @param $id int
    * @return boolean
    */
    public function delete($id)
    {
        $deletedIdioma = $this->idioma->findOrFail($id)
                                                ->delete();

        return ($deletedIdioma === TRUE) ? TRUE : FALSE;
    }

    /**
    * Devuelve el total de elementos de una consulta según parámetros
    *
    * @param $params array
    * @return int
    */
    protected function totalIdiomas(array $params)
    {
        return $this->getQuery($params)->count();
    }

    /**
    * Se encarga de que solo haya un idioma marcado como principal
    * @param $idioma Ttt\Panel\Repo\Idioma\Idioma
    *
    */
    protected function checkUniquePrincipal(\Ttt\Panel\Repo\Idioma\Idioma $idioma)
    {
        if($idioma->principal)
        {
            $all = $this->getQuery()->get()->all();
            foreach($all as $id)
            {
                if($id->principal && $id->id != $idioma->id)
                {
                    $id->principal = FALSE;
                    $id->update();
                }
            }
        }
    }
    

            
    /**
    * Devuelve la consulta con las condiciones establecidas
    *
    * @param array $params
    * @return Illuminate\Database\Query\Builder $query
    */
    protected function getQuery(array $params = array())
    {
        $query = $this->idioma->newQuery();
        if(isset($params['nombre']) && ! is_null($params['nombre']))
        {
            $query->where('nombre', 'LIKE', '%' . $params['nombre'] . '%');
        }

        return $query;
    }
}
