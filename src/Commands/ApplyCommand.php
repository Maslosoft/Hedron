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

namespace Maslosoft\Hedron\Commands;

use Maslosoft\Addendum\Interfaces\AnnotatedInterface;
use Maslosoft\Cli\Shared\Log\Logger;
use Maslosoft\Hedron\Applier;
use Maslosoft\Sitcom\Command;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplyCommand extends ConsoleCommand implements AnnotatedInterface
{

	protected function configure(): void
	{
		$this->setName("commit");
		$this->setDescription("Apply headers to all php classes");
		$this->setDefinition([
		]);

		$help = <<<EOT
The <info>show</info> command applies informational headers to all of your project files containing class definitions.
EOT;
		$this->setHelp($help);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$logger = new Logger($output);
		(new Applier($logger))->apply();
		return 1;
	}

	/**
	 * @SlotFor(Command)
	 * @param Command $signal
	 */
	public function reactOn(Command $signal): void
	{
		$signal->add($this, 'hedron');
	}

}
