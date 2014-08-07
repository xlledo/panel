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
        'nombre'          => 'required',
        
    );

    protected $messages = array(
        'required'          => 'El campo :attribute es obligatorio',
        'max'               => 'El :attribute no puede ser mayor de :max caracteres.'
    );

}
