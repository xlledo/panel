<?php
namespace Ttt\Panel;


class Panel {
	public function __construct()
	{

	}

	public function saluda()
	{
		return 'Hola';
	}

	public function getConfigMergedForFile($fileName = 'acciones')
	{
		$installedPackages = array_keys(\Config::getNamespaces());

		$allConfig = array();
		foreach($installedPackages as $key)
		{
			$thisConfig = \Config::get($key . '::' . $fileName, array());

			$allConfig = array_merge($allConfig, $thisConfig);
		}

		return $allConfig;
	}
}
