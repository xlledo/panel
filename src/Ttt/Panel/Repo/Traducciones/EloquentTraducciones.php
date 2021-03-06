<?php

namespace Ttt\Panel\Repo\Traducciones;

use Illuminate\Database\Eloquent\Model;

class EloquentTraducciones implements TraduccionesInterface{

    protected $traduccion;
    protected $traduccion_i18n;

    public function __construct(Model $Traduccion, Model $Traduccion_i18n)
    {
        $this->traduccion = $Traduccion;
        $this->traduccion_i18n = $Traduccion_i18n;
    }

    /**
    * Devuelve una traduccion por su ID
    * @param $id int
    * @return object Object con la información del módulo
    */
    public function byId($id)
    {
        return $this->traduccion->with('maker', 'updater')
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

        $idioma = \App::make('Ttt\Panel\Repo\Idioma\IdiomaInterface')->idiomaPrincipal();     
        
        $orderBy  = isset($params['ordenPor']) ? $params['ordenPor'] : 'clave';
        $orderDir = isset($params['ordenDir']) ? $params['ordenDir'] : 'asc';

        $query = $this->getQuery($params);

        $traducciones = $query->with('maker', 'updater')
                                ->join('traducciones_i18n', function($join) use($idioma, $params)
                                {
                                    $join->on('traducciones.id', '=', 'traducciones_i18n.item_id')
                                            ->where('idioma','=',$idioma->codigo_iso_2)
                                            ->where('texto','LIKE','%'. $params['texto'] . '%');
                                })
                                ->orderBy($orderBy, $orderDir)
                                ->skip($limit * ($page - 1))
                                ->take($limit)
                                ->get(array('traducciones.*','traducciones_i18n.texto'));

        $result->items      = $traducciones->all();
        $result->totalItems = $this->totalTraducciones($params);

        return $result;
    }

    /**
    * Devuelve una traduccion según su slug
    * @param $slug string
    * @return object Object con la información de la Traduccion
    */
    public function bySlug($clave)
    {
        return $this->traduccion->with('maker', 'updater')
                    ->where('clave', $clave)
                    ->first();
    }

    /**
    * Crea una nueva Traduccion
    * @param $data array
    * @return boolean
    */
    public function create(array $data)
    {

        //Aquí va la madre del cordero, asi que lo dejamos para cuando tengamos
        //el esqueleto del resto listo

        $datos_comunes  = array();
        $datos_i18n     = array();
        $atributos_traducibles = Traduccion::$atributosTraducibles;

        //Rellenamos los campos comunes
        foreach($data as $k => $v){
                if(in_array($k, $atributos_traducibles))
                {
                    $datos_i18n[$k]     = $v;
                }else{
                    $datos_comunes[$k]  = $v;
                }
            }

        $datos_comunes['clave']= $this->slug($datos_comunes['clave']);
        $item                  = $this->traduccion->create($datos_comunes);
        $datos_i18n['idioma']  = $data['idioma'];
        $datos_i18n['item_id'] = $item->id;

        //creamos el Item y su traduccion
        $item_i18n  = $this->traduccion_i18n->create($datos_i18n);

        if($item && $item_i18n)
        {
            return $item->id;
        }else{
            return FALSE;
        }

    }

    /**
    * Actualiza una traducción
    * @param $data array
    * @return boolean
    */
    public function update(array $data)
    {

        //Idem del metodo anterior
        $traduccion = $this->traduccion->with('updater')->findOrFail($data['id']);

        if(! $traduccion)
        {
            return FALSE;
        }

        $traduccion->actualizado_por   = $data['usuario'];
        $traduccion->clave             = $this->slug($data['clave'], $traduccion->id);
        $traduccion->update();

        return $traduccion->id;
    }

    /**
    * Elimina una traducción
    * @param $id int
    * @return boolean
    */
    public function delete($id)
    {
        //Ojo aquí, hay que diferenciar entre borrar una traducción
        //y borrar el original, lo cual se llevará por delante
        //todas las traducciones

        $itemBorrado = $this->traduccion->findOrFail($id);

        /* Borramos las traducciones asociadas */
        $itemBorrado->first()->traducciones()->delete();

        /* Borramos el item master */
        $itemBorrado->first()->delete();

        return ($itemBorrado === TRUE) ? TRUE : FALSE;
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
            $item = ($checkId !== FALSE) ? $this->traduccion->where('clave', $candidateSlug)
                                                            ->where('id', '!=', $checkId)
                                                            ->first() : $this->traduccion->where('clave', $candidateSlug)->first();

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
    protected function totalTraducciones(array $params)
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
        $query = $this->traduccion->newQuery();
        if(! is_null($params['clave']))
        {
            $query->where('clave', 'LIKE', '%' . $params['clave'] . '%');
        }

        return $query;
    }
}
