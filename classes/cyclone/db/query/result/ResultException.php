<?php

namespace cyclone\db\query\result;

use cyclone\db;

class ResultException extends db\Exception {

    const NO_ROWS_FOUND = 1;

    const TOO_MANY_ROWS = 2;

}