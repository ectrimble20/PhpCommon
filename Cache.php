<?php

namespace ECT\Common;

/**
 * Class Cache
 * 
 * Wrapper for the STL APC functions for accessing the web servers cache.
 *
 * Note:  This is designed for single object -> single key and is not designed to handle array
 * operations as it is only intended for quick simple object storage
 */
class Cache {

    /**
     * Store a value in the cache
     *
     * @param string $key
     * @param mixed $value
     */
    public static function Store($key, $value)
    {
        //remove old value if exists
        if( self::Exists($key)){
            self::Delete($key);
        }
        apc_store($key, $value);
    }

    /**
     * Retrieve a value from the cache
     *
     * @param string $key
     * @return mixed (null if it doesn't exist, check with ! is_null() )
     */
    public static function Fetch($key)
    {
        if( self::Exists($key)) {
            return apc_fetch($key, $value);
        } else {
            return null;
        }
    }

    /**
     * Delete a value from the cache
     *
     * @param string $key
     */
    public static function Delete($key)
    {
        apc_delete($key);
    }

    /**
     * Check if a key exists
     *
     * @param string $key
     * @return bool
     */
    public static function Exists($key)
    {
        return apc_exists($key);
    }

}