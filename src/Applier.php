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
			foreach ($this->getFiles($dir) as $fileName)
			{
				$this->applyHeaders(sprintf('%s%s', $dir, $fileName));
			}
		}

		// Notice
		$this->output->writeln(sprintf('Processed <comment>%d</comment> files', $this->processed));
		if (count($this->success) == array_sum($this->success))
		{
			// Success
			$this->output->writeln(sprintf('Modified <info>%d</info> files', array_sum($this->success)));
		}
		else
		{
			// Error
			$this->output->writeln(sprintf('Failed to modify %d files', count($this->success) - array_sum($this->success)));
		}
	}

	/**
	 * @return int
	 */
	public function listFiles()
	{
		$num = 0;
		foreach ($this->config['sources'] as $dir)
		{
			foreach ($this->getFiles($dir) as $fileName)
			{
				$modify = $this->applyHeaders(sprintf('%s%s', $dir, $fileName), true);
				if (true === $modify)
				{
					// Notice
					$niceDir = ltrim($dir, './\\');
					$this->output->writeln(sprintf('%s%s', $niceDir, $fileName));
					$num++;
				}
			}
		}
		return $num;
	}

	/**
	 * @param string $dir
	 * @return string[]
	 */
	private function getFiles($dir)
	{
		$finder = new Finder();

		$this->filter = new Filter($dir, $this->config['filter'], $this->output);

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

			$result[] = $file;
		}
		return $result;
	}

	private function applyHeaders($file, $checkOnly = false)
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

		// Seems not PHP, or empty file
		if (empty($line))
		{
			return false;
		}

		// Empty namespace, skip
		if (empty($ns))
		{
			return false;
		}
		$n = StringHelper::detectNewline($source);
		$lines = array_slice(explode($n, $source), $line - 1);
		$new = sprintf("<?php$n$n%s$n$n%s", $this->renderer->render(), implode($n, $lines));
		if ($checkOnly)
		{
			if ($new == $source)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		if (is_writable($file))
		{
			if ($new == $source)
			{
				$success = true;
			}
			else
			{
				$success = file_put_contents($file, $new);
			}
			$niceFile = ltrim($file, './\\');
			if ($success)
			{
				// Success
				if ($new == $source)
				{
					if ($this->output->isVerbose())
					{
						$this->output->writeln(sprintf('<comment>Skipped</comment> %s', $niceFile));
						return false;
					}
				}
				else
				{
					$this->output->writeln(sprintf('<info>Written</info> %s', $niceFile));
					$this->success[] = true;
					return true;
				}
			}
			else
			{
				// Fail
				$this->output->writeln(sprintf('Failed %s', $niceFile));
				$this->success[] = false;
				return false;
			}
		}
		else
		{
			$this->success[] = false;
		}
		return false;
	}

}
