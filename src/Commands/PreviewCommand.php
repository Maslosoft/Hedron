<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\Hedron\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PreviewCommand
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class PreviewCommand extends Command
{

	protected function configure()
	{
		$this->setName("preview");
		$this->setDescription("Show files to which headers will be applied");
		$this->setDefinition([
		]);

		$help = <<<EOT
The <info>preview</info> command will display files to which headers will be applied with <info>apply</info> command.
				No files will be modified at this stage.
EOT;
		$this->setHelp($help);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("Following files will be modified:");
	}

}
