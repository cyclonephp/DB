<?php

require_once cyclone\LIBPATH.'db/tests/db/postgres/dbtest.php';

use cyclone as cy;
use cyclone\db;

class DB_Postgres_CompileTest extends DB_Postgres_DbTest {

    public function testConnection() {
        cy\DB::connector('postgres')->connect();
    }

    public function testCompileInsert() {
        $query = cy\DB::insert('user')->values(array(
            'name' => 'user'
            , 'email' => 'user@example.com'));
        $this->assertEquals('INSERT INTO "user" ("name", "email") VALUES (\'user\', \'user@example.com\')'
                , $query->compile('postgres'));
    }

    public function testCompileUpdate() {
        $query = cy\DB::update('user')->values(array('name' => NULL, 'email' => 'ebence88@gmail.com'))
                ->where('id', '=', DB::esc(1))->limit(10);

        $this->assertEquals('UPDATE "user" SET "name" = NULL, "email" = \'ebence88@gmail.com\' WHERE "id" = \'1\' LIMIT 10',
                $query->compile('postgres'));
    }

    public function testCompileDelete() {
        $query = cy\DB::delete('user')->where('name', 'like', '%crys%')->limit(10);

        $this->assertEquals('DELETE FROM "user" WHERE "name" like "%crys%" LIMIT 10'
                , $query->compile('postgres'));
    }

    /**
     * @expectedException cyclone\db\Exception
     */
    public function testCompileHint() {
        $query = cy\DB::select()->hint('dummy')->from('table');
        $query->compile('postgres');
    }
    
}