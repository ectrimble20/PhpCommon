<?php

namespace ECT\Common;

/**
 * Class Guid
 * 
 * This class attempts to simulate generating a Global Unique Id string.  Since PHP (at least 5.X) does not natively
 * support GUID generation, this function uses mt_rand to generate a psudo random GUID.
 * 
 * This has been tested to generate unique GUID's into the millionth iteration.  GENERALLY this should be good enough
 * for most needs.
 * 
 * *****NOTE***** (well a warning)
 * If you require 100.00% unique GUID's, this function IS NOT what you want to use.  There is the possiblity that this 
 * will have a collision as it doesn't follow the standard GUID generation due to limitations with PHP.
 * 
 * If/When I find a better solution to this, I'll implement it into this function
 */
class Guid {

    /**
     * Generate a psudo-random GUID
     * @return string
     */
    public static function Get()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

}