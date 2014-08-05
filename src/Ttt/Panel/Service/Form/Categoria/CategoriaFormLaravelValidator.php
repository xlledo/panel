<?php namespace Ttt\Panel\Service\Form\Categoria;

use Ttt\Panel\Service\Validation\AbstractLaravelValidator;

class CategoriaFormLaravelValidator extends AbstractLaravelValidator {

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array(
        'nombre'             => 'required'
    );

    protected $messages = array(
        'required'                   => 'El campo :attribute es obligatorio'
    );

}
