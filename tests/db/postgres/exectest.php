<?php

use cyclone as cy;
use cyclone\db;

require_once cy\FileSystem::get_root_path('db') . 'tests/db/postgres/dbtest.php';

class DB_Postgres_ExecTest extends DB_Postgres_DbTest {

    public function testExecSelect() {
        $arr = cy\DB::select()->from('users')->exec('cytst-postgres')->as_array();
        $this->assertEquals(array(
            array('id' => 1, 'name' => 'user1'),
            array('id' => 2, 'name' => 'user2')
        ), $arr);

        $result = cy\DB::select()->from('users')->exec('cytst-postgres')->index_by('id');
        $exp_result = array(
            1 => array('id' => 1, 'name' => 'user1'),
            2 => array('id' => 2, 'name' => 'user2')
        ); 
        $cnt = 1;
        foreach ($result as $id => $row) {
            $this->assertEquals($cnt, $id);
            $this->assertEquals($exp_result[$cnt], $row);
            ++$cnt;
        }
        // we iterate again to check if rewind() works properly
        $cnt = 1;
        foreach ($result as $id => $row) {
            $this->assertEquals($cnt, $id);
            $this->assertEquals($exp_result[$cnt], $row);
            ++$cnt;
        }
    }

    public function testExecInsert() {
        cy\DB::delete('users')->exec('cytst-postgres');
        $id = cy\DB::insert('users')->values(array('name' => 'user3'))->exec('cytst-postgres');

        $this->assertEquals(3, $id);

        $id = cy\DB::insert('serusers')->values(array('name' => 'user1'))->exec('cytst-postgres');
        $this->assertEquals(3, $id);

        $id = cy\DB::insert('users')->values(array('name' => 'user2'))->exec('cytst-postgres', FALSE);
        $this->assertNull($id);
    }

    public function testExecDelete() {
        $affected = cy\DB::delete('users')->where('id', '=', cy\DB::esc(1))->exec('cytst-postgres');
        $this->assertEquals(1, $affected);
        
        $result = pg_query('select count(1) cnt from users');
        $row = pg_fetch_assoc($result);
        $this->assertEquals(1, $row['cnt']);
    }

    public function testExecUpdate() {
        $affected = cy\DB::update('users')->values(array('name' => 'user2_mod'))
                ->where('id', '=', cy\DB::esc(2))->exec('cytst-postgres');

        $this->assertEquals(1, $affected);

        $result = pg_query('select name from users where id = 2');
        $row = pg_fetch_assoc($result);
        $this->assertEquals('user2_mod', $row['name']);
    }

    public function testUniqueConstraintException() {
        $thrown = FALSE;
        try {
            cy\DB::insert('users')
                ->values(array('name' => 'u'))
                ->values(array('name' => 'u'))->exec('cytst-postgres');
        } catch (db\ConstraintException $ex) {
            $this->assertEquals('users_name_key', $ex->constraint_name);
            $this->assertEquals(db\ConstraintException::UNIQUE_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('name', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException thrown');
    }

    public function testNotNullConstraintException() {
        $thrown = FALSE;
        try {
            cy\DB::insert('users')->values(array('name' => null))->exec('cytst-postgres');
        } catch (db\ConstraintException $ex) {
            $this->assertEquals(db\ConstraintException::NOTNULL_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('name', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException thrown');
    }

}
