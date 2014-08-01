<?php
namespace Ttt\Panel\Repo\Categoria;

class Categoria extends \Baum\Node{

	protected $table = 'categorias';

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
		return new \Ttt\Panel\Repo\Categoria\Extensions\Collection($models);
	}

	/**
	* Reordena una todo el árbol alfabéticamente por el campo nombre
	* @param $parent Ttt\Panel\Repo\Categoria\Categoria
	* @return boolean
	*/

	public function makeTreeOrdered(Categoria $parent)
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

	/**
	* Reordena un árbol completo a partir de una estructura en array
	* @param $parent Ttt\Panel\Repo\Categoria\Categoria
	* @param $childrenArray array
	* @return array
	*/
	public function reorderTreeFrom(Categoria $parent, array $childrenArray)
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
