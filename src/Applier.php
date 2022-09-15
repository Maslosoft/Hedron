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

use Maslosoft\Hedron\Finder\Filter;
use Maslosoft\Hedron\Helpers\StringHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function rtrim;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Applier
{

	public $config;

	/**
	 * Output
	 * @var LoggerInterface
	 */
	private LoggerInterface $output;

	/**
	 *
	 * @var Renderer
	 */
	private Renderer $renderer;
	private array $success = [];
	private int $processed = 0;

	public function __construct(LoggerInterface $logger = null)
	{
		if (null === $logger)
		{
			$logger = new NullLogger();
		}
		$this->output = $logger;
		$this->config = (new Configuration())->load();
		$this->renderer = new Renderer($this->config);
	}

	public function apply(): void
	{

		foreach ($this->config['sources'] as $dir)
		{
			foreach ($this->getFiles($dir) as $fileName)
			{
				$this->applyHeaders(sprintf('%s%s', $dir, $fileName));
			}
		}

		// Notice
		$this->output->info(sprintf('Processed <comment>%d</comment> files', $this->processed));
		if (count($this->success) === array_sum($this->success))
		{
			// Success
			$this->output->info(sprintf('Modified <info>%d</info> files', array_sum($this->success)));
		}
		else
		{
			// Error
			$this->output->error(sprintf('Failed to modify %d files', count($this->success) - array_sum($this->success)));
		}
	}

	/**
	 * @return int
	 */
	public function listFiles(): int
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
					// Use alert log level to always show
					// but hide it as error with info tag
					$this->output->alert(sprintf('<info>%s%s</info>', $niceDir, $fileName));
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
	private function getFiles(string $dir): array
	{
		$info = new \SplFileInfo($dir);
		if($info->isFile())
		{
			return [''];
		}
		$finder = new Finder();

		$filter = new Filter($dir, $this->config['filter'], $this->output);

		$result = [];

		foreach ($finder->name('*.php')->files()->in($dir) as $entry)
		{
			/* @var $entry SplFileInfo */
			if ($filter->isFiltered(realpath($entry->getPathname())))
			{
				continue;
			}

			$file = $entry->getRelativePathname();

			// Ignore views
			$firstChar = basename($file)[0];
			if ('_' === $firstChar || ctype_lower($firstChar))
			{
				continue;
			}

			$result[] = $file;
		}
		return $result;
	}

	private function applyHeaders(string $file, bool $checkOnly = false): bool
	{
		$this->processed++;
		$file = rtrim($file, '/\\');
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
			// Token value - which is not used in this case
			array_shift($token);
			$line = array_shift($token);
			if ($type === T_NAMESPACE)
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
			$this->output->debug("No namespace in <info>$file</info>, skipping...");
			return false;
		}
		$n = StringHelper::detectNewline($source);
		if(StringHelper::isGenerated($source, $line, $n))
		{
			$this->output->debug("Generated file: <info>$file</info>, skipping...");
			return false;
		}
		$lines = array_slice(explode($n, $source), $line - 1);
		$new = sprintf("<?php$n$n%s$n$n%s", $this->renderer->render(), implode($n, $lines));
		if ($checkOnly)
		{
			return $new !== $source;
		}
		if (is_writable($file))
		{
			if ($new === $source)
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
				if ($new === $source)
				{
					$this->output->debug(sprintf('<comment>Skipped</comment> %s', $niceFile));
					return false;
				}

				$this->output->info(sprintf('<info>Written</info> %s', $niceFile));
				$this->success[] = true;
				return true;
			}

			// Fail
			$this->output->error(sprintf('Failed %s', $niceFile));
			$this->success[] = false;
			return false;
		}

		$this->success[] = false;
		return false;
	}

}
