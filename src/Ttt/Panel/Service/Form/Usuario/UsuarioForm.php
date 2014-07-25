<?php namespace Ttt\Panel\Service\Form\Usuario;

use Ttt\Panel\Service\Validation\ValidableInterface;
use Ttt\Panel\Repo\Usuario\UsuarioInterface;

class UsuarioForm {

    /**
     * Form Data
     *
     * @var array
     */
    protected $data;

    /**
     * Validator
     *
     * @var \Ttt\Panel\Service\Validation\ValidableInterface
     */
    protected $validator;

    /**
     * Modulo repository
     *
     * @var \Ttt\Repo\Usuario\UsuarioInterface
     */
    protected $usuario;

    public function __construct(ValidableInterface $validator, UsuarioInterface $usuario)
    {
        $this->validator = $validator;
        $this->usuario = $usuario;
    }

    /**
     * Create a new user
     *
     * @throws \Ttt\Panel\Exception\TttException
     * @return boolean
     */
    public function create(array $input)
    {
        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido crearse el registro');
            return false;
        }
        unset($input['confirm_password']);
        return $this->usuario->create($input);
    }

    /**
     * Update an existing user
     * @param $input array
     * @param \Cartalyst\Sentry\Users\UserInterface $user
     *
     * @throws \Ttt\Exception\TttException
     * @return boolean
     */
    public function update(array $input, \Cartalyst\Sentry\Users\UserInterface &$user)
    {
        //reestablecemos las reglas para el password, ya que en la edición no ha de ser obligatorio
        $this->validator->setRuleForKey('password', '');
        if($input['password'] == '')
        {
            $this->validator->setRuleForKey('confirm_password', '');
        }

        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido actualizarse el registro');
            return false;
        }

        //establecemos las nuevas propiedades
        $user->first_name = $input['first_name'];
        $user->last_name  = $input['last_name'];
        $user->email      = $input['email'];
        if($input['password'] != '')
        {
            $user->password = $input['password'];
        }

        return $this->usuario->update($user);
    }

    /**
     * Return any validation errors
     *
     * @return array
     */
    public function errors()
    {
        return $this->validator->errors();
    }

    /**
     * Test if form validator passes
     *
     * @return boolean
     */
    protected function valid(array $input)
    {
        return $this->validator->with($input)->passes();
    }
}
