<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\Hedron\Finder;

use PHP_CodeCoverage;
use PHP_CodeCoverage_Filter;
use Symfony\Component\Finder\Finder;

/**
 * Based on Codeception/Coverage/Filter.php
 */
class Filter
{

	/**
	 * Filter instance
	 * @var PHP_CodeCoverage_Filter	 
	 */
	private $filter = null;

	private $workingDir = null;

	/**
	 * Configuration array
	 * @var string[][][]
	 */
//	private $config = [];

	/**
	 * Filter
	 *
	 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
	 */
	function __construct($workingDir, $config = [])
	{
		$this->workingDir = $workingDir;
		$this->filter = new PHP_CodeCoverage_Filter();
		$this->whiteList($config)->blackList($config);
	}

	public function isFiltered($filename)
	{
		return $this->filter->isFiltered($filename);
	}

	/**
	 * @param $config
	 * @return Filter
	 */
	protected function whiteList($config)
	{
		$filter = $this->filter;
		if (!isset($config['whitelist']))
		{
			$config['whitelist'] = array();
			if (isset($config['include']))
			{
				$config['whitelist']['include'] = $config['include'];
			}
			if (isset($config['exclude']))
			{
				$config['whitelist']['exclude'] = $config['exclude'];
			}
		}
		if (isset($config['whitelist']['include']))
		{
			foreach ($config['whitelist']['include'] as $fileOrDir)
			{
				$finder = strpos($fileOrDir, '*') === false ? array($fileOrDir) : $this->matchWildcardPattern($fileOrDir);
				foreach ($finder as $file)
				{
					$filter->addFileToWhitelist($file);
				}
			}
		}
		if (isset($config['whitelist']['exclude']))
		{
			foreach ($config['whitelist']['exclude'] as $fileOrDir)
			{
				$finder = strpos($fileOrDir, '*') === false ? array($fileOrDir) : $this->matchWildcardPattern($fileOrDir);
				foreach ($finder as $file)
				{
					$filter->removeFileFromWhitelist($file);
				}
			}
		}
		return $this;
	}

	/**
	 * @param $config
	 * @return Filter
	 */
	protected function blackList($config)
	{
		$filter = $this->filter;
		if (isset($config['blacklist']))
		{
			if (isset($config['blacklist']['include']))
			{
				foreach ($config['blacklist']['include'] as $fileOrDir)
				{
					$finder = strpos($fileOrDir, '*') === false ? array($fileOrDir) : $this->matchWildcardPattern($fileOrDir);
					foreach ($finder as $file)
					{
						$filter->addFileToBlacklist($file);
					}
				}
			}
			if (isset($config['blacklist']['exclude']))
			{
				foreach ($config['blacklist']['exclude'] as $fileOrDir)
				{
					$finder = strpos($fileOrDir, '*') === false ? array($fileOrDir) : $this->matchWildcardPattern($fileOrDir);
					foreach ($finder as $file)
					{
						$filter->removeFileFromBlacklist($file);
					}
				}
			}
		}
		return $this;
	}

	protected function matchWildcardPattern($pattern)
	{
		$finder = Finder::create();
		$fileOrDir = str_replace('\\', '/', $pattern);
		$parts = explode('/', $fileOrDir);
		$file = array_pop($parts);
		$finder->name($file);
		if (count($parts))
		{
			$last_path = array_pop($parts);
			if ($last_path === '*')
			{
				$finder->in($this->workingDir . implode('/', $parts));
			}
			else
			{
				$finder->in($this->workingDir . implode('/', $parts) . '/' . $last_path);
			}
		}
		$finder->ignoreVCS(true)->files();
		return $finder;
	}

	/**
	 * @return PHP_CodeCoverage_Filter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

}
