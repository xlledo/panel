<?php 

namespace Ttt\Panel\Service\Form\Fichero;

use Ttt\Panel\Service\Validation\ValidableInterface;
use Ttt\Panel\Repo\Fichero\FicheroInterface;

class FicheroForm {

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
     * Fichero repository
     *
     * @var \Ttt\Repo\Article\FicheroInterface
     */
    protected $fichero;

    public function __construct(ValidableInterface $validator, FicheroInterface $fichero)
    {
        $this->validator    = $validator;
        $this->fichero      = $fichero;
    }

    /**
     * Create an new fichero
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

        return $this->fichero->create($input);
    }

    /**
     * Update an existing fichero
     *
     * @throws \Ttt\Exception\TttException
     * @return boolean
     */
    public function update(array $input)
    {
        if( ! $this->valid($input))
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido actualizarse el registro');
            return false;
        }

        return $this->fichero->update($input);
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
