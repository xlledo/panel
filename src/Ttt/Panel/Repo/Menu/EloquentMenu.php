<?php
namespace Ttt\Panel\Repo\Menu;

use Illuminate\Database\Eloquent\Model;

class EloquentMenu implements MenuInterface{

    protected $menu;

    public function __construct(Model $menu)
    {
        $this->menu = $menu;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\CategoriaInterface
    */
    public function byId($id)
    {
        return $this->menu->find($id);
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
    public function createRoot(array $data)
    {
        //crea el módulo
        $menu = $this->menu->create(array(
            'nombre'          => $data['nombre'],
            'visible'         => $data['visible'],
            'ruta'            => NULL,
            'icono'           => NULL,
            'modulo_id'       => NULL
        ));

        if(! $menu)
        {
            //¿deberíamos lanzar una excepción?
            throw new \Ttt\Panel\Exception\TttException('Error al intentar realizar la inserción en base de datos [' . get_class($this) . ']');
        }

        return $menu;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\Categoria
    */
    public function createChild(array $data, \Ttt\Panel\Repo\Menu\Menu $root)
    {
        //si tenemos modulo_id la ruta ha de salir de él, si no saldrá de lo que se haya indicado
        $ruta = $data['ruta'] == '' ? NULL : $data['ruta'];
        if(! is_null($data['modulo_id']))
        {
            $modulo = \App::make('Ttt\Panel\Repo\Modulo\ModuloInterface')->byId($data['modulo_id']);
            $ruta = $modulo->slug;
        }

        $node = $this->menu->create(
            array(
                'nombre'    => $data['nombre'],
                'ruta'      => $ruta,
                'icono'     => $data['icono'],
                'visible'   => $data['visible']
            )
        )->makeChildOf($root);

        if(! is_null($data['modulo_id']))
        {
            $node->modulo()->associate($modulo);
            $node->update();
        }

        return $node;
    }

    /**
    * @see \Ttt\Panel\Repo\Categoria\Categoria
    */
    public function updateChild(array $data, \Ttt\Panel\Repo\Menu\Menu $menu)
    {

        //si tenemos modulo_id la ruta ha de salir de él, si no saldrá de lo que se haya indicado
        $ruta = $data['ruta'] == '' ? NULL : $data['ruta'];
        if(! is_null($data['modulo_id']))
        {
            $modulo = \App::make('Ttt\Panel\Repo\Modulo\ModuloInterface')->byId($data['modulo_id']);
            $ruta = $modulo->slug;
        }


        $menu->nombre    = $data['nombre'];
        $menu->ruta      = $ruta;
        $menu->visible   = $data['visible'];
        $menu->icono     = $data['icono'];
        if(! is_null($data['modulo_id']))
        {
            $menu->modulo()->associate($modulo);
        }else{
            $menu->modulo()->dissociate();
        }

        $menu->update();

        //return TRUE;
        return $menu;
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
        $class = '\\Ttt\\Panel\\Repo\\Menu\\Menu';

        return new $class;
    }
}
