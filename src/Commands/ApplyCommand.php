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

namespace Maslosoft\Hedron\Commands;

use Maslosoft\Addendum\Interfaces\AnnotatedInterface;
use Maslosoft\Hedron\Applier;
use Maslosoft\Sitcom\Command;
use Symfony\Component\Console\Command\Command as ConsoleComand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplyCommand extends ConsoleComand implements AnnotatedInterface
{

	protected function configure()
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

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new Applier($output))->apply();
	}

	/**
	 * @SlotFor(Command)
	 * @param Command $signal
	 */
	public function reactOn(Command $signal)
	{
		$signal->add($this, 'hedron');
	}

}
