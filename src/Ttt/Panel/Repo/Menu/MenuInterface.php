<?php
namespace Ttt\Panel\Repo\Menu;

interface MenuInterface{

    /**
    * Devuelve un nodo de categorías por su ID
    * @param $id int
    * @return object Object con la información del nodo
    */
    public function byId($id);

    /**
    * Devuelve un nodo RAÍZ de categorías por su ID
    * @param $id int
    * @return object Object con la información del nodo
    */
    public function rootById($id);

    /**
    * Devuelve un nodo no RAÍZ de categorías por su ID
    * @param $id int
    * @return object Object con la información del nodo
    */
    public function childById($id);

    /**
    * Crea un nuevo módulo
    * @param $data array
    * @return boolean
    */
    public function createRoot(array $data);

    /**
    * Actualiza un módulo
    * @param $data array
    * @param  $root \Ttt\Panel\Repo\Categoria\Categoria
    * @return \Ttt\Panel\Repo\Categoria\Categoria
    */
    public function createChild(array $data, \Ttt\Panel\Repo\Menu\Menu $menu);

    /**
    * Actualiza un hijo
    * @param $data array
    * @param  $categoria \Ttt\Panel\Repo\Categoria\Categoria
    * @return boolean
    */
    public function updateChild(array $data, \Ttt\Panel\Repo\Menu\Menu $menu);

    /**
    * Elimina un módulo
    * @param $id int
    * @return boolean
    */
    public function delete($id);

    /**
     * Crea una nueva instancia del modelo. Es útil cuando queremos tener una instancia limpia del objeto.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function createModel();
}
