<?php 

namespace Ttt\Panel\Service\Form\Variablesglobales;

use Ttt\Panel\Service\Validation\ValidableInterface;
use Ttt\Panel\Repo\Variablesglobales\VariablesglobalesInterface;


class VariablesglobalesForm {

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
     * Variableglobale repository
     *
     * @var \Ttt\Repo\Variablesglobales\VariablesglobalesInterface
     */
    protected $variablesglobale;

    public function __construct(ValidableInterface $validator, VariablesglobalesInterface $variablesglobale)
    {
        $this->validator = $validator;
        $this->variablesglobale = $variablesglobale;
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

        return $this->variablesglobale->create($input);
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

        return $this->variablesglobale->update($input);
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
