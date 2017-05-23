<?php

/**
 * @author: awei.tian
 * @date: 2013-11-9
 * @function:
 */
// show table status from amzphp where name='ebank';
// +-------+--------+---------+------------+------+----------------+-------------+-----------------+--------------+-----------+----------------+---------------------+---------------------+------------+-----------------+----------+----------------+----------+
// | Name | Engine | Version | Row_format | Rows | Avg_row_length | Data_length |Max_data_length | Index_length | Data_free | Auto_increment | Create_time | Update_time | Check_time | Collation | Checksum | Create_options | Comment |
// +-------+--------+---------+------------+------+----------------+-------------+-----------------+--------------+-----------+----------------+---------------------+---------------------+------------+-----------------+----------+----------------+----------+
// | ebank | MyISAM | 10 | Dynamic | 3 | 1586 | 4760 |281474976710655 | 2048 | 0 | 4 | 2013-09-14 14:37:58 | 2013-09-14 14:44:19 | NULL | utf8_unicode_ci | NULL | | 电子银行 |
// +-------+--------+---------+------------+------+----------------+-------------+-----------------+--------------+-----------+----------------+---------------------+---------------------+------------+-----------------+----------+----------------+----------+

// SHOW COLUMNS FROM device_info
// +-------+-------------+------+-----+---------+----------------+
// | Field | Type | Null | Key | Default | Extra |
// +-------+-------------+------+-----+---------+----------------+
// | sid | int(11) | NO | PRI | NULL | auto_increment |
// | vv | varchar(50) | YES | | NULL | |
// +-------+-------------+------+-----+---------+----------------+
namespace Tian;

class MysqlTableReflection implements ITableReflection {
	/**
	 *
	 * @var \Tian\Connection\IConnection
	 */
	private $connection;
	/**
	 *
	 * @var \Tian\ICache
	 */
	private $cache;
	/**
	 *
	 * @var string
	 */
	private $tabname;
	/**
	 * 初始值 为NULL,初始化以后为array
	 *
	 * @var array
	 */
	private static $descriptions = null;
	/**
	 * 初始值 为NULL,初始化以后为array
	 *
	 * @var array
	 */
	private static $key_descriptions = null;
	public function __construct($tabname, \Tian\Connection\IConnection $connection, \Tian\ICache $cache = null) {
		$this->connection = $connection;
		$this->cache = $cache;
		$this->tabname = $tabname;
	}
	public function cacheKeyKeyDesc() {
		return 'Tian.MysqlTableReflection.key_descriptions.' . $this->tabname;
	}
	public function cacheKeyDesc() {
		return 'Tian.MysqlTableReflection.descriptions.' . $this->tabname;
	}
	public function getPk() {
		$this->initTableKeyDecription ();
		$ret = [ ];
		foreach ( self::$key_descriptions as $val ) {
			if ($val ["Key"] == "PRI")
				$ret [] = $val ["Field"];
		}
		return $ret;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see ITableInfo::getColumnNames()
	 */
	public function getColumnNames() {
		$this->initTableKeyDecription ();
		$ret = array ();
		foreach ( self::$key_descriptions as $val ) {
			$ret [] = $val ["Field"];
		}
		return $ret;
	}
	public function getEngineType() {
		$this->initTableDecription ();
		return self::$descriptions ["Engine"];
	}
	public function getComment() {
		$this->initTableDecription ();
		return self::$descriptions ["Comment"];
	}
	protected function initTableDecription() {
		if (is_array ( self::$descriptions )) {
			return;
		}
		if (! is_null ( $this->cache )) {
			$ret = $this->cache->get ( $this->cacheKeyDesc () );
			if (is_array ( $ret )) {
				self::$descriptions = $ret;
				return;
			}
		}
		$result = $this->connection->fetch ( "show table status from `" . $this->connection->getDbName () . "` where name=:tablename", [ 
				"tablename" => $this->tabname 
		] );
		self::$descriptions = $result;
		if (! is_null ( $this->cache )) {
			$this->cache->set ( $this->cacheKeyDesc (), $result , 0 );
		}
		return;
	}
	protected function initTableKeyDecription() {
		if (is_array ( self::$key_descriptions )) {
			return;
		}
		if (! is_null ( $this->cache )) {
			$ret = $this->cache->get ( $this->cacheKeyKeyDesc () );
			if (is_array ( $ret )) {
				self::$key_descriptions = $ret;
				return;
			}
		}
		$result = $this->connection->fetchAll ( "SHOW COLUMNS FROM `$this->tabname`" );
		if (count ( $result ) == 0) {
			self::$key_descriptions = [ ];
			if (! is_null ( $this->cache )) {
				$this->cache->set ( $this->cacheKeyKeyDesc (), [ ], 0 );
			}
			return;
		}
		self::$key_descriptions = $result;
		if (! is_null ( $this->cache )) {
			$this->cache->set ( $this->cacheKeyKeyDesc (), $result, 0 );
		}
	}
}