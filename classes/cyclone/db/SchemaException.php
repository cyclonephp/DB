<?php

namespace cyclone\db;

/**
 * Generic exception class, the instances of its subclasses are thrown if a relation,
 * column or database function referenced by a query to be executed doesn't exist in
 * the database. Instances are thrown by
 * <ul>
 *  <li> executors (in \cyclone\db\executor namespace)</li>
 *  <li> prepared executors (in \cyclone\db\prepared\executor namespace)</li>
 * </ul>
 *
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package DB
 */
abstract class SchemaException extends Exception {

}