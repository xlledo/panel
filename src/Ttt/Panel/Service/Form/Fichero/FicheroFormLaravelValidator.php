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
        'nombre'           => 'required',
        'fichero'          => 'required|mimes:jpeg,jpg,png,gif',
        'fichero_original' => 'required|mimes:jpeg,jpg,png,gif', //Esta en el fichero de configuracion tambien
    );

    protected $messages = array(
        'required'          => 'El campo :attribute es obligatorio',
        'max'               => 'El :attribute no puede ser mayor de :max caracteres.'
    );

}
