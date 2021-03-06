<?php

namespace cyclone\db\query\result;

use cyclone\db;

/**
 * The result of a SELECT statement executed on a postgres database.
 *
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 * @see \cyclone\db\executor\Mysqli::exec_select()
 */
class Mysqli extends AbstractResult {

    /**
     * The raw query result.
     *
     * @var resource
     */
    protected $result;

    public function  __construct(\mysqli_result $result) {
        $this->result = $result;
    }

    public function key() {
        if (is_null($this->_index_by)) {
            return $this->_idx;
        }
        if ('array' == $this->_row_type)
            return $this->_current_row[$this->_index_by];
        return $this->_current_row->{$this->_index_by};
    }

    public function next() {
        if ('array' == $this->_row_type) {
            $this->_current_row = $this->result->fetch_assoc();
        } else {
            $this->_current_row = $this->result->fetch_object($this->_row_type);
        }
        ++$this->_idx;
    }

    public function rewind() {
        $this->result->data_seek(0);
        $this->_idx = -1;
        $this->next();
    }

    public function seek($pos) {
        $this->result->data_seek($pos);
        $this->_idx = $pos;
    }

    public function valid() {
        return $this->_current_row != null;
    }

    public function  count() {
        return $this->result->num_rows;
    }

    public function  __destruct() {
        $this->result->free();
    }

    public function as_array() {
        $rval = array();
        foreach ($this as $k => $v) {
            $rval[$k] = $v;
        }
        return $rval;
    }

    
}
