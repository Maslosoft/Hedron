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
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PreviewCommand
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class PreviewCommand extends ConsoleCommand implements AnnotatedInterface
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
		$modified = $applier->listFiles();
		if($modified === 0)
		{
			$output->writeln("No files will be modified.");
		}
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
