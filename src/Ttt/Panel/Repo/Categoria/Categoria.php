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
	protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');
}
