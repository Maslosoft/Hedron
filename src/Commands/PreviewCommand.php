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

namespace Maslosoft\Hedron\Commands;

use Maslosoft\Hedron\Applier;
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
		$this->setDescription("Show list of files to which headers will be applied");
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
		$output->writeln("Following files will be processed:");
		$applier = new Applier($output);
		$applier->listFiles();
	}

	/**
	 * @SlotFor(Maslosoft\Sitcom\Command)
	 * @param Maslosoft\Signals\Command $signal
	 */
	public function reactOn(\Maslosoft\Sitcom\Command $signal)
	{
		$signal->add($this, 'hedron');
	}
}
