<?php


// Path to Nette's cache directory.
// Change it, if this file is store not in /project/app/common/
define('NetteCacheDirectory', realpath(__DIR__ . '/../../temp/cache/'));


use Nette\Caching\Cache,
  Nette\Caching\Storages\FileJournal,
	Nette\Caching\Storages\FileStorage;


/**
 * Provides simple access to Nette's cache
 * and parsing Nette's models in to a simple array of data.
 *
 * https://github.com/ikenni/nette.extensions
 *
 * @author     Chernyavskiy Andrew (ikenni@gmail.com)
 * @package    NetteExtension
 */
class CacheHelpers {


	/**
	 * Returns cache of specified container.
	 *
	 * @param  string
	 * @return cache
	 */
	public static function load( $container ) {

		$journal = new FileJournal(NetteCacheDirectory . '/..');
		$storage = new FileStorage(NetteCacheDirectory, $journal);
		$cache = new Cache($storage, $container);

		return $cache;
	}



	/**
	 * Cleans cache storage, specified by name.
	 *
	 * @param  string
	 * @return void
	 */
	public static function clear( $CacheStorage ) {
		
		$cache = self::load( $CacheStorage );
		$cache->clean(array(Cache::TAGS => $CacheStorage));

		unset($cache);
		unset($CacheStorage);

	}



	/**
	 * This function will parse Nette's model in to a simple array of data.
	 *
	 * @param  mixed
	 * @return array
	 */
	public static function parse($array) {

		// Sometimes, there is not data in model.
		if ($array === false) {
			return array();
		}

		// Deciding which way to process by checking class name of model
		if (get_class($array) == 'Nette\Database\Table\ActiveRow' or get_class($array) == 'ArrayHash') {
			return self::ParseSingle($array);
		}

		// Parsing
		$Result = array();
		foreach ($array as $row) {
			$tmp = array();
			foreach ($row as $key => $value) { $tmp[$key] = $value; }
			$Result[] = $tmp;
			unset($tmp);
		}

		unset($array);

		return $Result;
	}



	/**
	 * This function will be called by self:parse(), if you have single row query-result
	 *
	 * @param  mixed
	 * @return array
	 */
	public static function parseSingle($Array) {
		
		$Result = array();
		foreach ($Array as $key => $value) {
			$Result[$key] = $value;
		}

		return $Result;
	}



}
