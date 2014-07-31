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
	* Reordena una colección de hijos para un padre
	* @param $parent Ttt\Panel\Repo\Categoria\Categoria
	* @param $children \Ttt\Panel\Repo\Categoria\Extensions\Collection
	*/
	public function saveTreeFrom(\Ttt\Panel\Repo\Categoria\Categoria $parent, \Ttt\Panel\Repo\Categoria\Extensions\Collection $children)
	{
		$childrenArray = $children->toArray();
		foreach($children as $chld)
		{
			$chld->delete();
		}
		return $parent->makeTree($childrenArray);
	}
}
