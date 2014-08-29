<?php 

namespace Ttt\Panel\Service\Form\Paginas;

use Ttt\Panel\Service\Validation\AbstractLaravelValidator;

class PaginasFormLaravelValidator extends AbstractLaravelValidator {

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array(
        'titulo'            => 'required',
        'texto'             => 'required',
        'idioma'            => 'required'
    );

    protected $messages = array(
        'required'               => 'El campo :attribute es obligatorio'
    );
}
