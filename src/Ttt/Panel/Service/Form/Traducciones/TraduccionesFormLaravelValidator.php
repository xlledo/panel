<?php 

namespace Ttt\Panel\Service\Form\Traducciones;

use Ttt\Panel\Service\Validation\AbstractLaravelValidator;

class TraduccionesFormLaravelValidator extends AbstractLaravelValidator {

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array(
        'clave'          => 'required',
        'texto'          => 'required',
        'idioma'         => 'required'
      
    );

    protected $messages = array(
        'required'               => 'El campo :attribute es obligatorio'
    );

}
