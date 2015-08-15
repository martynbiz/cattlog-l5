<?php namespace Cattlog\Filter;

abstract class FilterAbstract implements FilterInterface
{
	protected $config;

	public function __construct($config=array())
	{
		$this->config = $config;
	}
}
