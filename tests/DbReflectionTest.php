<?php

class DbReflectionTest extends PHPUnit_Framework_TestCase
{
    private $con;

    public function init()
    {
        //echo "setup";
        $this->con = new Aw\Db\Connection\Mysql(array(
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'garri',
            'user' => 'root',
            'password' => 'root',
            'charset' => 'utf8'
        ));

        $sql = "CREATE DATABASE IF NOT EXISTS `garri` CHARACTER SET UTF8 COLLATE utf8_general_ci;";
        $this->con->exec($sql);

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

    public function clean()
    {
        //echo "tear down";
        $this->con->exec("
			DROP TABLE `gg`;
			DROP TABLE `schedules`;
		");
    }

    public function testMysqlReflection()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Db($this->con, null);
        $this->assertTrue($info->tableExists('gg'));
        $this->assertTrue($info->tableExists('schedules'));
        $this->assertTrue(!$info->tableExists('lol'));
        $this->clean();
    }
}


