<?php

use Maslosoft\Hedron\Commands\ApplyCommand;
use Maslosoft\Hedron\Commands\PreviewCommand;
use Maslosoft\Hedron\Commands\RenderTemplateCommand;
use Symfony\Component\Console\Application;

date_default_timezone_set('UTC');

require __DIR__ . '/vendor/autoload.php';

$logo = <<<LOGO
    __  __         __               
   / / / /__  ____/ /________  ____ 
  / /_/ / _ \/ __  / ___/ __ \/ __ \
 / __  /  __/ /_/ / /  / /_/ / / / /
/_/ /_/\___/\__,_/_/   \____/_/ /_/ 

LOGO;
if($argc == 1)
{
	echo str_replace("\n", PHP_EOL, $logo);
	echo PHP_EOL;
}
$app = new Application('Hedron', '0.1.0');
$app->addCommands([
	new ApplyCommand(),
	new PreviewCommand(),
	new RenderTemplateCommand()
]);
$app->run();
