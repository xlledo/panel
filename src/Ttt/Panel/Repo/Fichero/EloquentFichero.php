<?php
namespace Ttt\Panel\Repo\Fichero;

use Illuminate\Database\Eloquent\Model;

class EloquentFichero implements FicheroInterface{
    
    protected $fichero;

    public function __construct(Model $fichero){
        $this->fichero = $fichero;
    }

    public function byId($id) {
        return $this->fichero->with('maker','updater')
                             ->findOrFail($id);
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
        
        //Crea el fichero
        
        $fichero = $this->fichero->create(
                array(
                    'nombre'            => $data['nombre'],
                    'fichero'           => $data['fichero'],
                    'creado_por'        => $data['usuario'],
                    'actualizado_por'   => $data['usuario'],
                    'ruta'              => $data['ruta'],
                    'mime'              => $data['mime'],
                    //'tipo'              => $data['tipo'],
                    'titulo_defecto'    => $data['titulo_defecto'],
                    'alt_defecto'       => $data['alt_defecto'],
                    'descripcion_defecto'   => $data['descripcion_defecto'],
                    'enlace_defecto'        => $data['enlace_defecto']
                ));
        
        return $fichero->id;

    }

    public function delete($id) {
        $deletedFichero = $this->fichero->findOrFail($id)
                                        ->delete();
        
        return ($deletedFichero === TRUE) ? TRUE : FALSE;

    }

    public function update(array $data) {
        $fichero = $this->fichero->findOrFail($data['id']);
        
        if( !$fichero ){
            return FALSE;
        }
        
        $fichero->nombre                = $data['nombre'];
        
        $fichero->titulo_defecto        = (array_key_exists('titulo_defecto', $data))       ? $data['titulo_defecto'] : '' ;
        $fichero->alt_defecto           = (array_key_exists('alt_defecto', $data))          ? $data['alt_defecto'] : '' ;
        $fichero->enlace_defecto        = (array_key_exists('enlace_defecto', $data))       ? $data['enlace_defecto'] : '' ;
        $fichero->descripcion_defecto   = (array_key_exists('descripcion_defecto', $data))  ? $data['descripcion_defecto'] : '' ;
        
        $fichero->update();
        
        return $fichero;
    }

    public function visible($id) {
        throw new Exception('No implementado aún');
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

    public function noVisible($id) {
        
    }

}