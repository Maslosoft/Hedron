<?php

/**
 * This software package is licensed under `AGPL, Commercial` license[s].
 *
 * @package maslosoft/hedron
 * @license AGPL, Commercial
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 *
 * @link http://maslosoft.com/hedron/
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

		// Sources setup
		if (!isset($config['sources']))
		{
			if (isset($composer['autoload']))
			{
				$config['sources'] = $this->_extractAutoload($composer['autoload']);
			}
		}
		if (!is_array($config['sources']))
		{
			$config['sources'] = [$config['sources']];
		}
		foreach($config['sources'] as $key => $dir)
		{
			$dirFormatted = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR;
			$dirFormatted = preg_replace('~/\\\~', DIRECTORY_SEPARATOR, $dirFormatted);
			$config['sources'][$key] = $dirFormatted;
		}
		// Template
		if(empty($config['template']))
		{
			$config['template'] = sprintf('%s/templates/default.html', realpath(__DIR__ . '/..'));
		}

		if(empty($config['tmp']))
		{
			$config['tmp'] = sprintf('%s/tmp/', realpath(__DIR__ . '/..'));
		}

		$config['composer'] = $composer;
		return $config;
	}

	private function _loadComposerFile()
	{
		$file = sprintf('%s/composer.json', $this->dir);
		$composer = file_exists($file) ? json_decode(file_get_contents($file), true) : array();
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
