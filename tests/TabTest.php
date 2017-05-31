<?php
class TabTest extends PHPUnit_Framework_TestCase {
	private $pdo;
	private $cache;
	public function setUp() {
		try {
			$this->cache = new \Tian\Memcache([
					'host'     => '192.168.33.10',
					'port'     => 11111,
			]);
			
			
			$this->pdo = new Tian\Connection\MysqlPdoConn ( [ 
					'host' => '127.0.0.1',
					'port' => 3306,
					'user' => 'root',
					'password' => 'root',
					'charset' => 'utf8',
					'database' => 'garri' 
			] );
			$this->pdo->exec ( "
			CREATE TABLE `gg` (
			  `pk1` INT(10) UNSIGNED NOT NULL,
			  `pk2` INT(10) UNSIGNED NOT NULL,
			  `fint` INT(10) UNSIGNED DEFAULT '16',
			  `data` VARCHAR(10) DEFAULT NULL,
			  `fenum` ENUM('aa','bb') DEFAULT NULL COMMENT 'comment_enum',
			  `fset` SET('a','bc') DEFAULT NULL,
			  `notnullable` TEXT NOT NULL,
			  PRIMARY KEY (`pk1`,`pk2`),
			  UNIQUE KEY `fint` (`fint`)
			) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='gg_comment';
			
			CREATE TABLE `g` (
			  `pk1` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `fint` int(10) unsigned DEFAULT '16',
			  `data` varchar(10) DEFAULT NULL,
			  PRIMARY KEY (`pk1`),
			  UNIQUE KEY `fint` (`fint`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='gg_comment'
					
		" );
		} catch ( \Exception $e ) {
			print $e->getMessage ();
			exit ();
		}
		
		// var_dump($this->pdo);
	}
	public function tearDown() {
		$this->pdo->exec ( 'DROP TABLE `gg`' );
		$this->pdo->exec ( 'DROP TABLE `g`' );
	}
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testGetColumnNames() {
		$demo = new \Tian\MysqlTableReflection('gg',$this->pdo,$this->cache);
		$d = $demo->getColumnNames();
// 		var_dump($d);
		$this->assertArraySubset(['pk1','pk2','fint'], $d);
	}
	
	public function testGet() {
		$demo = new \Tian\MysqlTableReflection('gg',$this->pdo,$this->cache);
		$this->assertEquals('comment_enum', $demo->getComment('fenum'));
		$this->assertEquals('16', $demo->getDefault('fint'));
		$this->assertEquals('10', $demo->getLen('data'));
		$this->assertEquals(['pk1','pk2'], $demo->getPk('data'));
		$this->assertEquals('gg_comment',$demo->getTableComment());
		$this->assertEquals('gg',$demo->getTableName());
		$this->assertEquals('int',$demo->getType('fint'));
		$this->assertEquals('text',$demo->getType('notnullable'));
		$this->assertEquals('InnoDB',$demo->getEngineType());
		$this->assertFalse($demo->isAutoIncrement('pk1'));
		$this->assertTrue($demo->isNullField('data'));
		$this->assertTrue(!$demo->isNullField('notnullable'));
		$this->assertTrue($demo->isUnsiged('fint'));
	}
	
	public function testAuto () {
		$demo = new \Tian\MysqlTableReflection('g',$this->pdo,$this->cache);
		$this->assertTrue($demo->isAutoIncrement('pk1'));
		$this->assertTrue($demo->isUnique('fint'));
	}
}


