<?php namespace Cattlog;

// as this tool is for use within Laravel, we'll just use some of it's array helpers
use Illuminate\Support\Arr;

class Cattlog
{
	/**
	 * @var array $config Config passed in
	 */
	protected $config;

	/**
	 * @var FileSystem $fileSystem FileSystem object to access files/dirs
	 */
	protected $fileSystem;

	/**
	 * @param FileSystem $fileSystem For any file access requests (eg. get array of source files)
	 * @param array $config Config for the class
	 */
	public function __construct(FileSystem $fileSystem, $config=array())
	{
		$this->fileSystem = $fileSystem;

		$this->config = $config;
	}

	/**
	 * Will remove an array of keys from data
	 * @param array $data Data to remove keys from
	 * @param string|array $keys Keys to remove from data
	 * @return array Data with keys removed
	 */
	public function removeKeys($data, $keys)
	{
		// ensure keys is an array
		if (! is_array($keys))
			$keys = array($keys);

		// loop through each key and remove it
		foreach ($keys as $key) {
			Arr::forget($data, $key);
		}

		// Tidy up empty arrays
		$data = $this->removeEmptyKeys($data);

		return $data;
	}

	/**
	 * Will remove empty keys from data recursively. Useful after
	 * removing keys and empty arrays remain
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
		// ensure keys is an array
		if (! is_array($keysToAdd))
			$keysToAdd = array($keysToAdd);

		// loop through each key and add it
		// only add if it doesn't exist, just encase we accidentally overwrite
		foreach ($keysToAdd as $key) {

			// use Laravel's array_get to check if the element exists using dot notation
			if(! Arr::has($data, $key))
				$this->setValue($data, $key, '');
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
	 * Set key in $data array with dot notation
	 * @param array $data Data to add keys to
	 * @param array $key Keys to add to data
	 * @param array $options Options to e.g. set new keys
	 * @return array The data array passed in
	 */
	public function setValue(&$data, $key, $newValue, $options=array())
	{
		// default options
		$options = array_merge(array(
			'create' => true, // create new, if none exist
			'overwrite' => true, // overwrite existing value
		), $options);

		// use Laravel's array_get to check if the element exists using dot notation
		if(Arr::has($data, $key)) {
			if ($options['overwrite']) {
				Arr::set($data, $key, $newValue);
			}
		} elseif ($options['create']) {
			Arr::set($data, $key, $newValue);
		}

		return $data;
	}

	/**
	 * Get key in $data array with dot notation
	 * @param array $data Data to add keys to
	 * @param array $key Keys to add to data
	 * @return array The data array passed in
	 */
	public function getValue($data, $key)
	{
		return Arr::get($data, $key);
	}

	/**
	 * Check whether key exists in $data
	 * @param array $data Data to add keys to
	 * @param array $key Keys to add to data
	 * @return boolean True if exists
	 */
	public function hasKey($data, $key)
	{
		return Arr::has($data, $key);
	}

	/**
	 * Get the keys from source directories
	 * @return array Keys in an indexed array
	 */
	public function getKeysFromSrcFiles()
	{
		// get files in dir
		$files = $this->fileSystem->getSrcFiles();

		// for each file, get the key in string format
		$keys = array();
		foreach ($files as $file) {

			// get the contents of the file
			$contents = $this->fileSystem->getFileContents($file);

			// regex on it to get all the matches
			preg_match_all($this->config['pattern'], $contents, $matches);

			// put the matches into
			$keys = array_merge($matches[1], $keys);
		}

		// array_flip will deal with the duplicates
		return $keys;
	}

	/**
	 * Get the keys from source directories.
	 * @return array Keys in an indexed array
	 */
	public function getKeysWithValuesFromDestFiles($lang)
	{
		// get files in dir
		$files = $this->fileSystem->getDestFiles($lang);

		// for each file, get the key in string format
		$keys = array();
		foreach ($files as $file) {

			// get the {prefix} from /path/to/files/{prefix}.php
			preg_match("/\/([A-Za-z0-9_\-\.]*)\.php$/", $file, $parts);
			$prefix = @$parts[1] . '.';

			// flatten to get keys such as "between.numeric", then take the
			// keys only (array_keys), and merge with existing (array_merge)
			$data = $this->fileSystem->getFileData($file);
			$flattened = Arr::dot($data, $prefix);
			$keys = array_merge($flattened, $keys);
		}


		return $keys;
	}

	/**
	 * Get the keys from source directories.
	 * @return array Keys in an indexed array
	 */
	public function getKeysFromDestFiles($lang)
	{
		// get files in dir
		$data = $this->getKeysWithValuesFromDestFiles($lang);

		return array_keys($data);
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
	//  * Will recursively count keys in a
	//  * @param array Ungrouped array of keys
	//  * @return array Grouped keys
	//  */
	// public function countKeys($data)
	// {
	// 	$grouped = array();
	// 	foreach ($keys as $key) {
	// 		$parts = explode('.', $key);
	// 		$file = array_shift($parts);
	//
	// 		// ensure this file is an array already
	// 		if (!isset($grouped[$file]) or !is_array($grouped[$file]))
	// 			$grouped[$file] = array();
	//
	// 		array_push($grouped[$file], implode('.', $parts));
	// 	}
	//
	// 	return $grouped;
	// }
}
