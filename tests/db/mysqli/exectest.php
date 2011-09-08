<?php

use cyclone as cy;
use cyclone\db;

class DB_Mysqli_ExecTest extends DB_MySQLi_DbTest {

    /**
     *
     * @expectedException cyclone\db\Exception
     */
    public function testExecUpdate() {
        $affected = cy\DB::update('user')->values(array('name' => 'crystal88_'))->exec();
        $this->assertEquals($affected, 2);
        cy\DB::update('users')->values(array('name' => 'crystal88_'))->exec();
    }

    /**
     *
     * @expectedException cyclone\db\Exception
     */
    public function testExecDelete() {
        $affected = cy\DB::delete('user')->exec();
        $this->assertEquals($affected, 2);
        cy\DB::delete('users')->exec();
    }

    /**
     *
     * @expectedException cyclone\db\Exception
     */
    public function testExecInsert() {
        $insert_id = cy\DB::insert('user')->values(array('name' => 'crystal'))->exec();
        $this->assertEquals(3, $insert_id);
        $insert_id = cy\DB::insert('user')->values(array('name' => 'crystal'))
                ->values(array('name' => 'crystal'))->exec();
        $this->assertEquals(4, $insert_id);
        cy\DB::insert('users')->values(array('name' => 'crystal'))->exec();
    }

    public function testExecSelect() {
        $names = array('user1', 'user2');
        $result = cy\DB::select()->from('user')->exec();
        $this->assertInstanceOf('cyclone\db\query\result\MySQLi', $result);
        $this->assertEquals(2, $result->count());
        $idx = 0;
        foreach ($result as $v) {
            $this->assertEquals($v['name'], $names[$idx++]);
        }
        $result = cy\DB::select()->from('user')->exec();
        $result->rows('stdClass');
        $idx = 0;
        foreach ($result as $v) {
            $this->assertEquals($v->name, $names[$idx++]);
        }
        $result = cy\DB::select()->from('user')->exec()
                ->index_by('name')->rows('stdClass');
        $idx = 0;
        foreach ($result as $k => $v) {
            $this->assertEquals($v->name, $names[$idx]);
            $this->assertEquals($k, $names[$idx++]);
        }
    }

    public function testExecMultiquery() {
        $result1 = cy\DB::select()->from('user')->exec();
        $result2 = cy\DB::select()->from('user')->exec();
        foreach ($result1 as $k => $v) {

        }

        cy\DB::executor()->exec_custom('select 2');

        cy\DB::executor()->exec_custom('drop table if exists t_posts; create table t_posts(id int);');
        cy\DB::connector()->disconnect();
        cy\DB::connector()->connect();
    }

    public function testExecCustom() {
        cy\DB::executor()->exec_custom('create table if not exists tmp (id int)');
    }

    public function testAsArray() {
        $names = array('user1', 'user2');
        $result = cy\DB::select()->from('user')->exec()->index_by('name')->rows('stdClass')->as_array();
        $this->assertEquals(count($result), 2);
        $idx = 0;
        foreach ($result as $k => $v) {
            $this->assertEquals($v->name, $names[$idx]);
            $this->assertEquals($k, $names[$idx++]);
        }
    }

    public function testCommitRollback() {
        $existing_rows = cy\DB::select()->from('user')->exec()->count();
        $this->assertEquals(2, $existing_rows);
        $conn = cy\DB::connector();
        $conn->autocommit(false);
        $deleted_rows = cy\DB::delete('user')->exec();
        $this->assertEquals(2, $deleted_rows);
        $conn->rollback();
        $existing_rows = cy\DB::select()->from('user')->exec()->count();
        $this->assertEquals(2, $existing_rows); return;
        $deleted_rows = cy\DB::delete('user')->exec();
        $this->assertEquals(2, $deleted_rows);
        $conn->commit();
        $existing_rows = cy\DB::select()->from('user')->exec()->count();
        $this->assertEquals(0, $existing_rows);
    }

    public function testTransactionSuccess() {
        $tx = new db\Transaction;
        $tx []= cy\DB::delete('user')->limit(1);
        $tx []= cy\DB::delete('user')->limit(1);
        $tx->exec();
    }

    public function testTransactionFailure() {
        $tx = new db\Transaction;
        $tx []= cy\DB::delete('user')->limit(1);
        $tx []= cy\DB::delete('badtablename')->limit(1);
        try {
            $tx->exec();
            $failed = false;
        } catch (db\Exception $ex) {
            $failed = true;
        }
        $this->assertTrue($failed);
        $this->assertEquals(2, cy\DB::select()->from('user')->exec()->count());
    }
    
}