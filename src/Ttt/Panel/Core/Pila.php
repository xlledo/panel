<?php
namespace Ttt\Panel\Core;


class Pila {

	protected $pilaName = 'sess_pila';

	protected $stack = array();

	public function __construct()
	{
		$this->stack = \Session::has($this->pilaName) ? \Session::get($this->pilaName) : array();
	}

	/**
	* Inicializa la Pila poniendo solamente la página de Inicio
	*/
	public function reset()
	{
		$this->clean()
					->push(
						array(
							'titulo'          => 'Inicio',
							'url'             => url('admin/dashboard'),
							'eloquent'        => NULL,
							'eloquentMethod'  => NULL,
							'retrievingValue' => NULL,
							'reference'       => FALSE,
							'pestania'        => FALSE
						)
					);

		return $this;
	}

	/**
	* Limpia la Pila totalmente
	*/
	public function clean()
	{
		$this->stack = array();
		return $this;
	}

	/**
	* Elimina el último elemento de la Pila
	*/
	public function pop()
	{
		if(count($this->stack))
		{
			array_pop($this->stack);
		}
		return $this;
	}

	/**
	* Inserta un nuevo elemento en la Pila
	*/
	public function push($element)
	{
		array_push($this->stack, $element);
		return $this;
	}

	public function guessStructure(
		$controlador,
		$metodo,
		$config,
		$parametros,
		$moduleSlug)
	{

		if($this->isFileRelatedCall($metodo))
		{
			//el caso de los ficheros relacionados es especial
			$this->popToReference();
			$ultimaReferencia     = $this->getUltimaReferencia(TRUE);
			$ultimaReferenciaPila = $this->getUltimaReferencia();
			if($ultimaReferencia === FALSE)
			{
				//no ha accedido de manera correcta, que es desde el ver de un elemento
				throw new \Ttt\Panel\Exception\TttException('No existe referencia en la Pila, para poder acceder ha de pasar por una vista de edición');
			}

			if($metodo === 'nuevoFichero')
			{
				$this->push(
					array(
						'titulo'          => 'Ficheros',
						'url'             => $ultimaReferenciaPila['url'] . '#ficheros',
						'eloquent'        => NULL,
						'eloquentMethod'  => NULL,
						'retrievingField' => NULL,
						'retrievingValue' => NULL,
						'reference'       => FALSE,
						'pestania'        => 'ficheros'
					)
				)->push(
					array(
						'titulo'          => 'Nuevo Fichero',
						'url'             => action($controlador . '@nuevoFichero'),
						'eloquent'        => NULL,
						'eloquentMethod'  => NULL,
						'retrievingField' => NULL,
						'retrievingValue' => NULL,
						'reference'       => FALSE,
						'pestania'        => FALSE
					)
				);
			}else{
				//solo puede ser verFichero
				//$ficheroRelacionado = $ultimaReferencia->ficheros()->find($parametros['id']);//recuperamos el fichero relacionado de la tabla pivote para su referencia
				$ficheroRelacionado = $ultimaReferencia->ficheros()
						->where($moduleSlug . '_ficheros.id', $parametros['id'])
						->get()->first();//recuperamos el fichero relacionado de la tabla pivote para su referencia
				$this->push(
					array(
						'titulo'          => 'Ficheros',
						'url'             => $ultimaReferenciaPila['url'] . '#ficheros',
						'eloquent'        => NULL,
						'eloquentMethod'  => NULL,
						'retrievingField' => NULL,
						'retrievingValue' => NULL,
						'reference'       => FALSE,
						'pestania'        => 'ficheros'
					)
				)->push(
					array(
						'titulo'          => $ficheroRelacionado->nombre,
						'url'             => $ultimaReferencia->url . '#ficheros',
						'eloquent'        => NULL,
						'eloquentMethod'  => NULL,
						'retrievingField' => NULL,
						'retrievingValue' => NULL,
						'reference'       => FALSE,
						'pestania'        => 'ficheros'
					)
				);
			}
		}else{

			//aquí tenemos la rave montada
			if($metodo === 'index')
			{
				$this->reset()->push(
					array(
						'titulo'          => $config['tituloCanonico'],
						'url'             => action($controlador . '@' . $metodo),
						'eloquent'        => NULL,
						'eloquentMethod'  => NULL,
						'retrievingField' => NULL,
						'retrievingValue' => NULL,
						'reference'       => FALSE,
						'pestania'        => FALSE
					)
				);
			}else{
				//es nuevo, ver o referenciaPestaña
				//SI no tenemos dirección, que indica navegación por Miga (reference == null || ! reference || (referencia !== forwardData || referencia !== backwardData))
				if(! \Input::has('direction') || ! in_array(\Input::get('direction'), array('forward', 'backward', 'keep')))
				{
					//Limpiamos la pila dejando solo Dashboard y añadimos la url con el título del módulo para el método index
					$this->reset()->push(
						array(
							'titulo'          => $config['tituloCanonico'],
							'url'             => action($controlador . '@index'),
							'eloquent'        => NULL,
							'eloquentMethod'  => NULL,
							'retrievingField' => NULL,
							'retrievingValue' => NULL,
							'reference'       => FALSE,
							'pestania'        => FALSE
						)
					);
					if($metodo === 'nuevo')
					{
						$this->push(
							array(
								'titulo'          => 'Nuevo',
								'url'             => action($controlador . '@nuevo'),
								'eloquent'        => NULL,
								'eloquentMethod'  => NULL,
								'retrievingField' => NULL,
								'retrievingValue' => NULL,
								'reference'       => FALSE,
								'pestania'        => FALSE
							)
						);
					}else{

						$element = $this->getElement($parametros, $config);
						//solo puede ser Ver

						$this->push(
							array(
								'titulo'          => $element['titulo'],
								'url'             => action($controlador . '@ver', $parametros[$config['param']]) . '?direction=backward',
								'eloquent'        => $config['eloquent'],
								'eloquentMethod'  => $config['metodo'],
								'retrievingField' => $config['param'],
								'retrievingValue' => $element['retrievingValue'],
								'reference'       => TRUE,
								'pestania'        => FALSE
							)
						);
					}
				}else{
					//tenemos dirección de navegación y es forward o backward
					$direction = \Input::get('direction');
					if($direction === 'forward')
					{
						//por narices ha de tener una referencia
						$ultimaReferencia = $this->getUltimaReferencia();
						if($ultimaReferencia === FALSE)
						{
							//no ha accedido de manera correcta, que es desde el ver de un elemento
							throw new \Ttt\Panel\Exception\TttException('No existe referencia en la Pila, para poder acceder ha de pasar por una vista de edición');
						}
						if(preg_match('/^referencia(.+)$/', $metodo, $matches))
						{
							//Apilamos la URL para la Pestaña, cuyo nombre se extrae del nombre del método quitándole la cadena referencia y haciendo un strtolower.
							//En la pestaña indicamos los datos de la propia referencia para recuperarlos a continuación.
							//Marcamos este elemento de la Pila como referencia = TRUE

							$pestania = strtolower(str_replace('referencia', '', $metodo));
							$tituloElm = ucfirst($pestania);

							//el título canónico ha de cogerse de la configuración
							if(array_key_exists($pestania, \Config::get('panel::pila', array())))
							{
								$tituloElm = \Config::get('panel::pila')[$pestania]['tituloCanonico'];
							}

							$this->push(
								array(
									'titulo'          => $tituloElm,
									'url'             => action($controlador . '@ver', $ultimaReferencia['retrievingValue']) . '?direction=backward' . '#' . $pestania,
									'eloquent'        => $ultimaReferencia['eloquent'],
									'eloquentMethod'  => $ultimaReferencia['eloquentMethod'],
									'retrievingField' => $ultimaReferencia['retrievingField'],
									'retrievingValue' => $ultimaReferencia['retrievingValue'],
									'reference'       => TRUE,
									'pestania'        => $pestania
								)
							);

						}else{
							//nuevo o ver
							$this->popToReference();
							if($metodo === 'nuevo')
							{
								$this->push(
									array(
										'titulo'          => 'Nuevo',
										'url'             => action($controlador . '@nuevo'),
										'eloquent'        => NULL,
										'eloquentMethod'  => NULL,
										'retrievingField' => NULL,
										'retrievingValue' => NULL,
										'reference'       => FALSE,
										'pestania'        => FALSE
									)
								);
							}else{
								//es ver
								//solo puede ser Ver
								$element = $this->getElement($parametros, $config);

								$this->push(
									array(
										'titulo'          => $element['titulo'],
										'url'             => action($controlador . '@ver', $parametros[$config['param']]) . '?direction=backward',
										'eloquent'        => $config['eloquent'],
										'eloquentMethod'  => $config['metodo'],
										'retrievingField' => $config['param'],
										'retrievingValue' => $element['retrievingValue'],
										'reference'       => TRUE,
										'pestania'        => FALSE
									)
								);
							}

						}
					}elseif($direction === 'backward'){
						//es del tipo backward
						//solo contemplaremos el ver, ya que si es el Nuevo no queremos saber nada
						if($metodo === 'ver')
						{
							//se deben eliminar todos los elementos de la pila hasta que lleguemos al que estamos editando

							$element = $this->getElement($parametros, $config);

							$newElm = array(
								'titulo'          => $element['titulo'],
								'url'             => action($controlador . '@ver', $parametros[$config['param']]) . '?direction=backward',
								'eloquent'        => $config['eloquent'],
								'eloquentMethod'  => $config['metodo'],
								'retrievingField' => $config['param'],
								'retrievingValue' => $element['retrievingValue'],
								'reference'       => TRUE,
								'pestania'        => FALSE
							);

							$this->popToElement($newElm);
						}
					}
					//el tipo keep no hace nada, mantiene la Pila tal cual
				}
			}
		}

		return $this;
	}

	function popToElement($element)
	{
		$reversedStack = array_reverse($this->stack);
		foreach($reversedStack as $key => $rs)
		{
			if($rs['titulo'] === 'Inicio')
			{
				break;
			}
			$keys = array_keys($rs);

			$totalKeys    = count($keys);
			$totalMatches = 1;
			foreach($keys as $k)
			{
				if($element[$k] === $rs[$k])
				{
					$totalMatches ++;
				}
			}

			if($totalMatches === $totalKeys)
			{
				//se ha encontrado el elemento
				break;
			}else{
				unset($reversedStack[$key]);
			}
		}

		$this->stack = array_reverse($reversedStack);

		return $this->stack;
	}

	public function popToReference()
	{
		$reversedStack = array_reverse($this->stack);
		foreach($reversedStack as $key => $rs)
		{
			if($rs['reference'] || $rs['titulo'] == 'Inicio')
			{
				break;
			}

			unset($reversedStack[$key]);
		}

		$this->stack = array_reverse($reversedStack);

		return $this->stack;
	}

	public function getUltimaReferencia($asModel = FALSE)
	{
		$reversedStack = array_reverse($this->stack);
		foreach($reversedStack as $rs)
		{
			if($rs['reference'])
			{
				return $asModel ? $this->getModelObject($rs) : $rs;
			}
		}

		return FALSE;
	}

	public function getPenultimaReferencia($asModel = FALSE)
	{
		$reversedStack = array_reverse($this->stack);
		$encontradas = 0;
		foreach($reversedStack as $rs)
		{
			if($rs['reference'])
			{
				if($encontradas == 1)
				{
					return $asModel ? $this->getModelObject($rs) : $rs;
				}
				$encontradas ++;
			}
		}

		return FALSE;
	}

	protected function getModelObject(array $referencia)
	{
		$obj = \App::make($referencia['eloquent'])->{$referencia['eloquentMethod']}($referencia['retrievingValue']);//Model|NULL
		if(! $obj)
		{
			throw new \Ttt\Panel\Exception\TttException('No se ha podido recuperar el objeto desde la referencia [' . $referencia['titulo'] . ']');
		}

		return $obj;
	}

	public function getPila()
	{
		return $this->stack;
	}

	public function store()
	{
		\Session::put($this->pilaName, $this->stack);
	}

	public function render()
	{
		$html = '';
		$iterator = 0;
		foreach($this->stack as $st)
		{
			$html .= '<li>';
			if($st['titulo'] === 'Inicio')
			{
				$html .= '<i class="icon-home home-icon"></i>';
			}
			if($iterator < (count($this->stack) - 1))
			{
				$html .= '<a href="' . $st['url'] . '" title="' . $st['titulo'] . '">' . $st['titulo'] . '</a>';
			}else{
				$html .= $st['titulo'];
			}

			$html .= '</li>';

			$iterator ++;
		}

		return $html;
	}

	protected function getElement($parametros, $config)
	{
		$element = \App::make($config['eloquent'])->{$config['metodo']}($parametros[$config['param']]);

		$titulo = '';
		foreach($config['readFields'] as $rf)
		{
			//@TODO: Fix for property exists
			$titulo .= $element->{$rf} . ' ';
		}

		return array(
			'titulo'          => rtrim($titulo),
			'retrievingValue' => $element->{$config['param']}
		);
	}

	protected function isFileRelatedCall($metodo)
	{
		return in_array($metodo, array('verFichero', 'nuevoFichero'));
	}
}
