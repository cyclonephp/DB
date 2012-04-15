<?php

use cyclone as cy;
use cyclone\db;
use cyclone\db\schema;

class DB_Schema_ForeignKeyTest extends Kohana_Unittest_TestCase {

    /**
     * @expectedException \cyclone\db\Exception
     */
    public function testEquals() {
        $table_1 = new schema\Table('tbl_1');
        $col_1_1 = $table_1->get_column('col_1_1');
        $col_1_2 = $table_1->get_column('col_1_2');

        $table_2 = new schema\Table('tbl_2');
        $col_2_1 = $table_2->get_column('col_2_1');
        $col_2_2 = $table_2->get_column('col_2_2');

        $fk_1 = new schema\ForeignKey;
        $fk_1->local_table = $table_1;
        $fk_1->foreign_table = $table_2;

        $fk_2 = new schema\ForeignKey;
        $this->assertFalse($fk_1->equals($fk_2), 'local table mismatch');
        $fk_2->local_table = $table_1;
        $this->assertFalse($fk_1->equals($fk_2), 'foreign table mismatch');

        $fk_2->foreign_table = $table_2;

        $fk_1->local_columns = array($col_1_1, $col_1_2);
        $fk_2->local_columns = array($col_1_1, $col_1_2);

        $fk_1->foreign_columns = array($col_2_1, $col_2_2);
        $fk_2->foreign_columns = array($col_2_1, $col_2_2);
        $this->assertTrue($fk_1->equals($fk_2), 'same arrays work');

        $fk_2->local_columns = array_reverse($fk_2->local_columns);
        $fk_2->foreign_columns = array_reverse($fk_2->foreign_columns);
        $this->assertTrue($fk_1->equals($fk_2), 'reverse-order join columns still match');

        $fk_2->local_columns = array_reverse($fk_2->local_columns);
        $this->assertFalse($fk_1->equals($fk_2), 'different join column order detected');

        $fk_1->foreign_columns = array();
        $fk_2->foreign_columns = array();
        $fk_1->equals($fk_2);
    }
}