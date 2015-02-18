<?php
namespace Ttt\Panel;

use \View;
use Ttt\Panel\Core\PanelController;

class DashboardController extends PanelController{

	protected $_views_dir = 'inicio';

	public function index()
	{
                //-- Preparación de items para la portada
                $dasboardItems = \Config::get('dashboard.items',array());
                $dasboardResultData = array();
                
                foreach ($dasboardItems as $key => $item)
                {
                    $dasboardResultData[$key] = call_user_func_array($item['eloquent'] . '::' . $item['method'], $item['defaultArguments']);
                }
                
                \View::share('dashboardItems', $dasboardItems);
                \View::share('dashboardItemsResult', $dasboardResultData);
                
		View::share('title', \Config::get('panel::app.dashboardTitle', 'Tres Tristes Tigres'));
		return View::make('panel::' . $this->_views_dir . '.dashboard');
	}
}
