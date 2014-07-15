<?php 
namespace Ttt\Panel\Service\Form\Variablesglobales;

use Ttt\Panel\Service\Validation\AbstractLaravelValidator;

class VariablesglobalesFormLaravelValidator extends AbstractLaravelValidator {

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array(
        'clave'          => 'required',
        'valor'          => 'required', 
    );

    protected $messages = array(
        'required'               => 'El campo :attribute es obligatorio'
    );

}
