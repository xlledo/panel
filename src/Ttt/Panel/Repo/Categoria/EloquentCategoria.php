<?php
namespace Ttt\Panel\Repo\Categoria;

use Illuminate\Database\Eloquent\Model;

class EloquentCategoria implements CategoriaInterface{

    protected $categoria;

    public function __construct(Model $categoria)
    {
        $this->categoria = $categoria;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\CategoriaInterface
    */
    public function byId($id)
    {
        return $this->categoria->find($id);
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\CategoriaInterface
    */
    public function rootById($id)
    {
        $element = $this->byId($id);
        if(! $element || ! $element->isRoot())
        {
            throw new \Ttt\Panel\Exception\TttException('El elemento indicado no existe');
        }

        return $element;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\CategoriaInterface
    */
    public function childById($id)
    {
        $element = $this->byId($id);
        if(! $element || $element->isRoot())
        {
            throw new \Ttt\Panel\Exception\TttException('El elemento indicado no existe');
        }

        return $element;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\CategoriaInterface
    */
    public function findAllRootsBy($orderBy = 'nombre', $orderDir = 'asc')
    {

        $roots = $this->categoria->newQuery()
                                        ->whereNull('parent_id')
                                        ->orderBy($orderBy, $orderDir)
                                        ->get();//->all() con el all nos devuelve un array, vaya tela

        return $roots;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\CategoriaInterface
    */
    public function bySlug($slug)
    {

    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\CategoriaInterface
    */
    public function createRoot(array $data)
    {
        //crea el módulo
        $categoria = $this->categoria->create(array(
            'nombre'          => $data['nombre'],
            'protegida'       => $data['protegida'],
            'slug'            => $this->slug($data['nombre']),
            'visible'         => $data['visible'],
            'valor'           => NULL
        ));

        if(! $categoria)
        {
            //¿deberíamos lanzar una excepción?
            throw new \Ttt\Panel\Exception\TttException('Error al intentar realizar la inserción en base de datos [' . get_class($this) . ']');
        }

        return $categoria;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\Categoria
    */
    public function updateRoot(array $data, \Ttt\Panel\Repo\Categoria\Categoria $categoria)
    {

        $categoria->nombre    = $data['nombre'];
        $categoria->slug      = $this->slug($categoria->nombre, $categoria->id);
        $categoria->visible   = $data['visible'];
        $categoria->protegida = $data['protegida'];

        $categoria->update();

        //return TRUE;
        return $categoria;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\Categoria
    */
    public function createChild(array $data, \Ttt\Panel\Repo\Categoria\Categoria $root)
    {
        return $this->categoria->create(
            array(
                'nombre'    => $data['nombre'],
                'slug'      => $this->slug($data['nombre']),
                'valor'     => $data['valor'],
                'visible'   => $data['visible'],
                'protegida' => $data['protegida']
            )
        )->makeChildOf($root);
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\Categoria
    */
    public function updateChild(array $data, \Ttt\Panel\Repo\Categoria\Categoria $categoria)
    {

        $categoria->nombre    = $data['nombre'];
        $categoria->slug      = $this->slug($categoria->nombre, $categoria->id);
        $categoria->visible   = $data['visible'];
        $categoria->valor = $data['valor'];

        $categoria->update();

        //return TRUE;
        return $categoria;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\CategoriaInterface
    */
    public function delete($id)
    {

    }

    /**
     * @see \Ttt\Panel\Repo\Categoria\CategoriaInterface
     */
    public function createModel()
    {
        $class = '\\Ttt\\Panel\\Repo\\Categoriatraducible\\Categoria';

        return new $class;
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
            $item = ($checkId !== FALSE) ? $this->categoria->where('slug', $candidateSlug)
                                                                ->where('id', '!=', $checkId)
                                                                ->first() : $this->categoria->where('slug', $candidateSlug)->first();

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
}
