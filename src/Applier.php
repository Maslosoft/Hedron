<?php

/**
* This software package is licensed under New BSD license.
* 
* @package maslosoft/hedron
* @licence New BSD
* 
* @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
* 
* @link http://maslosoft.com/hedron/
*/

namespace Maslosoft\Hedron;

use Maslosoft\Hedron\Finder\Filter;
use Maslosoft\Hedron\Helpers\StringHelper;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Applier
{

	public $config;

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

	/**
	 *
	 * @var Renderer
	 */
	private $renderer = null;
	private $success = [];
	private $processed = 0;

	public function __construct(OutputInterface $output = null)
	{
		if (null === $output)
		{
			$output = new NullOutput();
		}
		$this->output = $output;
		$this->config = (new Configuration())->load();
		$this->renderer = new Renderer($this->config);
	}

	public function apply()
	{

		foreach ($this->config['sources'] as $dir)
		{
			foreach ($this->_getFiles($dir) as $fileName)
			{
				$this->_applyHeaders(sprintf('%s%s', $dir, $fileName));
			}
		}

		// Notice
		$this->output->writeln(sprintf('Processed %d files', $this->processed));
		if (count($this->success) == array_sum($this->success))
		{
			// Success
			$this->output->writeln(sprintf('Modified %d files', array_sum($this->success)));
		}
		else
		{
			// Error
			$this->output->writeln(sprintf('Failed to modify %d files', count($this->success) - array_sum($this->success)));
		}
	}

	public function listFiles()
	{
		foreach ($this->config['sources'] as $dir)
		{
			foreach ($this->_getFiles($dir) as $fileName)
			{
				// Notice
				$niceDir = str_replace('/', DIRECTORY_SEPARATOR, ltrim($dir, './\\'));
				$this->output->writeln($niceDir.$fileName);
			}
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
		$line = 0;
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
		if(!$line)
		{
			return;
		}
		if(!$ns)
		{
			return;
		}
		$n = StringHelper::detectNewline($source);
		$lines = array_slice(explode($n, $source), $line - 1);
		$new = sprintf("<?php$n$n%s$n$n%s", $this->renderer->render(), implode($n, $lines));
		
		if (is_writable($file))
		{
			if($new == $source)
			{
				$success = true;
			}
			else
			{
				$success = file_put_contents($file, $new);
			}
			$niceFile = str_replace('/', DIRECTORY_SEPARATOR, ltrim($file, './\\'));
			if ($success)
			{
				// Success
				if($new == $source)
				{
					$this->output->writeln(sprintf('Skipped %s', $niceFile));
				}
				else
				{
					$this->output->writeln(sprintf('Written %s', $niceFile));
					$this->success[] = true;
				}
			}
			else
			{
				// Fail
				$this->output->writeln(sprintf('Failed %s', $niceFile));
				$this->success[] = false;
			}
		}
		else
		{

			$this->success[] = false;
		}
	}

}
