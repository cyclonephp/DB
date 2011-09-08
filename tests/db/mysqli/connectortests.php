<?php

use cyclone as cy;
use cyclone\db;

class DB_MySQLi_ConnectorTest extends Kohana_Unittest_TestCase {

    public function testPersistentConnection() {

       $test = new db\connector\MySQLi(array(
    'adapter' => 'mysqli',
    'prefix' => 'cy_',
    'connection' => array(
        'username' => 'simpledb',
        'password' => 'simpledb',
        'database' => 'simpledb',
        'host' => 'localhost',
        'presistent' => TRUE
    ))
);


    }

}

?>
