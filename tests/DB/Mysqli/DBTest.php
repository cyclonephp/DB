<?php

use cyclone as cy;
use cyclone\db;

abstract class DB_Mysqli_DbTest extends Kohana_Unittest_TestCase {

    public function setUp() {
        cy\DB::clear_connections();
        try {
            cy\DB::query('truncate cy_user')->exec('cytst-mysqli');
            $names = array('user1', 'user2');
            $insert = cy\DB::insert('user');
            foreach ($names as $name) {
                $insert->values(array('name' => $name));
            }
            $insert->exec('cytst-mysqli');

            cy\DB::query('truncate cy_user_email')->exec('cytst-mysqli');
            cy\DB::query('truncate cy_posts')->exec('cytst-mysqli');
        } catch (db\Exception $ex) {
            error_log($ex->getMessage() . ' class: ' . get_class($this));
            $this->markTestSkipped('skipping simpledb tests');
        }
    }

    public function tearDown() {
        DB::clear_connections();
    }
}