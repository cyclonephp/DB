<?php

namespace cyclone\db\schema\generator;
use cyclone\db\schema;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class AbstractGenerator implements db\schema\Generator {

    /**
     * The configuration passed in the constructor
     *
     * @var array
     */
    private $_cfg;

    function __construct($cfg) {
        $this->_cfg = $cfg;
    }

    public function ddl_create_table(schema\Table $table, $forced = FALSE) {
        $rval = '';
        $table_name = '';
        if (isset($this->_cfg['prefix'])) {
            $table_name = $this->_cfg['prefix'];
        }
        $table_name .= $table->table_name;
        if ($forced) {
            $rval .= "DROP TABLE IF EXISTS `{$table_name}`;" . Env::$eol;
        }
        $rval .= "CREATE TABLE `{$table_name}` (" . Env::$eol . "\t";
        $col_ddls = array();
        foreach ($table->columns as $col) {
            $col_ddls [] = $this->ddl_create_column($col);
        }
        $rval .= implode(",\n\t", $col_ddls);
        $rval .= "\n)" . Env::$eol;
        return $rval;
    }

    public function ddl_create_column(schema\Column $column) {
        if (!is_null($column->ddl))
            return "`{$column->name}` " . $column->ddl;
        $rval = "`{$column->name}` ";
        $rval .= $column->type;
        if (!is_null($column->length)) {
            $rval .= '(' . $column->length . ')';
        }
        return $rval;
    }

}
