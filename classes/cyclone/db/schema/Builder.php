<?php

namespace cyclone\db\schema;

use cyclone\DB;
use cyclone\FileSystem;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Builder {

    /**
     * Entry point of SimpleDB CLI schema generation.
     *
     * @param array $args command-line arguments
     */
    public static function build_schema($args) {
        $forced = array_key_exists('--forced', $args) ? $args['--forced'] : FALSE;
        $suppress_execution = array_key_exists('--suppress-execution', $args)
                ? $args['--suppress-execution'] : FALSE;
        $library = array_key_exists('--namespace', $args) ? $args['--namespace'] : NULL;
        $builder = new Builder($forced, $suppress_execution, $library);
        $builder->build();
    }

    public function ddl_for_table(Table $table) {
        $generator = DB::schema_generator($table->database);
        $ddl = $generator->ddl_create_table($table, $this->_forced);
        if ( ! $this->_suppress_execution) {
            DB::executor($table->database)->exec_custom($ddl);
        }
        return $ddl;
    }

    /**
     *
     * @var string
     */
    private $_namespace;

    /**
     * @var boolean
     */
    private $_forced = FALSE;

    /**
     * @var boolean
     */
    private $_suppress_execution = FALSE;

    public function  __construct($forced = FALSE, $suppress_execution = FALSE, $namespace = NULL) {
        $this->_namespace = $namespace;
        $this->_forced = $forced;
        $this->_suppress_execution = $suppress_execution;
    }

    public function build() {
        $namespaces = NULL === $this->_namespace ? NULL : explode(',', $this->_namespace);
        $files = array();
        foreach ($namespaces as $ns) {
            $files += FileSystem::get_default()->list_directory('classes/' . \str_replace('\\', \DIRECTORY_SEPARATOR, $ns));
        }
        $classes = array();
        foreach ($files as $rel_path => $abs_path) {
            $prefix_len = strlen('classes/record') + 1;
            $relname = substr($rel_path, $prefix_len, strrpos($rel_path, '.') - $prefix_len);
            $classname = str_replace(DIRECTORY_SEPARATOR, '_', $relname);
            $classname = 'Record_' . $classname;
            if ($classname == 'Record_abstract' || $classname == 'Record_schema')
                continue;
            $classes []= $classname;
        }
        $ddl = '';
        foreach ($classes as $class) {
            $ddl .= $this->build_ddl_for_record($class);
        }
        if ($this->_suppress_execution) {
            echo $ddl;
        }
    }

    public function build_ddl_for_record($class) {
        $inst = call_user_func(array($class, 'inst'));
        $record_schema = $inst->schema();
        $table_schema = Table::for_record_schema($record_schema);
        return $this->ddl_for_table($table_schema);
    }

}
