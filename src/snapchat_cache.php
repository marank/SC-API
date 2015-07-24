<?php

/**
 * @file
 *   Provides a storage class for the high-level Snapchat object. Caching
 *   results prevents unnecessary requests to the API.
 */
class SnapchatCache {

	const CACHE_FILE = "cache.dat";

	/**
	 * The lifespan of the data in seconds. This might be able to be customized
	 * at some point in the future.
	 */
	const LIFESPAN = 600;

	/**
	 * The cache data itself.
	 */
	private $_cache = array();

	public function __construct() {
		if (file_exists(self::CACHE_FILE)) {
			$this->_cache = unserialize(file_get_contents(self::CACHE_FILE));
		}
	}

	/**
	 * Gets a result from the cache if it's fresh enough.
	 *
	 * @param string $key
	 *   The key of the result to retrieve.
	 *
	 * @return mixed
	 *   The result or FALSE on failure.
	 */
	public function get($key, $ignoreLifespan = false) {
		// First, check to see if the result has been cached.
		if (!isset($this->_cache[$key])) {
			return FALSE;
		}

		// Second, check its freshness.
		if (($this->_cache[$key]['time'] < time() - self::LIFESPAN) && !$ignoreLifespan) {
			//unset($this->_cache[$key]);
			return FALSE;
		}

		return $this->_cache[$key]['data'];
	}

	/**
	 * Adds a result to the cache.
	 *
	 * @param string $key
	 *   The key of the result to store.
	 * @param mixed $data
	 *   The data to store.
	 */
	public function set($key, $data) {
		$this->_cache[$key] = array(
			'time' => time(),
			'data' => $data,
		);
		file_put_contents(self::CACHE_FILE, serialize($this->_cache));
	}

	/**
	 * Clears the cache.
	**/
	public function clear() {
		unset($this->_cache);
		$this->_cache = array();
		file_put_contents(self::CACHE_FILE, serialize($this->_cache));
	}

}
