<?php namespace Ttt\Panel\Service\Form\Categoria;

use Ttt\Panel\Service\Validation\ValidableInterface;
use Ttt\Panel\Repo\Categoria\CategoriaInterface;

class CategoriaForm {

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
    protected $categoria;

    public function __construct(ValidableInterface $validator, CategoriaInterface $categoria)
    {
        $this->validator = $validator;
        $this->categoria = $categoria;
    }

    /**
     * Create a new user
     *
     * @throws \Ttt\Panel\Exception\TttException
     * @return boolean
     */
    public function createRoot(array $input)
    {
        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido crearse el registro. Existen errores de validación.');
            return false;
        }
        return $this->categoria->createRoot($input);
    }

    /**
     * Update an existing user
     * @param $input array
     * @param \Ttt\Panel\Repo\Categoria\Categoria $categoria
     *
     * @throws \Ttt\Exception\TttException
     * @return boolean
     */
    public function updateRoot(array $input, \Ttt\Panel\Repo\Categoria\Categoria $categoria)
    {

        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido actualizarse el registro. Existen errores de validación.');
            return false;
        }

        return $this->categoria->updateRoot($input, $categoria);
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
