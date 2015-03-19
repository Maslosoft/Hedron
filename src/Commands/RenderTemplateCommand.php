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

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("This is how template will look like in files:");
		$renderer = new Renderer((new Configuration)->load());
		$output->writeln($renderer->render());
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
