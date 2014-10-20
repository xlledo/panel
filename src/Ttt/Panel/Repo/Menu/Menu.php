<?php
namespace Ttt\Panel\Repo\Menu;

class Menu extends \Ttt\Panel\Core\Database\Extensions\LogableNestableModel{

	public $paramsForLog = array('nombre');

	protected $table = 'menu';

	// 'parent_id' column name
	protected $parentColumn = 'parent_id';

	// 'lft' column name
	protected $leftColumn = 'lft';

	// 'rgt' column name
	protected $rightColumn = 'rgt';

	// 'depth' column name
	protected $depthColumn = 'depth';

	// guard attributes from mass-assignment
	//protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');
	protected $guarded = array('lft', 'rgt', 'parent_id', 'depth');

	/**
	* Permite reestablecer el orden de ordenación de la columna
	* @param string $column
	* @return \Ttt\Panel\Repo\Categoria\Categoria
	*/
	public function setOrderColumn($column)
	{
		$this->orderColumn = $column;

		return $this;
	}

	/**
	* Overload new Collection
	*
	* @param array $models
	* @return \Ttt\Panel\Repo\Categoria\Extensions\Collection
	*/
	public function newCollection(array $models = array()) {
		return new \Ttt\Panel\Repo\Menu\Extensions\Collection($models);
	}

	public function makeTreeOrdered()
	{
		$childrenOrdered         = $this->getImmediateDescendants()->toHierarchyForceOrder('nombre');

		/*echo '<pre>';
		print_r($childrenOrdered);
		echo '<pre>';*/

		$previousNode = NULL;
		$firstChildren = $this->getDescendants()->first();

		try{

			foreach($childrenOrdered as $chld)
			{
				$nodeToMove = $this->getDescendants()->getDictionary()[$chld->id];
				/*echo '<pre>';
				print_r($nodeToMove->toArray());
				echo '<pre>';*/
				//se trata de el primer elemento
				if(is_null($previousNode))
				{
					if($firstChildren)
					{
						if($firstChildren->id != $nodeToMove->id)
						{
							//solo ha de moverse si no se trata del mismo nodo
							//echo 'Movemos el nodo ' . $nodeToMove->nombre . ' a la izquierda de ' . $firstChildren->nombre . '<br />';
							$nodeToMove = $nodeToMove->moveToLeftOf($firstChildren);
						}
					}else{
						//echo 'Hacemos al nodo ' . $nodeToMove->nombre . ' primer hijo de ' . $this->nombre . '<br />';
						$nodeToMove = $nodeToMove->makeFirstChildOf($this);
					}
				}else{
					//lo movemos si el nodo anterior no es este mismo, si no saltará una excepción
					if($nodeToMove->id != $previousNode->id)
					{
						//echo 'Movemos el nodo ' . $nodeToMove->nombre . ' a la derecha de ' . $previousNode->nombre . '<br />';
						$nodeToMove = $nodeToMove->moveToRightOf($previousNode);
					}
				}
				//si este nodo tiene hijos llamamos recursivamente a este método
				if($chld->getImmediateDescendants()->count())
				{
					//echo 'Llamada recursiva<br />';
					$nodeToMove->makeTreeOrdered();
				}
				/*echo '<pre>';
				print_r($nodeToMove->toArray());
				echo '<pre>';*/
				$previousNode = $nodeToMove;
			}
		}catch(\RuntimeException $e){
				echo $e->getMessage();exit;
		}

		return TRUE;
	}

	public function reorderTreeFrom(Menu $parent, array $childrenArray)
	{
		$previousNode = null;
		foreach($childrenArray as $chld)
		{
			$nodeToMove = $this->find($chld['id']);
			//se trata del primer nodo
			if(is_null($previousNode))
			{
				$firstChildren = $parent->getDescendants()->first();
				if($firstChildren)
				{
					if($firstChildren->id != $nodeToMove->id)
					{
						//solo ha de moverse si no se trata del mismo nodo
						$nodeToMove->moveToLeftOf($firstChildren);
					}
				}else{
					$nodeToMove->makeFirstChildOf($parent);
				}
			}else{
				//lo movemos si el nodo anterior no es este mismo, si no saltará una excepción
				if($nodeToMove->id != $previousNode->id)
				{
					$nodeToMove->moveToRightOf($previousNode);
				}
			}

			//si este nodo tiene hijos llamamos recursivamente a este método
			if(isset($chld['children']) && count($chld['children']))
			{
				$this->reorderTreeFrom($nodeToMove, $chld['children']);
			}

			$previousNode = $nodeToMove;
		}

		return TRUE;
	}

	/**
	* Elimina todo el contenido a partir de un padre
	* @return void
	*/
	public function deleteTree()
	{
		foreach($this->children as $chld)
		{
			$chld->delete();
		}
	}

	public function modulo()
	{
		return $this->belongsTo('\Ttt\Panel\Repo\Modulo\Modulo', 'modulo_id', 'id');
	}
}
