<?php 

namespace Ttt\Panel\Service\Form\Fichero;

use Ttt\Panel\Service\Validation\AbstractLaravelValidator;

class FicheroFormLaravelValidator extends AbstractLaravelValidator {

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array(
        'nombre'           => 'required|max:255',
//        'fichero'          => 'mimes:jpeg,jpg,png,gif',
//        'fichero_original' => 'required|mimes:jpeg,jpg,png,gif', //Esta en el fichero de configuracion tambien
        'titulo_defecto'        => 'max:255',
        'alt_defecto'           => 'max:255',
        'enlace_defecto'        => 'max:255',
        'descripcion_defecto'   => 'max:255'
        
        
    );

    protected $messages = array(
        'required'          => 'El campo :attribute es obligatorio',
        'max'               => 'El :attribute no puede ser mayor de :max caracteres.',
        'mimes'             => 'Tipo no permitido, compruebe la extensión del fichero'
    );

}
