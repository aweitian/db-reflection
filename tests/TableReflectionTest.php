<?php

class TableReflectionTest extends PHPUnit_Framework_TestCase
{
    private $con;
    private $cache;

    public function init()
    {
        $this->con = new Aw\Db\Connection\Mysql (array(
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'garri',
            'user' => 'root',
            'password' => 'root',
            'charset' => 'utf8'
        ));

//        $sql = "CREATE DATABASE IF NOT EXISTS `garri` CHARACTER SET UTF8 COLLATE utf8_general_ci;";
//        $this->con->exec($sql);

        $this->con->exec("
            CREATE TABLE `gg` (
              `pk1` int(10) unsigned NOT NULL,
              `pk2` int(10) unsigned NOT NULL,
              `fint` int(10) unsigned DEFAULT '16',
              `data` varchar(10) DEFAULT NULL,
              `fenum` enum('aa','bb') DEFAULT NULL COMMENT 'comment_enum',
              `fset` set('a','bc') DEFAULT NULL,
              `notnullable` text NOT NULL,
              PRIMARY KEY (`pk1`,`pk2`),
              UNIQUE KEY `fint` (`fint`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='gg_comment';
			
			CREATE TABLE `schedules` (
			  `schedeles_id` int(11) NOT NULL AUTO_INCREMENT,
			  `schedeles_date` date NOT NULL,
			  `schedeles_doc` int(11) NOT NULL,
			  `schedeles_status` int(11) NOT NULL,
			  PRIMARY KEY (`schedeles_id`),
			  KEY `schedeles_doc` (`schedeles_doc`)
			) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8
		");
// 		$this->cache = new \Tian\Memcache ( [ 
// 				'host' => '192.168.33.10',
// 				'port' => 11111 
// 		] );
        $this->cache = null;
    }

    public function clean()
    {
        $this->con->exec("
			DROP TABLE `gg`;
			DROP TABLE `schedules`;
		");
    }

    public function testPk()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $this->assertArraySubset(array(
            'pk1',
            'pk2'
        ), $info->getPk());

        $info = new \Aw\Db\Reflection\Mysql\Table ('schedules', $this->con, $this->cache);
        $this->assertEquals('schedeles_id', $info->getPk());

        $this->clean();
    }

    public function testCol()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $this->assertArraySubset(array(
            'pk1',
            'pk2',
            'fint',
            'data',
            'fenum',
            'fset',
            'notnullable',
        ), $info->getColumnNames());
        $this->clean();
    }

    public function testComment()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $this->assertEquals('gg_comment', $info->getTableComment());
        $this->clean();
    }

    public function testTableType()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $this->assertEquals('InnoDB', $info->getEngineType());
        $this->clean();
    }

    public function testType()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $this->assertEquals('enum', $info->getType('fenum'));
        $this->clean();
    }

    public function testLen()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $this->assertEquals('10', $info->getLen('data'));
        $this->clean();
    }

    public function testisunsigned()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $this->assertTrue($info->isUnsiged('fint'));
        $this->clean();
    }

    public function testisNullField()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $this->assertTrue($info->isNullField('data'));
        $this->assertNotTrue($info->isNullField('notnullable'));
        $this->clean();
    }


    public function testgetDefault()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table('gg', $this->con, $this->cache);
        $this->assertEquals('16', $info->getDefault('fint'));
        $this->clean();
    }

    public function testisPk()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $this->assertTrue($info->isPk('pk1'));
        $this->clean();
    }

    public function testisAutoIncrement()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('schedules', $this->con, $this->cache);
        $this->assertTrue($info->isAutoIncrement('schedeles_id'));
        $this->clean();
    }

    public function testAllComment()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $comments = $info->getComments();
        $this->assertEquals($comments["fenum"], "comment_enum");
        $this->clean();
    }

    public function testAllDefault()
    {
        $this->init();
        $info = new \Aw\Db\Reflection\Mysql\Table ('gg', $this->con, $this->cache);
        $values = $info->getDefaults();
//        var_dump($values);
        $this->assertEquals($values["fint"], "16");
        $this->clean();
    }
}


