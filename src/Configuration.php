<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\Hedron;

/**
 * Configuration
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class Configuration
{

	protected static function loadConfigFile($file, $parentConfig)
	{
		$config = file_exists($file) ? Yaml::parse(file_get_contents($file)) : array();
		return self::mergeConfigs($parentConfig, $config);
	}

}
