<?php namespace Ttt\Panel\Service\Form\Modulo;

use Ttt\Panel\Service\Validation\ValidableInterface;
use Ttt\Panel\Repo\Modulo\ModuloInterface;

class ModuloForm {

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
     * @var \Ttt\Repo\Article\ModuloInterface
     */
    protected $modulo;

    public function __construct(ValidableInterface $validator, ModuloInterface $modulo)
    {
        $this->validator = $validator;
        $this->modulo = $modulo;
    }

    /**
     * Create an new modulo
     *
     * @throws \Ttt\Panel\Exception\TttException
     * @return boolean
     */
    public function save(array $input)
    {
        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido crearse el registro');
            return false;
        }

        return $this->modulo->create($input);
    }

    /**
     * Update an existing modulo
     *
     * @throws \Ttt\Exception\TttException
     * @return boolean
     */
    public function update(array $input)
    {
        if( ! $this->valid($input) )
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido actualizarse el registro');
            return false;
        }

        return $this->modulo->update($input);
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
