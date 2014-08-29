<?php 

namespace Ttt\Panel\Service\Form\Paginas;

use Ttt\Panel\Service\Validation\ValidableInterface;
use Ttt\Panel\Repo\Paginas\PaginasInterface;

class PaginasForm {

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
     * Paginas repository
     *
     * @var \Ttt\Repo\Paginas\PaginasInterface
     */
    protected $pagina;

    public function __construct(ValidableInterface $validator, PaginasInterface $pagina)
    {
  
        $this->validator    = $validator;
        $this->pagina       = $pagina;
    }

    /**
     * Create an new Pagina
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

        
        return $this->pagina->create($input);
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

        return $this->pagina->update($input);
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
