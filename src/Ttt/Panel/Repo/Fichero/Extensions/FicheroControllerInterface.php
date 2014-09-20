<?php

namespace Ttt\Panel\Repo\Fichero\Extensions;

interface FicheroControllerInterface {
    
    /* Función para guardar campos específicos de las relaciones */
    function guardarCamposEspecificos($pivotId, $ficheroId);
    
    /* Validación de los campos específicos de las relaciones*/
    function validarCamposEspecificos();
    
    /* Obtener campos especificos */
    function obtenerCamposEspecificos($ficheroId, $itemId, $pivotId, $enviarAVista);
    
}