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
use Maslosoft\Hedron\Configuration;
use Maslosoft\Hedron\Renderer;
use Maslosoft\Sitcom\Command;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ShowCommand
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class RenderTemplateCommand extends ConsoleCommand implements AnnotatedInterface
{

	protected function configure(): void
	{
		$this->setName("show");
		$this->setDescription("Show how current template will look like");
		$this->setDefinition([
		]);

		$help = <<<EOT
The <info>show</info> command will display header which will appear in each of your php class definition files.
				No files will be modified at this stage.
EOT;
		$this->setHelp($help);
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$output->writeln("This is how template will look like in files:");
		$renderer = new Renderer((new Configuration)->load());
		$output->writeln($renderer->render());
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
