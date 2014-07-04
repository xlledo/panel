<?php namespace Ttt\Panel\Service\Form\Modulo;

use Ttt\Panel\Service\Validation\AbstractLaravelValidator;

class ModuloFormLaravelValidator extends AbstractLaravelValidator {

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array(
        'nombre'          => 'required',
        'usuario'         => 'required|exists:users,id', // Assumes db connection
    );

    protected $messages = array(
        'usuario.exists'      => 'Este usuario no existe',
        'required'               => 'El campo :attribute es obligatorio'
    );

}
