<?php
set_include_path( __DIR__ . ';' . get_include_path() );
define('DIR_SEP',      DIRECTORY_SEPARATOR);
define('ITCUBE_PATH', (string)__DIR__ );
define('ROOT',        (string)str_replace( '\\itcube', '', __DIR__ ));
define('LOGS_DIR',    ITCUBE_PATH . DIR_SEP . 'Logs');
/**
* (ACCESS_VIEW | ACCESS_EDIT) = 5
* 
*/
if( !defined('ACCESS_DELETE') ) {
  define('ACCESS_VIEW'  , 1 << 0); // 1
  define('ACCESS_CREATE', 1 << 1); // 2
  define('ACCESS_EDIT'  , 1 << 2); // 4
  define('ACCESS_DELETE', 1 << 3); // 8
  define('ACCESS_ALL'   , ACCESS_VIEW | ACCESS_CREATE | ACCESS_EDIT | ACCESS_DELETE); // 15
}



function itcube_Loader($className){
    $c_name = (string)str_replace( '\\', '|', $className );
    $c_name = (string)str_replace( 'itcube', '', $c_name );
    $file_path = ITCUBE_PATH . $c_name . '.php';
    $file_path = str_replace('|', DIR_SEP, $file_path);
    $file_path = str_replace(DIR_SEP . DIR_SEP, DIR_SEP, $file_path);

    if( is_file($file_path) ) {
        require($file_path);
        return true;
    }
    return false;
}


spl_autoload_register('itcube_Loader');