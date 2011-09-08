<?php

use cyclone as cy;
use cyclone\db;


class DB_PreparedTest extends Kohana_Unittest_TestCase {

    public function testPrepareSelect() {
        $query = cy\DB::select()->from('user')->prepare();
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Select', $query);
    }

    public function testPrepareInsert() {
        $query = cy\DB::insert('user')->values(array('id' => 1, 'name' => 'u'))->prepare();
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Insert', $query);
    }

    public function testPrepareUpdate() {
        $query = cy\DB::update('user')->values(array('id' => 1))->prepare();
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Update', $query);
    }

    public function testPrepareDelete() {
        $query = cy\DB::delete('user')->prepare();
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Delete', $query);
    }

    public function testPrepareCustom() {
        $query = cy\DB::query('select * from cy_user')->prepare();
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Custom', $query);
    }
    
}