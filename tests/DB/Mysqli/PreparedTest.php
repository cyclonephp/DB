<?php

use cyclone as cy;
use cyclone\db;

class DB_Mysqli_PreparedTest extends DB_Mysqli_DbTest {


    public function test_prepare() {
        $stmt = cy\DB::executor_prepared('cytst-mysqli')->prepare('select * from cy_user');
        $this->assertInstanceOf('MySQLi_Stmt', $stmt);
    }

    /**
     * @expectedException cyclone\db\Exception
     * @expectedExceptionMessage failed to prepare statement: 'select * from dummy' Cause: Table 'simpledb.dummy' doesn't exist
     */
    public function test_prepare_failure() {
        $stmt = cy\DB::executor_prepared('cytst-mysqli')->prepare('select * from dummy');
    }


    public function test_exec_select() {
        $result = cy\DB::select('id', 'name')->from('user')->prepare('cytst-mysqli')->exec();
        $this->assertEquals(2, count($result));
    }

    /**
     * @expectedException cyclone\db\Exception
     */
    public function test_exec_select_failure() {
        $result = cy\DB::select()->from('user')->prepare('cytst-mysqli')->exec();
    }

    public function test_prepared_result() {
        $stmt = cy\DB::connector('cytst-mysqli')->db_conn->prepare('select id, name from cy_user');
        $stmt->execute();
        $stmt->store_result();
        $result = new db\prepared\result\MySQLi($stmt, cy\DB::select('id', 'name')->from('user'));
        $this->assertEquals(2, count($result));

        $exp = array(
            array('id' => 1, 'name' => 'user1'),
            array('id' => 2, 'name' => 'user2')
        );
        $idx = 0;
        foreach ($result as $key => $row) {
            $this->assertEquals($idx, $key);
            $this->assertEquals($exp[$idx]['id'], $row['id']);
            ++$idx;
        }

        $idx = 0;
        foreach ($result as $key => $row) {
            $this->assertEquals($idx, $key);
            $this->assertEquals($exp[$idx]['id'], $row['id']);
            ++$idx;
        }
    }

    public function test_prepared_result_index_by() {
        $stmt = cy\DB::connector('cytst-mysqli')->db_conn->prepare('select id, name from cy_user');
        $stmt->execute();
        $stmt->store_result();
        $result = new db\prepared\result\MySQLi($stmt, DB::select('id', 'name')->from('user'));
        $result->index_by('id');
        $this->assertEquals(2, count($result));

        $exp = array(
            1 => array('id' => 1, 'name' => 'user1'),
            2 => array('id' => 2, 'name' => 'user2')
        );
        $idx = 1;
        foreach ($result as $key => $row) {
            $this->assertEquals($idx, $key);
            $this->assertEquals($exp[$idx]['id'], $row['id']);
            ++$idx;
        }

        $idx = 1;
        foreach ($result as $key => $row) {
            $this->assertEquals($idx, $key);
            $this->assertEquals($exp[$idx]['id'], $row['id']);
            ++$idx;
        }
    }

    public function test_insert() {
        $result = cy\DB::insert('user')->values(array('name' => 'user3'))
                ->returning('id')
                ->prepare('cytst-mysqli')->exec();
        $this->assertInstanceOf('cyclone\\db\\StmtResult', $result);
        $this->assertEquals(1, $result->affected_row_count);
        $this->assertEquals(3, $result->rows[0]['id']);
    }

    public function test_update() {
        $result = cy\DB::update('user')->values(array('name' => 'u'))
                ->returning('id', 'name')
                ->prepare('cytst-mysqli')->exec();
        $this->assertInstanceOf('cyclone\\db\\StmtResult', $result);
        $this->assertEquals(2, $result->affected_row_count);
        $this->assertEquals(1, $result->rows[0]['id']);
        $this->assertEquals('u', $result->rows[0]['name']);
        $this->assertEquals(2, $result->rows[1]['id']);
        $this->assertEquals('u', $result->rows[1]['name']);
    }

    public function test_delete() {
        $aff_rows = cy\DB::delete('user')->prepare('cytst-mysqli')->exec();
        $this->assertEquals(2, $aff_rows);
    }

    public function test_param_int() {
        $result = cy\DB::select('name')->from('user')->where('id', '=', cy\DB::param())
                ->prepare('cytst-mysqli')->param(2)->exec();

        $this->assertEquals(1, count($result));
        
        $result = cy\DB::select('name')->from('user')->where('id', '=', cy\DB::param())
                ->where('id', '=', cy\DB::param())->prepare('cytst-mysqli')->param(1)->param(2)->exec();

        $this->assertEquals(0, count($result));
    }

    public function test_param_string() {
        $result = cy\DB::select('name')->from('user')->where('name', '=', cy\DB::param())
                ->prepare('cytst-mysqli')->param('user1')->exec();

        $this->assertEquals(1, count($result));

        $result = cy\DB::select('name')->from('user')->where('name', '=', cy\DB::param())
                ->where('name', '=', cy\DB::param())->prepare('cytst-mysqli')
                ->param('user1')->param('user2')->exec();

        $this->assertEquals(0, count($result));
    }

    public function test_param_boolean() {
        $result = cy\DB::select('name')->from('user')->where('id', '=', cy\DB::param())
                ->prepare('cytst-mysqli')->param(TRUE)->exec();

        $this->assertEquals(1, count($result));

    }

    /**
     * @expectedException cyclone\db\Exception
     */
    public function test_param_array() {
        $result = cy\DB::select('name')->from('user')->where('id', '=', cy\DB::param())
                ->prepare('cytst-mysqli')->param(array())->exec();
    }
}