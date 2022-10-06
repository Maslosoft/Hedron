<?php
/*
 * This file is part of the File_Iterator package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Maslosoft\Hedron\Helpers\Filter;

use FilterIterator;
use Iterator;

/**
 * FilterIterator implementation that filters files based on prefix(es) and/or
 * suffix(es). Hidden files and files from hidden directories are also filtered.
 *
 * @since     Class available since Release 1.0.0
 */
class FileFilterIterator extends FilterIterator
{
    public const PREFIX = 0;
    public const SUFFIX = 1;

    /**
     * @var array
     */
    protected array $suffixes = [];

    /**
     * @var array
     */
    protected array $prefixes = [];

    /**
     * @var array
     */
    protected array $exclude = [];

    /**
     * @var string
     */
    protected $basePath;

	/**
	 * @param Iterator    $iterator
	 * @param array       $suffixes
	 * @param array       $prefixes
	 * @param array       $exclude
	 * @param string|null $basePath
	 */
    public function __construct(Iterator $iterator, array $suffixes = array(), array $prefixes = array(), array $exclude = array(), string $basePath = NULL)
    {
        $exclude = array_filter(array_map('realpath', $exclude));

        if ($basePath !== NULL) {
            $basePath = realpath($basePath);
        }

        if ($basePath === FALSE) {
            $basePath = NULL;
        } else {
            foreach ($exclude as &$_exclude) {
                $_exclude = str_replace($basePath, '', $_exclude);
            }
        }

        $this->prefixes = $prefixes;
        $this->suffixes = $suffixes;
        $this->exclude  = $exclude;
        $this->basePath = $basePath;

        parent::__construct($iterator);
    }

    /**
     * @return bool
     */
    public function accept(): bool
    {
        $current  = $this->getInnerIterator()->current();
        $filename = $current->getFilename();
        $realpath = $current->getRealPath();

        if ($this->basePath !== null) {
            $realpath = str_replace($this->basePath, '', $realpath);
        }

        // Filter files in hidden directories.
        if (preg_match('=/\.[^/]*/=', $realpath)) {
            return false;
        }

        return $this->acceptPath($realpath) &&
               $this->acceptPrefix($filename) &&
               $this->acceptSuffix($filename);
    }

    /**
     * @param  string $path
     * @return bool
     * @since  Method available since Release 1.1.0
     */
    protected function acceptPath($path): bool
    {
        foreach ($this->exclude as $exclude) {
            if (strpos($path, $exclude) === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  string $filename
     * @return bool
     * @since  Method available since Release 1.1.0
     */
    protected function acceptPrefix(string $filename): bool
    {
        return $this->acceptSubString($filename, $this->prefixes, self::PREFIX);
    }

    /**
     * @param  string $filename
     * @return bool
     * @since  Method available since Release 1.1.0
     */
    protected function acceptSuffix(string $filename): bool
    {
        return $this->acceptSubString($filename, $this->suffixes, self::SUFFIX);
    }

    /**
     * @param  string $filename
     * @param  array  $subStrings
     * @param  int    $type
     * @return bool
     * @since  Method available since Release 1.1.0
     */
    protected function acceptSubString(string $filename, array $subStrings, int $type): bool
    {
        if (empty($subStrings)) {
            return true;
        }

        $matched = false;

        foreach ($subStrings as $string) {
            if (($type === self::PREFIX && strpos($filename, $string) === 0) ||
                ($type === self::SUFFIX &&
                 substr($filename, -1 * strlen($string)) === $string)) {
                $matched = true;
                break;
            }
        }

        return $matched;
    }
}
