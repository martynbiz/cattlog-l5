<?php namespace Cattlog;

// as this tool is for use within Laravel, we'll just use some of it's array helpers
use Illuminate\Support\Arr;

// use Cattlog\Utils\FileSystem as FS;
// use Cattlog\Utils\Colorize;

class Cattlog
{
	protected $config;

	/**
	 * @var FilterInterface $filter Store the filter once intiated
	 */
	protected $filter;

	public function __construct($config=array())
	{
		// set defaults
		$config = array_merge(array(
			'src' => array("resources/views"),
			'dest' => 'resources/lang/{lang}'
		), $config);

		// for laravel, only php is required so we'll hard set this
		// this may change in future so will keep this in config
		$config['filter'] = 'php';

		// set the full path of dest
		if (isset($config['dest'])) {
			if (!is_array($config['dest'])) $config['dest'] = array($config['dest']);

			foreach ($config['dest'] as $i => $dest) {
				$config['dest'][$i] = getcwd() . '/' . $dest;
			}
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

	// /**
	//  * Will update the dest file from changes in src
	//  * @param string $lang Name of the dest in dest dir
	//  */
	// public function update($lang)
	// {
	// 	// ensure lang is in array
	// 	if (isset($this->config['languages']) and !in_array($lang, $this->config['languages']))
	// 		throw new \Exception('Language "' . $lang . '" not found in config "languages" array.');
	//
	// 	// get the data ffrom the dest file
	// 	$data = $this->getDataFromFile($lang);
	//
	// 	// set key arrays
	// 	$keysFromData = array_keys($data);
	// 	$keysFromCode = $this->getKeysFromSrcFiles();
	// 	$keysToAdd = $this->getAddedKeys($keysFromData, $keysFromCode);
	// 	$keysToRemove = $this->getRemovedKeys($keysFromData, $keysFromCode);
	//
	// 	// remove the old keys
	// 	if (count($keysToRemove)) //array_forget($array, 'products.desk');
	// 		$data = $this->removeKeys($data, $keysToRemove);
	//
	// 	// add the new ones (blank, coz they're new).
	// 	if (count($keysToAdd)) // The array_get function also accepts a default value, which will be returned if the specific key is not found:
	// 		$data = $this->addKeys($data, $keysToAdd);
	//
	// 	// data is now complete, write to file
	// 	$this->writeDataToFile($lang, $data);
	// }

	/**
	 * Will remove an array of keys from data
	 * @param array $data Data to remove keys from
	 * @param array $keysToRemove Keys to remove from data
	 * @return array Data with keys removed
	 */
	public function removeKeys($data, $keysToRemove)
	{
		// loop through each key and remove it
		foreach ($keysToRemove as $key) {
			Arr::forget($data, $key);
		}

		// Tidy up empty arrays
		// $this->removeEmptyKeys($data);

		return $data;
	}

	/**
	 * Will remove empty keys from data. Useful after removing keys,
	 * and some remain still
	 * @param array $data Data to remove keys from
	 * @param array $keysToRemove Keys to remove from data
	 * @return array Data with keys removed
	 */
	public function removeEmptyKeys($data) {

		// first loop through and build array of elements to remove
		$keysToRemove = array();
		foreach ($data as $key => $value) {

			// first, if an array .. dig first
			if (is_array($data[$key])) {
				$data[$key] = $this->removeEmptyKeys($data[$key]);
			}

			// by this point, some children may have been removed, check
			if (is_array($data[$key]) and empty($data[$key])) {
				array_push($keysToRemove, $key);
			}
		}

		// no we are out the loop, delete all those that were empty
		foreach ($keysToRemove as $key) {
			unset($data[$key]);
		}

		return $data;
	}

	/**
	 * Will add keys with blank values
	 * @param array $data Data to add keys to
	 * @param array $keysToAdd Keys to add to data
	 * @return array Data with keys added
	 */
	public function addKeys($data, $keysToAdd)
	{
		// loop through each key and add it
		// only add if it doesn't exist, just encase we accidentally overwrite
		foreach ($keysToAdd as $key) {

			// use Laravel's array_get to check if the element exists using dot notation
			if(! Arr::has($data, $key))
				Arr::set($data, $key, '');
		}

		return $data;
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

		// for each file, get the key in string format
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
	 * Get the keys from source directories.
	 * @return array Keys in an indexed array
	 */
	public function getKeysFromDestFiles($lang)
	{
		// get files in dir
		$files = $this->getDestFiles($lang);

		// for each file, get the key in string format
		$keys = array();
		foreach ($files as $file) {

			// get the {collection} from /path/to/files/{collection}.php
			preg_match("/\/([A-Za-z0-9_\-\.]*)\.php$/", $file, $parts);
			$prefix = @$parts[1] . '.';

			// flatten to get keys such as "between.numeric", then take the
			// keys only (array_keys), and merge with existing (array_merge)
			if (file_exists($file))
				$keys = array_merge(array_keys(Arr::dot(include $file, $prefix)), $keys);
		}

		return $keys;
	}

	/**
	 * Will return the string path of the file
	 * @return string Dest file path
	 */
	public function getDestFile($lang, $group)
	{
		$destFiles = $this->getDestFiles($lang);

		// loop until a match is found
		$found = null;
		foreach ($destFiles as $file) {
			if (preg_match('/' . $group . '\.php$/', $file, $output_array)) {
				$found = $file;
				break;
			}
		}

		// get files in dir
		return $found;
	}

	/**
	 * Get the config array (may be altered from array that was given in instantiation)
	 * @return array Config
	 */
	public function getDestFiles($lang)
	{
		// set dest path by $lang e.g. /path/to/dest/{en}/
		$destDirs = $this->config['dest'];

		// replace path with $lang
		foreach ($destDirs as $i => $dir) {
			$destDirs[$i] = str_replace("{lang}", $lang, $dir);
		}

		// get files in dir
		return $destDirs;
	}

	/**
	 * This will group a key such as ["messages.hello.title", ..] as ["messages" => ["hello.title", ..]]
	 * @param array Ungrouped array of keys
	 * @return array Grouped keys
	 */
	public function groupKeysByFile($keys)
	{
		$grouped = array();
		foreach ($keys as $key) {
			$parts = explode('.', $key);
			$file = array_shift($parts);

			// ensure this file is an array already
			if (!isset($grouped[$file]) or !is_array($grouped[$file]))
				$grouped[$file] = array();

			array_push($grouped[$file], implode('.', $parts));
		}

		return $grouped;
	}

	// /**
	//  * Remove keys from a data set
	//  * @param array $data Data from a file
	//  * @param array $keys Keys to remove from data
	//  * @return array Data minus removed keys
	//  */
	// public function removeKeys($data, $keys)
	// {
	// 	foreach ($keys as $key) {
	// 		Arr:forget($data, $key);
	// 	}
	//
	// 	return $data;
	// }

	// /**
	//  * Get the destination file name from "lang" in config
	//  * @param string $lang The name of the file
	//  * @return string Full path
	//  */
	// public function getDestFilePath($lang)
	// {
	// 	return str_replace("{lang}", $lang, $this->config['dest']);
	// }

	// /**
	//  * Get the data from the lang file. Will at least return
	//  * and empty array
	//  * @param string $lang Language code (e.g. en)
	//  * @return array Data
	//  */
	// public function getTranslations($lang)
	// {
	// 	$baseDir = str_replace("{lang}", $lang, $this->config['dest']);
	//
	// 	$files = $this->getFiles($baseDir);
	//
	// 	$data = array();
	// 	foreach($files as $file) {
	//
	// 		// get the {collection} from /path/to/files/{collection}.php
	// 		preg_match("/\/([A-Za-z0-9_\-\.]*)\.php$/", $file, $parts);
	// 		$collection = @$parts[1];
	//
	// 		if ($collection) {
	//
	// 			$filter = $this->getFilter();
	//
	// 			// get the data from the dest file, or set to null
	// 			// null if the file exists is safer, sometimes in error null will get
	// 			// written to the text file. In either case, we got it covered
	// 			$destText = file_get_contents($file);
	// 			$data[$collection] = $filter->decode($destText);
	// 		}
	// 	}
	//
	// 	return $data;
	// }

	/**
	 * Write the data to file
	 * @param string $file File to write e.g. "/path/to/lang/en/messages.php"
	 * @param array $data Data to write
	 * @return void
	 */
	public function writeDataToFile($file, $data)
	{
		$data = '<'.'?php' . PHP_EOL .
        PHP_EOL .
        'return ' . var_export($data, true) . ';';

		// TODO this is the only place we use encode, get rid of filter ne
		file_put_contents($file, $data);
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
			if (is_dir($dir)) {
				$dir = rtrim($dir, '\\/');
				$files = array_merge($files, $this->_getFilesRecursive($dir));
			} elseif (is_file($dir)) {
				array_push($files, $dir); // $dir is a file
			}
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
