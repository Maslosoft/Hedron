<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\Hedron;

use Symfony\Component\Yaml\Yaml;

/**
 * Configuration
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class Configuration
{

	/**
	 *
	 * @var string
	 */
	private $dir = '';

	public function __construct($dir = null)
	{
		$this->dir = $dir? : getcwd();
	}

	public function load()
	{
		$config = $this->_loadConfigFile();
		$composer = $this->_loadComposerFile();
		$config['filter'] = isset($config['filter']) ? $config['filter'] : [];

		unset($config['sources']);

		if (!isset($config['sources']))
		{
			if (isset($composer->autoload))
			{
				$config['sources'] = $this->_extractAutoload($composer->autoload);
			}
		}
		if (!is_array($config['sources']))
		{
			$config['sources'] = [$config['sources']];
		}
		return $config;
	}

	private function _loadComposerFile()
	{
		$file = sprintf('%s/composer.json', $this->dir);
		$composer = file_exists($file) ? json_decode(file_get_contents($file)) : array();
		return $composer;
	}

	private function _loadConfigFile()
	{
		$file = sprintf('%s/.hedron.yml', $this->dir);
		$config = file_exists($file) ? Yaml::parse(file_get_contents($file)) : array();
		return $config;
	}

	public function _extractAutoload($autoload)
	{
		$dirs = [];
		if (isset($autoload->classmap))
		{
			$dirs = $autoload->classmap;
		}
		unset($autoload->classmap);
		unset($autoload->files);

		foreach ((array) $autoload as $psr)
		{
			foreach ((array) $psr as $path)
			{
				if (is_array($path))
				{
					$dirs = array_merge($dirs, $path);
					continue;
				}
				$dirs[] = $path;
			}
		}
		return $dirs;
	}

}
