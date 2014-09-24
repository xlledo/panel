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
            throw new \Ttt\Panel\Exception\TttException('No ha podido crearse el registro. No se han podido guardar los cambios. Por favor revise los campos marcados..');
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
            throw new \Ttt\Panel\Exception\TttException('No ha podido actualizarse el registro. No se han podido guardar los cambios. Por favor revise los campos marcados..');
            return false;
        }

        return $this->categoria->updateRoot($input, $categoria);
    }

    /**
     * Create a new child
     *
     * @param $input array
     * @param \Ttt\Panel\Repo\Categoria\Categoria $root
     * @throws \Ttt\Panel\Exception\TttException
     * @return \Ttt\Panel\Repo\Categoria\Categoria
     */
    public function createChild(array $input, \Ttt\Panel\Repo\Categoria\Categoria $root)
    {
        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido crearse la categoría. No se han podido guardar los cambios. Por favor revise los campos marcados..');
            return false;
        }
        return $this->categoria->createChild($input, $root);
    }

    /**
     * Update a child
     *
     * @param $input array
     * @param \Ttt\Panel\Repo\Categoria\Categoria $categoria
     * @throws \Ttt\Panel\Exception\TttException
     * @return \Ttt\Panel\Repo\Categoria\Categoria
     */
    public function updateChild(array $input, \Ttt\Panel\Repo\Categoria\Categoria $categoria)
    {
        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido crearse la categoría. No se han podido guardar los cambios. Por favor revise los campos marcados..');
            return false;
        }
        return $this->categoria->updateChild($input, $categoria);
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
