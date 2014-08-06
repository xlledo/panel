<?php
namespace Ttt\Panel\Repo\Modulo;

use Illuminate\Database\Eloquent\Model;

class EloquentModulo implements ModuloInterface{

    protected $modulo;

    public function __construct(Model $modulo)
    {
        $this->modulo = $modulo;
    }

    /**
    * Devuelve un módulo por su ID
    * @param $id int
    * @return object Object con la información del módulo
    */
    public function byId($id)
    {
        return $this->modulo->with('maker', 'updater')
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

        $orderBy  = isset($params['ordenPor']) ? $params['ordenPor'] : 'nombre';
        $orderDir = isset($params['ordenDir']) ? $params['ordenDir'] : 'asc';

        $query = $this->getQuery($params);

        $modulos = $query->with('maker', 'updater')
                    ->orderBy($orderBy, $orderDir)
                    ->skip($limit * ($page - 1))
                    ->take($limit)
                    ->get();

        $result->items      = $modulos->all();
        $result->totalItems = $this->totalModulos($params);

        return $result;
    }

    /**
    * Devuelve un módulo según su slug
    * @param $slug string
    * @return object Object con la información del módulo
    */
    public function bySlug($slug)
    {
        return $this->modulo->with('maker', 'updater')
                    ->where('slug', $slug)
                    ->first();
    }

    /**
    * Crea un nuevo módulo
    * @param $data array
    * @return boolean
    */
    public function create(array $data)
    {
        //crea el módulo
        $modulo = $this->modulo->create(array(
            'creado_por'      => $data['usuario'],
            'actualizado_por' => $data['usuario'],
            'nombre'          => $data['nombre'],
            'slug'            => $this->slug($data['nombre']),
            'visible'         => $data['visible']
        ));

        if(! $modulo)
        {
            //¿deberíamos lanzar una excepción?
            return FALSE;
        }

        //return TRUE;
        return $modulo->id;
    }

    /**
    * Actualiza un módulo
    * @param $data array
    * @return boolean
    */
    public function update(array $data)
    {
        $modulo = $this->modulo->with('updater')->findOrFail($data['id']);

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
        return $modulo->id;
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
        $deletedModule = $this->modulo->findOrFail($id)
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
            $item = ($checkId !== FALSE) ? $this->modulo->where('slug', $candidateSlug)
                                                                ->where('id', '!=', $checkId)
                                                                ->first() : $this->modulo->where('slug', $candidateSlug)->first();

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
    protected function totalModulos(array $params)
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
        $query = $this->modulo->newQuery();
        if(! is_null($params['nombre']))
        {
            $query->where('nombre', 'LIKE', '%' . $params['nombre'] . '%');
        }

        return $query;
    }
}
