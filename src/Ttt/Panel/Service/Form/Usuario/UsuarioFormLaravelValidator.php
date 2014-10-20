<?php namespace Ttt\Panel\Service\Form\Usuario;

use Ttt\Panel\Service\Validation\AbstractLaravelValidator;

class UsuarioFormLaravelValidator extends AbstractLaravelValidator {

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array(
        'first_name'         => 'required',
        'last_name'          => 'required',
        'email'              => 'required|email|existe',
        'password'           => 'required',
        'confirm_password'   => 'required|same:password'
    );

    protected $messages = array(
        'confirm_password.same'      => 'El campo repetir contraseña no concuerda con el campo contraseña',
        'first_name.required'        => 'El campo nombre es obligatorio',
        'last_name.required'         => 'El campo apellidos es obligatorio',
        'password.required'          => 'El campo contraseña es obligatorio',
        'confirm_password.required'  => 'El campo repetir contraseña es obligatorio',
        'required'                   => 'El campo :attribute es obligatorio',
        'email'                      => 'El campo :attribute ha de ser una dirección de E-mail válida',
        'email.existe'           => 'Ya existe un usuario con ese :attribute',
    );

}
