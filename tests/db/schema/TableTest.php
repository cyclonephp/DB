<?php

use cyclone as cy;
use cyclone\db;
use cyclone\db\schema;

class DB_Schema_TableTest {

    /**
     * @expectedException \cyclone\db\Exception
     */
    public function testAddForeignKey() {
        $table1 = new schema\Table('table1');
        $table2 = new schema\Table('table2');

        $fk1 = new schema\ForeignKey;
        $fk1->local_table = $table1;
        $table1->add_foreign_key($fk1);
        $this->assertEquals(1, count($table1->foreign_keys));

        $table1->add_foreign_key($fk1);
        $this->assertEquals(1, count($table1->foreign_keys));

        $fk2 = new schema\ForeignKey;
        $fk2->local_columns = array($table1->get_column('dummy'));
        $fk2->foreign_columns = array($table2->get_column('dummy'));

        $table1->add_foreign_key($fk2);
        $this->assertEquals(2, count($table1->foreign_keys));
        $this->assertEquals($table1, $fk2->local_table);

        $fk3 = new schema\ForeignKey;
        $fk3->local_table = $table2;
        $table1->add_foreign_key($fk3);
    }
}