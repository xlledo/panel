<?php

namespace Ttt\Panel\Repo\Paginas;

interface PaginasInterface{

    /**
    * Devuelve una Pagina por su ID
    * @param $id int
    * @return object Object con la información del módulo
    */
    public function byId($id);

    /**
    * Devuelve el listado de Paginas y el total de las mismas
    * @param $page int
    * @param $limit int
    * @param $params array los parámetros para los filtros y la ordenación
    * @return object Object con Items y totalItems para la paginación
    */
    public function byPage($page = 1, $limit = 10, $params = array());

    /**
    * Devuelve una página según su slug
    * @param $slug string
    * @return object Object con la información del módulo
    */
    public function bySlug($slug);

    /**
    * Crea una nueva página
    * @param $data array
    * @return boolean
    */
    public function create(array $data);

    /**
    * Actualiza una página
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
    
    
    /**
     * Elimina la asociacion de un fichero con una pagina
     * 
     * @param type $id
     */
    public function desasociarFichero($idFichero, $usuario, $idPagina);
    
}
