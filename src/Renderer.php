<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\Hedron;

use LightnCandy;
use Maslosoft\Hedron\Helpers\StringHelper;

/**
 * Renderer
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class Renderer
{

	private $config = [];
	private $renderer;
	private $path = '';

	public function __construct($config)
	{
		$this->config = $config;
		$str = LightnCandy::compile(file_get_contents($this->config['template']));
		$this->path = $config['tmp'] . '/tpl';
		file_put_contents($this->path, $str);
	}

	public function render($params = [])
	{
		$renderer = require $this->path;

		$extra = [
			'year' => date('Y')
		];
		$result = $renderer(array_merge($extra, $this->config, $params));
		$this->_wrapWithStars($result);
		return $result;
	}

	/**
	 *
	 * @param type $text
	 */
	private function _wrapWithStars(&$text)
	{
		$newline = StringHelper::detectNewline($text);
		$text = preg_replace('~^(.*)~m', '* \1', $text);
		$text = "/**$newline$text$newline*/";
	}

	public function __destruct()
	{
		if (file_exists($this->path))
		{
			unlink($this->path);
		}
	}

}
