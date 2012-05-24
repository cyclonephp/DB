<?php

use cyclone as cy;
use cyclone\db;

require_once cy\FileSystem::get_root_path('db') . 'tests/DB/Postgres/DBTest.php';

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
        $result = cy\DB::insert('users')->values(array('name' => 'user3'))
            ->returning('id')
            ->exec('cytst-postgres');
        $this->assertInstanceOf('cyclone\\db\\StmtResult', $result);
        $this->assertEquals(3, $result->rows[0]['id']);

        $result = cy\DB::insert('serusers')->values(array('name' => 'user4'))
            ->values(array('name' => 'user5'))
            ->returning('id')
            ->exec('cytst-postgres');
        $this->assertEquals(3, $result->rows[0]['id']);
        $this->assertEquals(4, $result->rows[1]['id']);
    }

    public function testExecDelete() {
        $result = cy\DB::delete('users')
            ->where('id', '=', cy\DB::esc(1))
            ->returning('name')
            ->exec('cytst-postgres');
        $this->assertInstanceOf('cyclone\\db\\StmtResult', $result);
        $this->assertEquals(1, $result->affected_row_count);
        $this->assertEquals('user1', $result->rows[0]['name']);
        
        $result = pg_query('select count(1) cnt from users');
        $row = pg_fetch_assoc($result);
        $this->assertEquals(1, $row['cnt']);
    }

    public function testExecUpdate() {
        $result = cy\DB::update('users')->values(array('name' => 'user2_mod'))
                ->where('id', '=', cy\DB::esc(2))
                ->returning('name')
                ->exec('cytst-postgres');
        $this->assertInstanceOf('cyclone\\db\\StmtResult', $result);
        $this->assertEquals(1, $result->affected_row_count);
        $this->assertEquals('user2_mod', $result->rows[0]['name']);

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

    public function testForeignKeyConstraintException() {
        $thrown = FALSE;
        try {
            cy\DB::insert('user_emails')->values(array(
                'user_fk' => 5,
                'email' => 'somebody@example.org'
            ))->exec('cytst-postgres');
        } catch (db\ConstraintException $ex) {
            $this->assertEquals(db\ConstraintException::FOREIGNKEY_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('user_fk', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException thrown');
    }

}
