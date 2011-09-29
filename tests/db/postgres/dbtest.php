<?php

use cyclone as cy;
use cyclone\db;


abstract class DB_Postgres_DbTest extends Kohana_Unittest_TestCase {

    public function setUp() {
        parent::setUp();
        try {
            $sql = file_get_contents(cy\LIBPATH . 'db/tests/pg_test.sql');
            //die("ott\n");
            cy\DB::executor('postgres')->exec_custom($sql);
        } catch (db\Exception $ex) {
            error_log($ex->getMessage());
            $this->markTestSkipped();
        }
    }

    
    
}