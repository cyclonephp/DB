<?php

use cyclone as cy;
use cyclone\db;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Mysqli/DBTest.php';

class DB_PreparedTest extends DB_Mysqli_DbTest {

    public function testPrepareSelect() {
        $query = cy\DB::select()->from('user')->prepare('cytst-mysqli');
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Select', $query);
    }

    public function testPrepareInsert() {
        $query = cy\DB::insert('user')->values(array('id' => 1, 'name' => 'u'))
                ->prepare('cytst-mysqli');
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Insert', $query);
    }

    public function testPrepareUpdate() {
        $query = cy\DB::update('user')->values(array('id' => 1))->prepare('cytst-mysqli');
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Update', $query);
    }

    public function testPrepareDelete() {
        $query = cy\DB::delete('user')->prepare('cytst-mysqli');
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Delete', $query);
    }

    public function testPrepareCustom() {
        $query = cy\DB::query('select * from cy_user')->prepare('cytst-mysqli');
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\query\\Custom', $query);
    }
    
}