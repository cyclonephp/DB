<?php

namespace cyclone\db\query\result;
use cyclone\db;

/**
 * The base class for database result processors.
 *
 * Database result classes provide a simple and convenient way to iterate on the
 * result of a SELECT query. Every database adapter has it's own implementation
 * of @c AbstractResult. Result objects are recommended to not be created directly,
 * but via the exec() method of @c \cyclone\db\query\Select.
 *
 * Example: @code
 * $result = \cyclone\DB::select()->from('t_users')->exec()
 *      ->rows('Model_User')->index_by('id');
 * foreach ($result as $id => $user) {
 *    echo "user #$id: {$user->name}";
 * } @endcode
 *
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
abstract class AbstractResult extends \ArrayIterator implements \Countable, \Traversable {

    /**
     * The type of the resulting rows. Can be <code>array</code> or a class name.
     * If it's a class name, then the class should have a parameterless constructor
     * and the values will be populated by simple value assignments (the concrete
     * subclasses won't try to call setters or whatever other method).
     *
     * @var string
     */
    protected $_row_type = 'array';

    /**
     * If this value is not <code>NULL</code>, then during processing the result,
     * the array keys will be the actual values in the current row specified by
     * this value (so it should be a column name in the query result).
     *
     * @var string
     */
    protected $_index_by;

    /**
     * The row that's currently processed (a cursor).
     *
     * @var array
     */
    protected $_current_row;

    /**
     * The index of the currently processed row.
     *
     * @var int
     */
    protected $_idx = -1;

    /**
     * Sets the row type to be used during the iteration.
     *
     * It can be a valid class name, or 'array'. The latter is the default.
     *
     * @param string $type
     * @return AbstractResult $this
     */
    public function rows($type) {
        $this->_row_type = $type;
        return $this;
    }

    /**
     * Sets the column of the database result to be used as index key during the
     * iteration.
     *
     * By default it's NULL. If it's NULL, then the key will be the number of the
     * currently processed row. It's useful to set it to a primary key (if it's
     * selected).
     *
     * @param string $column
     * @return AbstractResult $this
     */
    public function index_by($column) {
        $this->_index_by = $column;
        return $this;
    }


    /**
     * Returns all the result rows as associative arrays.
     *
     * @return array
     */
    public abstract function as_array();

    public function key() {
        if (is_null($this->_index_by)) {
            return $this->_idx;
        }
        if ('array' == $this->_row_type)
            return $this->_current_row[$this->_index_by];
        return $this->_current_row->{$this->_index_by};
    }

    public function  current() {
        return $this->_current_row;
    }

}
