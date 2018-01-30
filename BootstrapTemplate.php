<?php

namespace ECT;

/**
 * Class BootStrap
 * @package ECT
 * 
 * This is a template for a standard BootStrapping class.
 * 
 * What is a BootStrapping class you might be asking?
 * 
 * A BootStrapping class, in this context, is a class that provides a method to look up files based on their
 * namespace.  For instance, if you call a class MyNameSpace\Application\Controller, this will attempt to find
 * the required php file at ./MyNameSpace/Application/Controller.php, at which point it will include it into 
 * the code.
 * 
 * This allows only the required files to be loaded at run time and allows you to have only 1 or 2 required
 * includes within your main intake file.
 * 
 * To use this class, modify the const PREFIX to your top level namespace, using the example above, that would
 * be MyNameSpace.
 * 
 * Place this class file at the top level of your application, generally wherever your index.php or whatever
 * you are using as the main intake point for the application.
 * 
 * Include the file:
 * require_once 'BootStrap.php';
 * 
 * Then call the boostrap function:
 * BootStrap::registerAutoLoader();
 * 
 * This will register the static BootStrap::autoload($class) function as the function that the runtime will
 * call when it's looking for a class.  This is handled by the spl_autoload_register function.
 * 
 * Once you've called registerAutoLoader, you can then call into any classes within your application.
 * 
 * There are some limitations to this, such as trying to load errors or have functionality called prior to
 * the bootstrap being initialized.
 */
class BootStrap
{

    //NOTE: Set this to your top level namespace for bootstrapping
    const PREFIX = "ECT";

    /**
     * @param $class
     */
    public static function autoLoad($class)
    {
        //add \\ to the prefix, we need this to create a proper string with the namespace pathing
        $prefix = self::PREFIX .'\\';
        //search based off this directory
        $base_dir = __DIR__ . "/";
        $len = strlen($prefix);
        //make sure we have a good input
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        //create the path to our file
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        //check that the file exists prior to loading
        if (file_exists($file)) {
            require $file;
        }
        //NOTE: Add any error handling you wish below this line but inside the function {}'s
    }

    /**
     *
     */
    public static function registerAutoLoader()
    {
        spl_autoload_register(__NAMESPACE__ . "\\BootStrap::autoLoad");
    }

}