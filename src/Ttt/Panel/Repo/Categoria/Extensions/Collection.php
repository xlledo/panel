<?php
namespace Ttt\Panel\Repo\Categoria\Extensions;

class Collection extends \Baum\Extensions\Eloquent\Collection{

	public function toHierarchyForceOrder($forcedOrder = null) {
		$dict = $this->getDictionary();

		// Enforce sorting by $orderColumn setting in Baum\Node instance
		uasort($dict, function($a, $b) use($forcedOrder){
			$a->setOrderColumn($forcedOrder);
			$b->setOrderColumn($forcedOrder);
			return ($a->getOrder() >= $b->getOrder()) ? 1 : -1;
		});

		return new \Ttt\Panel\Repo\Categoria\Extensions\Collection($this->hierarchical($dict));
	}
}
