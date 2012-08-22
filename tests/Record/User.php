<?php

use cyclone\db\record;

class Record_User extends record\AbstractRecord {

    protected static function  setup() {
        $schema = new record\Schema;
        $schema->database = 'cytst-mysqli';
        $schema->table_name = 'user';
        $schema->columns = array(
            'id' => 'int primary key auto_increment',
            'name' => 'varchar(32) not null',
            'email' => 'varchar(32)'
        );
        $schema->primary_key = 'id';
        return $schema;
    }

}