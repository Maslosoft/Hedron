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

namespace Maslosoft\Hedron\Helpers;

use function implode;
use function strpos;

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

	public static function isGenerated($source, $line, $separator)
	{
		$lines = array_slice(explode($separator, $source), 0, $line - 1);
		$header = implode($separator, $lines);
		return strpos($header, '@generated') !== false;
	}

}
