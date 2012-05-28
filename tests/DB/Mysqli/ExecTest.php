<?php

use cyclone as cy;
use cyclone\db;

class DB_Mysqli_ExecTest extends DB_MySQLi_DbTest {

    /**
     *
     * @expectedException cyclone\db\Exception
     */
    public function test_exec_update() {
        $result = cy\DB::update('user')->values(array('name' => 'crystal88_'))
                ->exec('cytst-mysqli');
        $this->assertInstanceOf('cyclone\\db\\StmtResult', $result);
        $this->assertEquals($result->affected_row_count, 2);
        cy\DB::update('users')->values(array('name' => 'crystal88_'))->exec('cytst-mysqli');
    }

    public function test_exec_update_returning() {
        $result = cy\DB::update('user')->values(array('email' => 'crystal@example.org'))
            ->returning('name')->exec('cytst-mysqli');
        $this->assertEquals(array(
            array('name' => 'user1'),
            array('name' => 'user2')
        ), $result->rows);
    }

    /**
     *
     * @expectedException cyclone\db\Exception
     */
    public function test_exec_delete() {
        $result = cy\DB::delete('user')->exec('cytst-mysqli');
        $this->assertInstanceOf('cyclone\\db\\StmtResult', $result);
        $this->assertEquals($result->affected_row_count, 2);
        cy\DB::delete('users')->exec('cytst-mysqli');
    }

    /**
     *
     * @expectedException cyclone\db\Exception
     */
    public function test_exec_insert() {
        $insert_id = cy\DB::insert('user')->values(array('name' => 'crystal'))
                ->returning('id')
                ->exec('cytst-mysqli')->rows[0]['id'];
        $this->assertEquals(3, $insert_id);
        $insert_id = cy\DB::insert('user')->values(array('name' => 'crystal'))
                ->values(array('name' => 'crystal'))
                ->returning('id')
                ->exec('cytst-mysqli')->rows[0]['id'];
        $this->assertEquals(4, $insert_id);
        cy\DB::insert('users')->values(array('name' => 'crystal'))->exec('cytst-mysqli');
    }

    /**
     *
     */
    public function test_exec_insert_returning() {
        $result = cy\DB::insert('user')->values(array(
            'name' => 'crystal',
            'email' => 'crystal@example.org')
        )->returning('id', 'name', 'email')->exec('cytst-mysqli');
        $this->assertEquals(array(
            'id' => '3',
            'name' => 'crystal',
            'email' => 'crystal@example.org'
        ), $result->rows[0]);
    }

    public function test_exec_select() {
        $names = array('user1', 'user2');
        $result = cy\DB::select()->from('user')
                ->exec('cytst-mysqli');
        $this->assertInstanceOf('cyclone\db\query\result\MySQLi', $result);
        $this->assertEquals(2, $result->count());
        $idx = 0;
        foreach ($result as $v) {
            $this->assertEquals($v['name'], $names[$idx++]);
        }
        $result = cy\DB::select()->from('user')->exec('cytst-mysqli');
        $result->rows('stdClass');
        $idx = 0;
        foreach ($result as $v) {
            $this->assertEquals($v->name, $names[$idx++]);
        }
        $result = cy\DB::select()->from('user')->exec('cytst-mysqli')
                ->index_by('name')->rows('stdClass');
        $idx = 0;
        foreach ($result as $k => $v) {
            $this->assertEquals($v->name, $names[$idx]);
            $this->assertEquals($k, $names[$idx++]);
        }
    }

    public function test_exec_multiquery() {
        $result1 = cy\DB::select()->from('user')->exec('cytst-mysqli');
        $result2 = cy\DB::select()->from('user')->exec('cytst-mysqli');
        foreach ($result1 as $k => $v) {

        }

        cy\DB::executor('cytst-mysqli')->exec_custom('select 2');

        cy\DB::executor('cytst-mysqli')->exec_custom('drop table if exists t_posts; create table t_posts(id int);');
        cy\DB::connector('cytst-mysqli')->disconnect();
        cy\DB::connector('cytst-mysqli')->connect();
    }

    public function test_exec_custom() {
        cy\DB::executor('cytst-mysqli')->exec_custom('create table if not exists tmp (id int)');
    }

    public function test_as_array() {
        $names = array('user1', 'user2');
        $result = cy\DB::select()->from('user')->exec('cytst-mysqli')->index_by('name')->rows('stdClass')->as_array();
        $this->assertEquals(count($result), 2);
        $idx = 0;
        foreach ($result as $k => $v) {
            $this->assertEquals($v->name, $names[$idx]);
            $this->assertEquals($k, $names[$idx++]);
        }
    }

    public function test_commit_rollback() {
        $existing_rows = cy\DB::select()->from('user')->exec('cytst-mysqli')->count();
        $this->assertEquals(2, $existing_rows);
        $conn = cy\DB::connector('cytst-mysqli');
        $conn->start_transaction();
        $deleted_rows = cy\DB::delete('user')->exec('cytst-mysqli');
        $this->assertEquals(2, $deleted_rows->affected_row_count);
        $conn->rollback();
        $existing_rows = cy\DB::select()->from('user')->exec('cytst-mysqli')->count();
        $this->assertEquals(2, $existing_rows);
        $conn->start_transaction();
        $deleted_rows = cy\DB::delete('user')->exec('cytst-mysqli');
        $this->assertEquals(2, $deleted_rows->affected_row_count);
        $conn->commit();
        $existing_rows = cy\DB::select()->from('user')->exec('cytst-mysqli')->count();
        $this->assertEquals(0, $existing_rows);
    }

    public function test_transaction_success() {
        $tx = new db\Transaction;
        $tx []= cy\DB::delete('user')->limit(1);
        $tx []= cy\DB::delete('user')->limit(1);
        $tx->exec('cytst-mysqli');
    }

    public function test_transaction_failure() {
        $tx = new db\Transaction;
        $tx []= cy\DB::delete('user')->limit(1);
        $tx []= cy\DB::delete('badtablename')->limit(1);
        try {
            $tx->exec('cytst-mysqli');
            $failed = false;
        } catch (db\Exception $ex) {
            $failed = true;
        }
        $this->assertTrue($failed);
        $this->assertEquals(2, cy\DB::select()->from('user')->exec('cytst-mysqli')->count());
    }
    
}