<?php
namespace Ttt\Panel\Repo\Fichero;

use Illuminate\Database\Eloquent\Model;

class EloquentFichero implements FicheroInterface{

    
    protected $table = 'ficheros';
    protected $fichero;

    public function __construct(Model $fichero)
    {
        $this->fichero = $fichero;
    }

    public function byId($id) {
        
    }

    public function byPage($page = 1, $limit = 10, $params = array()) {
        
        $result = new \StdClass;
        $result->page       = $page;
        $result->limit      = $limit;
        $result->totalItems = 0;
        $result->items      = array();

        $orderBy  = isset($params['ordenPor']) ? $params['ordenPor'] : 'clave';
        $orderDir = isset($params['ordenDir']) ? $params['ordenDir'] : 'asc';

        $query = $this->getQuery($params);

        $ficheros       = $query->with('maker', 'updater')
                                ->orderBy($orderBy, $orderDir)
                                ->skip($limit * ($page - 1))
                                ->take($limit)
                                ->get();

        $result->items      = $ficheros->all();
        $result->totalItems = $this->totalFicheros($params);

        return $result;
    }

    public function create(array $data) {
        
    }

    public function delete($id) {
        
    }

    public function noVisible($id) {
        
    }

    public function update(array $data) {
        
    }

    public function visible($id) {
        
    }

    /**
    * Devuelve el total de elementos de una consulta según parámetros
    *
    * @param $params array
    * @return int
    */
    protected function totalFicheros(array $params)
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
        $query = $this->fichero->newQuery();
        if(! is_null($params['nombre']))
        {
            $query->where('nombre', 'LIKE', '%' . $params['nombre'] . '%');
        }

        return $query;
    }    
    
}