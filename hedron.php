<?php

use Maslosoft\Hedron\Commands\ApplyCommand;
use Maslosoft\Hedron\Commands\PreviewCommand;
use Symfony\Component\Console\Application;

date_default_timezone_set('UTC');

require __DIR__ . '/vendor/autoload.php';

$app = new Application('Hedron', '0.1.0');
$app->addCommands([
	new ApplyCommand(),
	new PreviewCommand()
]);
$app->run();
