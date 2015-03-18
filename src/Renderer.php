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

		if (isset($this->config['composer']['license']) && !isset($params['license']))
		{
			if (is_array($this->config['composer']['license']))
			{
				$extra['license'] = implode(', ', $this->config['composer']['license']);
			}
			else
			{
				$extra['license'] = $this->config['composer']['license'];
			}
		}
		$data = array_merge($extra, $this->config, $params);
		$result = $renderer($data);
		$this->_wrapWithStars($result);
		return $result;
	}

	/**
	 * @param type $text
	 */
	private function _wrapWithStars(&$text)
	{
		$newline = StringHelper::detectNewline($text);
		$text = preg_replace('~^(.*)~m', ' * \1', $text);
		$text = preg_replace('~\s+$~m', '', $text);
		$text = "/**$newline$text$newline */";
	}

	public function __destruct()
	{
		if (file_exists($this->path))
		{
			unlink($this->path);
		}
	}

}
