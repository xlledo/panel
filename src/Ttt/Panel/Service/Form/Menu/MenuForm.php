<?php namespace Ttt\Panel\Service\Form\Menu;

use Ttt\Panel\Service\Validation\ValidableInterface;
use Ttt\Panel\Repo\Menu\MenuInterface;

class MenuForm {

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
    protected $menu;

    public function __construct(ValidableInterface $validator, MenuInterface $menu)
    {
        $this->validator = $validator;
        $this->menu = $menu;
    }

    /**
     * Create a new child
     *
     * @param $input array
     * @param \Ttt\Panel\Repo\Categoria\Categoria $root
     * @throws \Ttt\Panel\Exception\TttException
     * @return \Ttt\Panel\Repo\Categoria\Categoria
     */
    public function createChild(array $input, \Ttt\Panel\Repo\Menu\Menu $root)
    {
        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido crearse la opción de menú. Existen errores de validación.');
            return false;
        }
        return $this->menu->createChild($input, $root);
    }

    /**
     * Update a child
     *
     * @param $input array
     * @param \Ttt\Panel\Repo\Categoria\Categoria $categoria
     * @throws \Ttt\Panel\Exception\TttException
     * @return \Ttt\Panel\Repo\Categoria\Categoria
     */
    public function updateChild(array $input, \Ttt\Panel\Repo\Menu\Menu $menu)
    {
        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido crearse la opción de menú. Existen errores de validación.');
            return false;
        }
        return $this->menu->updateChild($input, $menu);
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
