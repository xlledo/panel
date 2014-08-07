<?php
namespace Ttt\Panel\Repo\Fichero;

interface FicheroInterface{

    /**
    * Devuelve un fichero por su ID
    * @param $id int
    * @return object Object con la información del fichero
    */
    public function byId($id);

    /**
    * Devuelve el listado de ficheros y el total de los mismos
    * @param $page int
    * @param $limit int
    * @param $params array los parámetros para los filtros y la ordenación
    * @return object Object con Items y totalItems para la paginación
    */
    public function byPage($page = 1, $limit = 10, $params = array());

    /**
    * Crea un nuevo fichero
    * @param $data array
    * @return boolean
    */
    public function create(array $data);

    /**
    * Actualiza un fichero
    * @param $data array
    * @return boolean
    */
    public function update(array $data);

    /**
    * Marca como visible un fichero
    * @param $id int
    * @param $usuario int
    * @return boolean
    */
    public function visible($id);

    /**
    * Marca como NO visible un fichero
    * @param $id int
    * @param $usuario int
    * @return boolean
    */
    public function noVisible($id);

    /**
    * Elimina un fichero
    * @param $id int
    * @return boolean
    */
    public function delete($id);
}
