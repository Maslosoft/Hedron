<?php

namespace Maslosoft\Hedron\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ApplyCommand extends Command
{

	protected function configure()
	{
		$this->setName("apply");
		$this->setDescription("Apply headers to all php classes");
		$this->setDefinition([
		]);

		$help = <<<EOT
The <info>apply</info> command applies informational headers to all of your project files containing class definitions.
EOT;
		$this->setHelp($help);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

	}

}
