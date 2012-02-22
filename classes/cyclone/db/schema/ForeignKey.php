<?php

namespace cyclone\db\schema;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class ForeignKey {

    /**
     * @var Table
     */
    public $local_table;

    /**
     * @var array<Column>
     */
    public $local_columns = array();

    /**
     * @var Table
     */
    public $foreign_table;

    /**
     * @var array<Column>
     */
    public $foreign_columns = array();

    public function equals(ForeignKey $other) {
        if ($this->local_table != $other->local_table)
            return FALSE;

        if ($this->foreign_table != $other->foreign_table)
            return FALSE;

        if (count($this->local_columns) != count($other->local_columns))
            return FALSE;

        if (count($this->foreign_columns) != count($other->foreign_columns))
            return FALSE;

        if (count($this->local_columns) != count($this->foreign_columns))
            throw new db\Exception("invalid foreign key state: " . count($this->local_columns)
                    . " local columns can not reference " . count($this->foreign_columns)
                    . " foreign columns");

        foreach ($this->local_columns as $idx => $col) {
            $other_idx = array_search($col, $other->local_columns);
            if (FALSE === $other_idx)
                return FALSE;

            $foreign_col = $this->foreign_columns[$idx];



            if ($col != $other->local_columns[$other_idx])
                return FALSE;

            if ($foreign_col != $other->foreign_columns[$other_idx])
                return FALSE;
        }

        return TRUE;
    }

}