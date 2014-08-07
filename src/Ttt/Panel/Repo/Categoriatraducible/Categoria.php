<?php
namespace Ttt\Panel\Repo\Categoriatraducible;

class Categoria extends \Ttt\Panel\Core\Database\Extensions\TranslatableNestableModel{

	protected $table = 'categorias_traducibles';

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
	* Devuelve todas las traducciones de un item
	*
	* @return Traduccion_i18n
	*/

	public function traducciones()
	{
			return $this->hasMany('Ttt\Panel\Repo\Categoriatraducible\CategoriaI18n', 'item_id');
	}

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
		return new \Ttt\Panel\Repo\Categoriatraducible\Extensions\Collection($models);
	}

	/**
	* Reordena una todo el árbol alfabéticamente por el campo nombre
	* @param $parent Ttt\Panel\Repo\Categoria\Categoria
	* @return boolean
	*/

	public function __makeTreeOrdered(Categoria $parent)
	{

		$childrenOrdered         = $parent->getDescendants()->toHierarchyForceOrder('nombre');
		$childrenOrderedArray    = $childrenOrdered->toArray();

		$previousNode = NULL;

		$iterator = 0;
		foreach($childrenOrdered as $chld)
		{
			$chld->delete();
		}

		$parent->makeTree($childrenOrderedArray);

		return TRUE;
	}

	public function makeTreeOrdered(Categoria $parent)
	{
		$childrenOrdered         = $parent->getDescendants()->toHierarchyForceOrder('nombre');

		$previousNode = NULL;

		foreach($childrenOrdered as $chld)
		{
			$nodeToMove = $parent->getDescendants()->getDictionary()[$chld->id];
			//se trata de el primer elemento
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
			if($chld->children->count())
			{
				$this->makeTreeOrdered($chld);
			}

			$previousNode = $nodeToMove;
		}

		return TRUE;
	}

	/**
	* Reordena un árbol completo a partir de una estructura en array
	* @param $parent Ttt\Panel\Repo\Categoria\Categoria
	* @param $childrenArray array
	* @return array
	*/
	public function __reorderTreeFrom(Categoria $parent, array $childrenArray)
	{
		$parentCollection = $this->newCollection();

		$pushTo = array();

		foreach($childrenArray as $chld)
		{
			$node = $this->find($chld['id']);
			$nodeArray = $node->toArray();
			if(isset($chld['children']) && count($chld['children']))
			{
				$nodeArray['children'] = $this->reorderTreeFrom($node, $chld['children']);
			}
			$pushTo[] = $nodeArray;
		}

		return $pushTo;
	}

	public function reorderTreeFrom(Categoria $parent, array $childrenArray)
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
}
