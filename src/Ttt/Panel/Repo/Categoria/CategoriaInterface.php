<?php
namespace Ttt\Panel\Repo\Categoria;

interface CategoriaInterface{

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
    * Devuelve el listado de raíces
    * @param $orderBy el campo por el que ordenar
    * @param $orderDir dirección de ordenación
    * @return object Collection con el listado de raíces de categorías
    */
    public function findAllRootsBy($orderBy = 'nombre', $orderDir = 'asc');

    /**
    * Devuelve un módulo según su slug
    * @param $slug string
    * @return object Object con la información del módulo
    */
    public function bySlug($slug);

    /**
    * Crea un nuevo módulo
    * @param $data array
    * @return boolean
    */
    public function createRoot(array $data);

    /**
    * Actualiza un módulo
    * @param $data array
    * @param  $categoria \Ttt\Panel\Repo\Categoria\Categoria
    * @return boolean
    */
    public function updateRoot(array $data, \Ttt\Panel\Repo\Categoria\Categoria $categoria);

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
