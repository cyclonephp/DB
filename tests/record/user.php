<?php

use cyclone\db\record;

class Record_User extends record\AbstractRecord {

    protected function  setup() {
        $this->_schema->database = 'cytst-mysqli';
        $this->_schema->table_name = 'user';
        $this->_schema->columns = array(
            'id' => 'int primary key auto_increment',
            'name' => 'varchar(32) not null',
            'email' => 'varchar(32)'
        );
        $this->_schema->primary_key = 'id';
    }

    public static function  inst() {
        return parent::_inst(__CLASS__);
    }
    
}