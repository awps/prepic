<?php
/**
 * Load PrePic
 *
 * SPL Autoloader.
 *
 * @param string $class
 */
function load_prepic($class)
{
    // does the class use the namespace prefix?
    $len = strlen('PrePic\\');
    if (strncmp('PrePic\\', $class, $len) !== 0) {
        // no, move to the next registered loader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = PREPIC_PATH . 'src/' . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
}

/*
-------------------------------------------------------------------------------
Register autoloader
-------------------------------------------------------------------------------
*/
spl_autoload_register('load_prepic');
