<?php

/**
 * Test constants used for unit tests
 */

define( '_NL', PHP_EOL );
define( 'MUMSYS_REGEX_AZ09', '/^([0-9a-zA-Z])+$/i' );
define( 'MUMSYS_REGEX_ALNUM', '/^([_]|[0-9a-zA-Z])+$/i' );
define( 'MUMSYS_REGEX_AZ09X', '/^([_-]|[0-9a-zA-Z])+$/i' );

/**
 * Datetime string like mysql uses it eg: YYYY-MM-DD HH:ii:ss
 */
define( 'MUMSYS_REGEX_DATETIME_MYSQL', '/^(\d{4})-(\d{2})-(\d{2} (\d{1,2}):(\d{1,2}):(\d{1,2}))$/' );
define( 'MUMSYS_REGEX_AZ09X_', '/^([ ]|[_-]|[0-9a-zA-Z])+$/i' );


// 4 Mumsys_Db_Driver_Abstract
define( '_CMS_AND', 'And' );
define( '_CMS_OR', 'Or' );
define( '_CMS_ISEQUAL', 'is equal' );
define( '_CMS_ISGREATERTHAN', 'is greater than' );
define( '_CMS_ISLESSTHAN', 'is less than' );
define( '_CMS_ISGREATERTHANOREQUAL', 'is greater or equal' );
define( '_CMS_ISLESSTHANOREQUAL', 'is less or equal' );
define( '_CMS_ISNOTEQUAL', 'is not equal' );
define( '_CMS_CONTAINS', 'contains' );
define( '_CMS_CONTAINS_NOT', 'contains not' );
define( '_CMS_ENDSWITH', 'ends with' );
define( '_CMS_ENDSNOTWITH', 'ends not with' );
define( '_CMS_BEGINSWITH', 'beginns with' );
define( '_CMS_BEGINSNOTWITH', 'begins not with' );
