<?php

/**
 * PHP source code class header applier
 *
 * This software package is licensed under `AGPL-3.0-only, proprietary` license[s].
 *
 * @package maslosoft/hedron
 * @license AGPL-3.0-only, proprietary
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 * @link https://maslosoft.com/hedron/
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
	private string $dir;

	public function __construct($dir = null)
	{
		$this->dir = $dir? : getcwd();
	}

	public function load()
	{
		$config = $this->_loadConfigFile();
		$composer = $this->_loadComposerFile();
		$config['filter'] = $config['filter'] ?? [];

		// Sources setup
		if (!isset($config['sources']) && isset($composer['autoload']))
		{
			$config['sources'] = $this->_extractAutoload($composer['autoload']);
		}
		if (!is_array($config['sources']))
		{
			$config['sources'] = [$config['sources']];
		}
		foreach($config['sources'] as $key => $dir)
		{
			$dirFormatted = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR;
			$dirFormatted = preg_replace('~[\\\\/]~', DIRECTORY_SEPARATOR, $dirFormatted);
			$config['sources'][$key] = $dirFormatted;
		}
		// Template
		if(empty($config['template']))
		{
			$config['template'] = sprintf('%s/templates/default.html', dirname(__DIR__));
		}

		if(empty($config['tmp']))
		{
			$config['tmp'] = sprintf('%s/tmp/', dirname(__DIR__));
		}

		if(empty($composer['name']))
		{
			$composer['name'] = 'empty/name';
		}

		$config['composer'] = $composer;
		$parts = explode('/', $composer['name']);
		
		$config['shortName'] = array_pop($parts);
		$config['vendorName'] = array_pop($parts);
		return $config;
	}

	private function _loadComposerFile()
	{
		$file = sprintf('%s/composer.json', $this->dir);
		return file_exists($file) ? json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR) : array();
	}

	private function _loadConfigFile()
	{
		$file = sprintf('%s/.hedron.yml', $this->dir);
		return file_exists($file) ? Yaml::parse(file_get_contents($file)) : array();
	}

	public function _extractAutoload($autoload): array
	{
		$dirs = $autoload->classmap ?? [];
		unset($autoload->classmap, $autoload->files);

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
