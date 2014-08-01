<?php

namespace Ttt\Panel\Repo\Traducciones;

interface TraduccionesInterface{

    /**
    * Devuelve una Traduccion por su ID
    * @param $id int
    * @return object Object con la información del módulo
    */
    public function byId($id);

    /**
    * Devuelve el listado de Traducciones y el total de las mismas
    * @param $page int
    * @param $limit int
    * @param $params array los parámetros para los filtros y la ordenación
    * @return object Object con Items y totalItems para la paginación
    */
    public function byPage($page = 1, $limit = 10, $params = array());

    /**
    * Devuelve una traduccion según su slug
    * @param $slug string
    * @return object Object con la información del módulo
    */
    public function bySlug($slug);

    /**
    * Crea una nueva traduccion
    * @param $data array
    * @return boolean
    */
    public function create(array $data);

    /**
    * Actualiza una traduccion
    * @param $data array
    * @return boolean
    */
    public function update(array $data);

    /**
    * Elimina un módulo
    * @param $id int
    * @return boolean
    */
    public function delete($id);
}
