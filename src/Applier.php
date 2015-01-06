<?php

namespace Maslosoft\Hedron;

use Maslosoft\Hedron\Finder\Filter;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Applier
{

	/**
	 * Output
	 * @var OutputInterface
	 */
	private $output = null;

	/**
	 * Fitler
	 * @var Filter
	 */
	private $filter = null;

	public function __construct(OutputInterface $output = null)
	{
		if (null === $output)
		{
			$output = new NullOutput();
		}
		$this->output = $output;
		$filter = [
			'blacklist' => [
				'include' => [
					'vendor/*'
				]
			]
		];
		$this->config['filter'] = $filter;
	}

	public function apply()
	{
		foreach ($this->_getFiles() as $file)
		{
			$this->_applyHeaders($file);
		}
		// Notice
		$this->output->writeln(sprintf('Processed %d files', $this->processed));
		if ($this->isSuccess())
		{
			// Success
			$this->builder->writeln(sprintf('Modified %d files', array_sum($this->success)));
		}
		else
		{
			// Error
			$this->builder->writeln(sprintf('Failed to modify %d files', count($this->success) - array_sum($this->success)));
		}
	}

	public function listFiles()
	{
//		$dir = __DIR__ . '/../';
		$dir = getcwd();
		foreach ($this->_getFiles($dir) as $fileName)
		{
			// Notice
			$this->output->writeln($fileName);
		}
	}

	/**
	 * TODO Implement filters, see Codeception/Coverage/Filter.php
	 * @param type $dir
	 * @return type
	 */
	private function _getFiles($dir)
	{
		$finder = new Finder();

		$this->filter = new Filter($dir, $this->config['filter']);

		$result = [];

		foreach ($finder->name('*.php')->files()->in($dir) as $entry)
		{
			/* @var $entry SplFileInfo */
			if ($this->filter->isFiltered(realpath($entry->getPathname())))
			{
				continue;
			}

			$file = $entry->getRelativePathname();

			// Ignore views
			$firstChar = basename($file)[0];
			if (ctype_lower($firstChar) || '_' == $firstChar)
			{
				continue;
			}

//				$this->_applyHeaders(__DIR__ . '/ApplierTest.php');
//				break;
			$result[] = $file;
		}
		return $result;
	}

	private function _applyHeaders($file)
	{
		$this->processed++;
		$source = file_get_contents($file);
		$tokens = token_get_all($source);
		$ns = '';
		foreach ($tokens as $i => $token)
		{
			if (is_string($token))
			{
				continue;
			}
			$type = array_shift($token);
			$value = array_shift($token);
			$line = array_shift($token);
			if ($type == T_NAMESPACE)
			{
				$ns = $tokens[$i + 2][1];
				break;
			}
		}
		$new = false;
		if ($ns == 'Maslosoft')
		{
			$lines = array_slice(explode("\n", $source), $line - 1);
			$new = sprintf("%s\n%s", $this->_header, implode("\n", $lines));
		}
//		echo "<pre>$new";
//		exit;
		if (is_writable($file) && $new)
		{
			$success = file_put_contents($file, $new);
			if ($success)
			{
				// Success
				$this->builder->writeln(sprintf('Written %s', $file), OutputInterface::VERBOSITY_VERY_VERBOSE);
				$this->success[] = true;
			}
			else
			{
				// Success
				$this->builder->writeln(sprintf('Failed %s', $file), OutputInterface::VERBOSITY_VERBOSE);
				$this->success[] = false;
			}
		}
		else
		{

			$this->success[] = false;
		}
	}

}
