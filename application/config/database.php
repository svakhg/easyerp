<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;

$db['erp']['hostname'] = 'rdsauqmnfy6ja6n.mysql.rds.aliyuncs.com:3306';
$db['erp']['username'] = 'easywed_erp';
$db['erp']['password'] = 'akowkFD_87_fswdv';
$db['erp']['database'] = 'easywed_erp';
$db['erp']['dbdriver'] = 'mysql';
$db['erp']['dbprefix'] = 'ew_';
$db['erp']['pconnect'] = FALSE;
$db['erp']['db_debug'] = TRUE;
$db['erp']['cache_on'] = FALSE;
$db['erp']['cachedir'] = '';
$db['erp']['char_set'] = 'utf8';
$db['erp']['dbcollat'] = 'utf8_general_ci';
$db['erp']['swap_pre'] = '';
$db['erp']['autoinit'] = TRUE;
$db['erp']['stricton'] = FALSE;

$db['easywed']['hostname'] = 'rdsauqmnfy6ja6n.mysql.rds.aliyuncs.com:3306';
$db['easywed']['username'] = 'ewd4prodxf';
$db['easywed']['password'] = 'odxfny_mkeD';
$db['easywed']['database'] = 'easywed_product';
$db['easywed']['dbdriver'] = 'mysql';
$db['easywed']['dbprefix'] = 'ew_';
$db['easywed']['pconnect'] = FALSE;
$db['easywed']['db_debug'] = TRUE;
$db['easywed']['cache_on'] = FALSE;
$db['easywed']['cachedir'] = '';
$db['easywed']['char_set'] = 'utf8';
$db['easywed']['dbcollat'] = 'utf8_general_ci';
$db['easywed']['swap_pre'] = '';
$db['easywed']['autoinit'] = TRUE;
$db['easywed']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */
