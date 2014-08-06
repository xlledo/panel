<?php 

namespace Ttt\Panel\Service\Form\Traducciones;

use Ttt\Panel\Service\Validation\ValidableInterface;
use Ttt\Panel\Repo\Traducciones\TraduccionesInterface;


class TraduccionesForm {

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
     * Traducciones repository
     *
     * @var \Ttt\Repo\Traducciones\TraduccionesInterface
     */
    protected $traduccion;

    public function __construct(ValidableInterface $validator, TraduccionesInterface $traduccion)
    {
  
        $this->validator    = $validator;
        $this->traduccion   = $traduccion;
    }

    /**
     * Create an new Traduccion
     *
     * @throws \Ttt\Panel\Exception\TttException
     * @return boolean
     */
    public function save(array $input)
    {
        if(!$this->valid($input))
        {
            throw new \Ttt\Panel\Exception\TttException('No ha podido crearse el registro');
            return false;
        }

        return $this->traduccion->create($input);
    }

    /**
     * Update an existing Traduccion
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

        return $this->traduccion->update($input);
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
