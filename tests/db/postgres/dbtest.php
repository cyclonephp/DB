<?php

use cyclone as cy;
use cyclone\db;


abstract class DB_Postgres_DbTest extends Kohana_Unittest_TestCase {

    public function setUp() {
        parent::setUp();
        try {
            $sql = file_get_contents(cy\LIBPATH . 'db/tests/schema/postgres.sql');
            cy\DB::executor('cytst-postgres')->exec_custom($sql);
        } catch (db\Exception $ex) {
            error_log($ex->getMessage());
            $this->markTestSkipped();
        }
    }

    
    
}