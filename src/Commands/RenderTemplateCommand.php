<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\Hedron\Commands;

use Maslosoft\Hedron\Configuration;
use Maslosoft\Hedron\Renderer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ShowCommand
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class RenderTemplateCommand extends Command
{
	protected function configure()
	{
		$this->setName("rendertemplate");
		$this->setDescription("Show how current template will look like");
		$this->setDefinition([
		]);

		$help = <<<EOT
The <info>rendertemplate</info> command will display header which will appear in each of your php class definition files.
				No files will be modified at this stage.
EOT;
		$this->setHelp($help);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("This is how template will look like in files:");
		$renderer = new Renderer((new Configuration)->load());
		$output->writeln($renderer->render());
	}

}
