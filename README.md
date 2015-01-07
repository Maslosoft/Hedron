#Maslosoft Hedron

######PHP class header applier

Hedron helps you make your class headers up to date and consistent. It will apply predefined header to all files containing namespaced class definitions.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Maslosoft/Hedron/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Maslosoft/Hedron/?branch=master)

## Instalation

    composer require maslosoft/hedron --dev

## Usage

Preview files to apply headers

	vendor/bin/hedron preview
	
Display rendered template

	vendor/bin/hedron rendertemplate
	
Apply header to all class files.

<span style="background:red;color:white;">Backup/commit your project before continue.</span>
This will write headers to files.

	vendor/bin/hedron apply
	
## Configuration

Configuration can be provided in yaml file `.hedron.yml` in root of your project.
Hedron also uses `composer.json` to make your config easier, or even unnessesary.

Here is example with default values (`.hedron.example.yml`):

	# All paths are realtive to your project root
	# Root path or paths with sources. If blank will use composer.autoload paths.
	sources: ""
	# Path to template file, if blank will use vendor/maslosoft/hedron/templates/default.html (no it's not html)
	template: ""
	# Filter configuration, by default empty. Below is some example filter.
	filter:
		whitelist:
			include:
				- app/*
			exclude:
				- app/cache/*
		blacklist:
			include:
				- app/controllers/*
			exclude:
				- app/cache/CacheProvider.php
	# Reserved 
	# This contains composer.json as array
	composer: ""
	tmp: ""

All of this configuration is available in template.


## Template

Template uses handlebars as templating engine. It contains all data from `.hedron.yml` and `composer.json`.

Here is example, default template:

	This software package is licensed under {{composer.license}} license.

	@package {{composer.name}}
	@licence {{composer.license}}
	{{#each composer.authors}}
	@copyright Copyright (c) {{name}} <{{email}}>
	{{/each}}
	{{#if composer.homepage}}@link {{composer.homepage}}{{/if}}
	
And after rendering it looks like that:

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
	
## Replacing

This script modifies source files en masse. For safety and robustness it uses native php tokenizer and replaces everything before `namespace` token. Only that. If there is no namespace declaration file will not be modified.
	
## Resources

 * [Project website](http://maslosoft.com/hedron/)
 * [Project Page on GitHub](https://github.com/Maslosoft/Hedron)
 * [Report a Bug](https://github.com/Maslosoft/Hedron/issues)
 * [PHP Addendum library](http://code.google.com/p/addendum/)


