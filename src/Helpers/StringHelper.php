<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
