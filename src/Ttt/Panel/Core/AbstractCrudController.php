<?php
namespace Ttt\Panel\Core;

use \Config;
use \View;
use \Input;
use \Request;

abstract class AbstractCrudController extends PanelController {

	protected $_titulo      = null;//se usa para semánticamente indicar información como El title de las páginas

	static protected $_model_name     = null;//obligatorio para realizar el guardado de un elemento
	static protected $_model          = null;//obligatorio para realizar el guardado de un elemento

	protected $allowed_url_params = array();
}
