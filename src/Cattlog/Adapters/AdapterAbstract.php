<?php namespace Cattlog\Adapters;

use Cattlog\ConfigTrait;
use Cattlog\FileSystem;

class AdapterAbstract implements AdapterInterface
{
	use ConfigTrait;

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
		// we'll use file system for any file access related stuff
		// also by passing it in, let's us test the class more effectively
		$this->fileSystem = $fileSystem;
	}
}
