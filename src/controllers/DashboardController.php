<?php
namespace Ttt\Panel;

use \View;
use Ttt\Panel\Core\PanelController;

class DashboardController extends PanelController{

	protected $_views_dir = 'inicio';

	public function index()
	{
		View::share('title', 'CRM FacePhi');
		return View::make('panel::' . $this->_views_dir . '.dashboard');
	}
}
