<?php
namespace Ttt\Panel\Service\Validation;

use Illuminate\Validation\Factory as Validator;

abstract class AbstractLaravelValidator implements ValidableInterface{

    /*
    * Validator
    *
    * @var Illuminate\Validation\Factory
    */
    protected $validator;

    /*
    * Datos para la validación key => $val array
    *
    * @var Array
    */
    protected $data = array();

    /*
    * Errores de validación
    *
    * @var Array
    */
    protected $errors = array();

    /*
    * Reglas para la validación
    *
    * @var Array
    */
    protected $rules = array();

    /*
    * Mensajes de error personalizados
    *
    * @var Array
    */
    protected $messages = array();

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
    * Añade datos a la validación
    *
    * @param $input array Clave del ítem de caché
    * @return Impl\Service\Validation\ValidableInterface
    */
    public function with(array $input)
    {
        $this->data = $input;

        return $this;
    }

    /**
    * Comprueba si pasa la validación
    *
    * @return bool
    */
    public function passes()
    {
        $validator = $this->validator->make(
            $this->data,
            $this->rules,
            $this->messages
        );

        if($validator->fails())
        {
            $this->errors = $validator->messages();
            return FALSE;
        }

        return TRUE;
    }

    /**
    * Recupera los errores
    *
    * @return array
    */
    public function errors()
    {
        return $this->errors;
    }
}
