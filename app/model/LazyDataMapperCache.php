<?php

class LazyDataMapperCache implements \LazyDataMapper\IExternalCache
{

	/** @var \Nette\Caching\Cache */
	private $cache;


	public function __construct(\Nette\Caching\IStorage $storage)
	{
		$this->cache = new \Nette\Caching\Cache($storage, 'ldm');
	}


	public function load($key)
	{
		return $this->cache->load($key);
	}


	public function save($key, $value)
	{
		$this->cache->save($key, $value);
	}
}
