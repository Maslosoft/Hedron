<?php

/**
 * This software package is licensed under `AGPL, Commercial` license[s].
 *
 * @package maslosoft/hedron
 * @license AGPL, Commercial
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 *
 * @link http://maslosoft.com/hedron/
 */

namespace Maslosoft\Hedron\Helpers;

/**
 * StringHelper
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class StringHelper
{

	public static function detectNewline($text)
	{
		$newlines = preg_replace('/.*/', '', $text);
		return substr($newlines, 0, 1);
	}

}
