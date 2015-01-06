#Maslosoft Hedron

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Maslosoft/Hedron/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Maslosoft/Hedron/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Maslosoft/Hedron/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Maslosoft/Hedron/?branch=master)
<img src="https://travis-ci.org/Maslosoft/Hedron.svg?branch=master" style="height:18px"/>

######PHP class header applier

## Instalation

    composer require maslosoft/hedron --dev

## Usage

Preview files to apply headers

	vendor/bin/hedron preview
	
Display rendered template

	vendor/bin/hedron rendertemplate
	
Apply header to all class files.

<span style="background:red;color:white;">Backup/commit before continue</span>

	vendor/bin/hedron apply
	
## Configuration

Configuration can be provided in yaml file `.hedron.yaml` in root of your project.

## Template


	
## Resources

 * [Project website](http://maslosoft.com/hedron/)
 * [Project Page on GitHub](https://github.com/Maslosoft/Hedron)
 * [Report a Bug](https://github.com/Maslosoft/Hedron/issues)
 * [PHP Addendum library](http://code.google.com/p/addendum/)


