<?php namespace Ttt\Panel\Service\Form\Idioma;

use Ttt\Panel\Service\Validation\AbstractLaravelValidator;

class IdiomaFormLaravelValidator extends AbstractLaravelValidator {

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array(
        'nombre'          => 'required',
        'codigo_iso_2'    => 'required|max:2',
        'codigo_iso_3'    => 'max:3',
    );

    protected $messages = array(
        'required'          => 'El campo :attribute es obligatorio',
        'max'               => 'El :attribute no puede ser mayor de :max caracteres.'
    );

}
