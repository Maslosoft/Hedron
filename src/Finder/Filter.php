<?php

/**
 * This software package is licensed under `AGPL, Commercial` license[s].
 *
 * @package maslosoft/hedron
 * @license AGPL, Commercial
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 * @link https://maslosoft.com/hedron/
 */

namespace Maslosoft\Hedron\Finder;

use Exception;
use InvalidArgumentException;
use Maslosoft\Hedron\Helpers\Filter\FileFilter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Finder\Finder;

/**
 * Based on Codeception/Coverage/Filter.php
 */
class Filter
{

	/**
	 * Filter instance
	 * @var FileFilter
	 */
	private $filter = null;

	private $workingDir = null;

	/**
	 * @var LoggerInterface
	 */
	private $output = null;

	/**
	 * Filter
	 *
	 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
	 * @param                      $workingDir
	 * @param array                $config
	 * @param LoggerInterface|null $output
	 */
	public function __construct($workingDir, $config = [], LoggerInterface $output = null)
	{
		$this->workingDir = $workingDir;
		$this->filter = new FileFilter;

		if (empty($output))
		{
			$output = new NullLogger;
		}
		$this->output = $output;

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
				try
				{
					$finder = strpos($fileOrDir, '*') === false ? array($fileOrDir) : $this->matchWildcardPattern($fileOrDir);
					foreach ($finder as $file)
					{
						$filter->addFileToWhitelist($file);
					}
				} catch (InvalidArgumentException $e)
				{
					$this->handle($e);
				}
			}
		}
		if (isset($config['whitelist']['exclude']))
		{
			foreach ($config['whitelist']['exclude'] as $fileOrDir)
			{
				try
				{
					$finder = strpos($fileOrDir, '*') === false ? array($fileOrDir) : $this->matchWildcardPattern($fileOrDir);
					foreach ($finder as $file)
					{
						$filter->removeFileFromWhitelist($file);
					}
				} catch (InvalidArgumentException $e)
				{
					$this->handle($e);
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
					try
					{
						$finder = strpos($fileOrDir, '*') === false ? array($fileOrDir) : $this->matchWildcardPattern($fileOrDir);
						foreach ($finder as $file)
						{
							$filter->addFileToBlacklist($file);
						}
					} catch (InvalidArgumentException $e)
					{
						$this->handle($e);
					}
				}
			}
			if (isset($config['blacklist']['exclude']))
			{
				foreach ($config['blacklist']['exclude'] as $fileOrDir)
				{
					try
					{
						$finder = strpos($fileOrDir, '*') === false ? array($fileOrDir) : $this->matchWildcardPattern($fileOrDir);
						foreach ($finder as $file)
						{
							$filter->removeFileFromBlacklist($file);
						}
					} catch (InvalidArgumentException $e)
					{
						$this->handle($e);
					}
				}
			}
		}
		return $this;
	}

	/**
	 * @param $pattern
	 * @throws InvalidArgumentException
	 * @return Finder
	 */
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
	 * @return FileFilter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * Handle exception
	 * @param $e
	 */
	private function handle(Exception $e)
	{
		$this->output->error(sprintf('<error>%s</error> <info>This will be skipped, rest operations should succeed.</info>', $e->getMessage()));
	}

}
