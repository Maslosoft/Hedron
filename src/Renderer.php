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

namespace Maslosoft\Hedron;

use LightnCandy\LightnCandy;
use Maslosoft\Hedron\Helpers\StringHelper;

/**
 * Renderer
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class Renderer
{

	private array $config;
	private $renderer;
	private string $path;

	public function __construct($config)
	{
		$this->config = $config;
		$str = LightnCandy::compile(file_get_contents($this->config['template']));
		$this->path = sprintf('%s.php', tempnam($config['tmp'], 'tpl'));
		file_put_contents($this->path, sprintf("<?php\n%s", $str));
		$this->renderer = require $this->path;
	}

	public function render($params = [])
	{
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
		$renderer = $this->renderer;
		$result = $renderer($data);
		$this->_wrapWithStars($result);
		return $result;
	}

	/**
	 * @param string $text
	 */
	private function _wrapWithStars(string &$text): void
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
