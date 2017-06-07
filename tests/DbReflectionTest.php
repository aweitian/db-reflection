<?php
class DbReflectionTest extends PHPUnit_Framework_TestCase {
	private $con;
	public function setUp()
	{
		$this->con = new \Tian\Connection\MysqlPdoConn([
				'host' => '127.0.0.1',
				'database' => 'garri',
				'user' => 'root',
				'password' => 'root',
				'charset' => 'utf8'
		]);
		$this->con->exec('
			CREATE TABLE `gg` (
				`pk1` int(10) unsigned NOT NULL,
				`pk2` int(10) unsigned NOT NULL,
				`data` varchar(10) DEFAULT NULL,
				PRIMARY KEY (`pk1`,`pk2`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			CREATE TABLE `schedules` (
			  `schedeles_id` int(11) NOT NULL AUTO_INCREMENT,
			  `schedeles_date` date NOT NULL,
			  `schedeles_doc` int(11) NOT NULL,
			  `schedeles_status` int(11) NOT NULL,
			  PRIMARY KEY (`schedeles_id`),
			  KEY `schedeles_doc` (`schedeles_doc`)
			) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8
		');

	}
	
	public function tearDown()
	{
		$this->con->exec("
			DROP TABLE `gg`;
			DROP TABLE `schedules`;
		");
	}
	public function testMysqlReflection() {
		$cache = new \Tian\Memcache([
				'host'     => '192.168.33.10',
				'port'     => 11111,
		]);
		$info = new \Tian\MySqlDbReflection($this->con,$cache);
		$this->assertTrue($info->tableExists('gg'));
		$this->assertTrue($info->tableExists('schedules'));
		$this->assertTrue(!$info->tableExists('lol'));
	}
}


