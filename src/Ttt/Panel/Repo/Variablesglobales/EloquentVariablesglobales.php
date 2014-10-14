<?php
namespace Ttt\Panel\Repo\Variablesglobales;

use Illuminate\Database\Eloquent\Model;

class EloquentVariablesglobales implements VariablesglobalesInterface{

    protected $variablesglobale;

    public function __construct(Model $Variablesglobale)
    {
        $this->variablesglobale = $Variablesglobale;
    }

    /**
    * Devuelve un módulo por su ID
    * @param $id int
    * @return object Object con la información del módulo
    */
    public function byId($id)
    {
        return $this->variablesglobale->with('maker', 'updater')
                                      ->findOrFail($id);
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

        $orderBy  = isset($params['ordenPor']) ? $params['ordenPor'] : 'clave';
        $orderDir = isset($params['ordenDir']) ? $params['ordenDir'] : 'asc';

        $query = $this->getQuery($params);

        $variablesglobales = $query->with('maker', 'updater')
                                ->orderBy($orderBy, $orderDir)
                                ->skip($limit * ($page - 1))
                                ->take($limit)
                                ->get();

        $result->items      = $variablesglobales->all();
        $result->totalItems = $this->totalVariablesglobales($params);

        return $result;
    }

    /**
    * Devuelve un módulo según su slug
    * @param $slug string
    * @return object Object con la información del módulo
    */
    public function bySlug($clave)
    {
        return $this->variablesglobale->with('maker', 'updater')
                    ->where('clave', $clave)
                    ->first();
    }

    /**
    * Crea una nueva Variableglobal
    * @param $data array
    * @return boolean
    */
    public function create(array $data)
    {
        //crea el módulo
        $variablesglobale = $this->variablesglobale->create(array(
            'valor'             => $data['valor'],
            'clave'             => $this->slug($data['clave']),
            'creado_por'        => $data['usuario'],
            'actualizado_por'   => $data['usuario']
        ));

        if(! $variablesglobale)
        {
            //¿deberíamos lanzar una excepción?
            return FALSE;
        }

        //return TRUE;
        return $variablesglobale->id;
    }

    /**
    * Actualiza un módulo
    * @param $data array
    * @return boolean
    */
    public function update(array $data) 
    {
    
        $variablesglobale = $this->variablesglobale->with('updater')->findOrFail($data['id']);

        if(! $variablesglobale)
        {
            return FALSE;
        }

        $variablesglobale->actualizado_por   = $data['usuario']; 
        $variablesglobale->clave             = $this->slug(isset($data['clave']) ? $data['clave'] : $variablesglobale['clave'], $data['id'] );
        $variablesglobale->valor             = isset($data['valor']) ? $data['valor'] : $variablesglobale['valor'];

        
        $variablesglobale->update();

        //return TRUE;
        return $variablesglobale->id;
    }

    /**
    * Marca como visible un módulo
    * @param $id int
    * @return boolean
    */
    public function visible($id, $usuario)
    {
        return $this->update(array(
            'id'      => $id,
            'visible' => 1,
            'usuario' => $usuario
        ));
    }

    /**
    * Marca como NO visible un módulo
    * @param $id int
    * @return boolean
    */
    public function noVisible($id, $usuario)
    {

        return $this->update(array(
            'id'      => $id,
            'visible' => 0,
            'usuario' => $usuario
        ));
    }

    /**
    * Elimina un módulo
    * @param $id int
    * @return boolean
    */
    public function delete($id)
    {


        $deletedModule = $this->variablesglobale->findOrFail($id)
                                                ->delete();

        return ($deletedModule === TRUE) ? TRUE : FALSE;
    }

    /**
     * Make a string "slug-friendly" for URLs
     * @param  string $string  Human-friendly tag
     * @param  int $checkId  En el caso de actualizar, queremos que guardar el slug del elemento porque ya existirá
     * @return string       Computer-friendly tag
     */
    protected function slug($string, $checkId = FALSE)
    {
        $originalSlug = $candidateSlug = url_title($string, 'dash', TRUE);
        //queremos que los slugs sean únicos, por lo tanto
        $existe = TRUE;
        //$rule   = '/-[0-9]+$/';
        $matchingRule = '/-(?P<digit>\d+)$/';

        while($existe)
        {
            $item = ($checkId !== FALSE) ? $this->variablesglobale->where('clave', $candidateSlug)
                                                                ->where('id', '!=', $checkId)
                                                                ->first() : $this->variablesglobale->where('clave', $candidateSlug)->first();

            $existe = ! is_null($item);//¿existe elemento (que no sea el actual si se indica)?
            if(! $existe)
            {
                break;
            }

            $nextInt = 1;
            if (preg_match($matchingRule, $candidateSlug, $coincidencias)){
                $nextInt = (int)$coincidencias['digit'];
                $nextInt ++;
            }

            $candidateSlug = $originalSlug . '-' . $nextInt;
        }

        return $candidateSlug;
    }

    /**
    * Devuelve el total de elementos de una consulta según parámetros
    *
    * @param $params array
    * @return int
    */
    protected function totalVariablesglobales(array $params)
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
        $query = $this->variablesglobale->newQuery();
        if(! is_null($params['clave'])   )
        {
            $query->where('clave', 'LIKE', '%' . $params['clave'] . '%');
        }
        
        if(! is_null($params['valor'])   )
        {
            $query->where('valor', 'LIKE', '%' . $params['valor'] . '%');
        }

        return $query;
    }
}
