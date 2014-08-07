<?php
namespace Ttt\Panel\Repo\Idioma;

interface IdiomaInterface{

    /**
    * Devuelve un módulo por su ID
    * @param $id int
    * @return object Object con la información del módulo
    */
    public function byId($id);

    /**
    * Devuelve el idioma principal
    * @param $id int
    * @return object Object con la información del idioma
    */
    public function idiomaPrincipal();

    /**
    * Devuelve el listado de módulos y el total de los mismos
    * @param $page int
    * @param $limit int
    * @param $params array los parámetros para los filtros y la ordenación
    * @return object Object con Items y totalItems para la paginación
    */
    public function byPage($page = 1, $limit = 10, $params = array());

    /**
    * Crea un nuevo módulo
    * @param $data array
    * @return boolean
    */
    public function create(array $data);

    /**
    * Actualiza un módulo
    * @param $data array
    * @return boolean
    */
    public function update(array $data);

    /**
    * Marca como visible un módulo
    * @param $id int
    * @param $usuario int
    * @return boolean
    */
    public function visible($id);

    /**
    * Marca como NO visible un módulo
    * @param $id int
    * @param $usuario int
    * @return boolean
    */
    public function noVisible($id);

    /**
    * Elimina un módulo
    * @param $id int
    * @return boolean
    */
    public function delete($id);
}
