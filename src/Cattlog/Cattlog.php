<?php namespace Cattlog;

class Cattlog
{
	protected $config;

	/**
	 * @var FilterInterface $filter Store the filter once intiated
	 */
	protected $filter;

	public function __construct($config=array())
	{
		// set the full path of dest
		if (isset($config['dest'])) {
			$config = array_merge($config, array(
				'dest' => getcwd() . '/' . $config['dest'],
			));
		}

		// set the full path of src. also, set as an array even
		// for single item
		if (isset($config['src'])) {
			if (!is_array($config['src'])) $config['src'] = array($config['src']);

			foreach ($config['src'] as $i => $src) {
				$config['src'][$i] = getcwd() . '/' . $src;
			}
		}

		$this->config = $config;
	}

	/**
	 * Will update the dest file from changes in src
	 * @param string $lang Name of the dest in dest dir
	 */
	public function update($lang)
	{
		// ensure lang is in array
		if (isset($this->config['languages']) and !in_array($lang, $this->config['languages']))
			throw new \Exception('Language "' . $lang . '" not found in config "languages" array.');

		// get the data ffrom the dest file
		$data = $this->getDataFromFile($lang);

		// set key arrays
		$keysFromData = array_keys($data);
		$keysFromCode = $this->getKeysFromSrcFiles();
		$keysToAdd = $this->getAddedKeys($keysFromData, $keysFromCode);
		$keysToRemove = $this->getRemovedKeys($keysFromData, $keysFromCode);

		// remove the old keys
		if (count($keysToRemove))
			$data = $this->removeKeys($data, $keysToRemove);

		// add the new ones (blank, coz they're new).
		if (count($keysToAdd))
			$data = $this->addKeys($data, $keysToAdd);

		// data is now complete, write to file
		$this->writeDataToFile($lang, $data);
	}

	/**
	 * Will remove an array of keys from data
	 * @param array $data Data to remove keys from
	 * @param array $keysToRemove Keys to remove from data
	 * @return array Data with keys removed
	 */
	public function removeKeys($data, $keysToRemove)
	{
		return array_diff_key($data, array_flip($keysToRemove));
	}

	/**
	 * Will add keys with blank values
	 * @param array $data Data to add keys to
	 * @param array $keysToAdd Keys to add to data
	 * @return array Data with keys added
	 */
	public function addKeys($data, $keysToAdd)
	{
		$keysToAdd = array_flip($keysToAdd); // array('KEY_1'=>0, 'KEY_2'=>1, ...)
		array_walk($keysToAdd, function (&$value, $key) {
			$value = '';
		});

		return array_merge($data, $keysToAdd);
	}

	/**
	 * This will compare the old and newly scanned keys, and
	 * return an array of keys which have been removed. Useful for
	 * showing what difference between scans
	 * @param array $keysFromDest The array of old key/pairs
	 * @param array $keysFromScan The array of new key/pairs
	 * @return array Removed key/values
	 */
	public function getRemovedKeys($keysFromDest, $keysFromScan)
	{
		// first, find all the keys which have not been removed
		$notRemoved = array_intersect($keysFromScan, $keysFromDest);

		// find the keys that are in $keysFromDest, but not long in $keysFromScan
		// use array_values to fix indexes
		return array_values(array_diff($keysFromDest, $notRemoved));
	}

	/**
	 * This will compare the old and newly scanned keys, and
	 * return an array of keys which are new. Useful for
	 * showing what difference between scans
	 * @param array $keysFromDest The array of old key/pairs
	 * @param array $keysFromScan The array of new key/pairs
	 * @return array Added key/values
	 */
	public function getAddedKeys($keysFromDest, $keysFromScan)
	{
		// find the keys that are in $keysFromDest, but not long in $keysFromScan
		return array_values(array_diff($keysFromScan, $keysFromDest));
	}

	/**
	 * Get the keys from source directories
	 * @return array Keys in an indexed array
	 */
	public function getKeysFromSrcFiles()
	{
		// get files in dir
		$files = $this->getFiles($this->config['src']);

		// for each file, get the matches
		$keys = array();
		foreach ($files as $file) {

			// get the contents of the file
			$contents = file_get_contents($file);

			// regex on it to get all the matches
			preg_match_all($this->config['pattern'], $contents, $matches);

			// put the matches into
			$keys = array_merge(array_flip($matches[1]), $keys);
		}

		// array_flip will deal with the duplicates
		return array_keys($keys);
	}

	/**
	 * Get the destination file name from "lang" in config
	 * @param string $lang The name of the file
	 * @return string Full path
	 */
	public function getDestFilePath($lang)
	{
		return str_replace("{lang}", $lang, $this->config['dest']);
	}

	/**
	 * Get the data from the lang file. Will at least return
	 * and empty array
	 * @param string $lang Language code (e.g. en)
	 * @return array Data
	 */
	public function getDataFromFile($lang)
	{
		$filter = $this->getFilter();
		$destPath = $this->getDestFilePath($lang);

		// get the data from the dest file, or set to null
		// null if the file exists is safer, sometimes in error null will get
		// written to the text file. In either case, we got it covered
		$data = array();
		if (file_exists($destPath)) {
			$destText = file_get_contents($destPath);
			$data = $filter->decode($destText);

			// in any case, $data should be an array
			if (!is_array($data)) {
				$data = array();
			}
		}

		return $data;
	}

	/**
	 * Write the data to file
	 * @param string $lang Language code (e.g. en)
	 * @param array $data Data to write
	 */
	public function writeDataToFile($lang, $data)
	{
		$filter = $this->getFilter();
		$destPath = $this->getDestFilePath($lang);

		$destPath = $this->getDestFilePath($lang);
		file_put_contents($destPath, $filter->encode($data));
	}


	// TODO Move to Cattlog\Util\FileSystem

	/**
	 * Recursive scan to get files within a dir
	 * @param string|array $dirs Directories to scan
	 * @param string $prefix Attach a prefix to each file (optional)
	 * @return array Files
	 */
	public function getFiles($dirs)
	{
		// ensure that $dirs is an array of directories for the locale_lookup
		// if set only as string (e.g. "/path/to/src")
		if (! is_array($dirs))
			$dirs = array($dirs);

		// remove trailing slash from config dir
		$files = array();
		foreach ($dirs as $dir) {
			$dir = rtrim($dir, '\\/');
			$files = $this->_getFilesRecursive($dir);
		}

		return $files;
	}

	/**
	 * Recursive scan to get files within a dir
	 * @param string $dir Directory to scan
	 * @param string $prefix Attach a prefix to each file (optional)
	 * @return array Files
	 */
	protected function _getFilesRecursive($dir)
	{
		$files = array();
		foreach (scandir($dir) as $f) {
			if ($f !== '.' and $f !== '..') {
				if (is_dir("$dir/$f")) {
					$files = array_merge($files, $this->_getFilesRecursive("$dir/$f"));
				} else {
					$files[] = $dir.'/'.$f;
				}
			}
		}

		return $files;
	}

	/**
	 * Get the filter for the encoding/decoding
	 * @return FilterInterface $filter Our filter for encoding/decosing
	 */
	public function getFilter()
	{
		// return filter
		if (!$this->filter) {

			// Class name convention
			// Upper case the first letter: php -> Php
			$className = ucfirst($this->config['filter']);

			// Attempt to load from .cattlog/filters
			//...

			// Attempt to retrieve from built-in filters
			$classPath = '\\Cattlog\\Filter\\' . $className;
			if (class_exists($classPath))
				$this->filter = new $classPath($this->config);
		}

		return $this->filter;
	}
}
