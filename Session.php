<?php

namespace ECT\Common;

/**
 * Class Session
 * 
 * Wrapper for STL session functions to make things a little easier to manage
 * and a little more transparent.
 * 
 * This class is designed to be extended with custom access functions as needed.
 * 
 * You can do this by simply creating a custom session class and extending session,
 * then use the Get/Set functions to create custom functions, example below:
 * 
 * class CustomSession extends ECT\Common\Session
 * {
 *      public static function GetUser()
 *      {
 *          return $self::Get("user");
 *      }
 * }
 * 
 * This would allow you to call CustomSession::GetUser() and abstract the call into
 * the base Session class if you choose to do so.
 */
class Session {

    //Tracking constants, these are just static states, these could be considered an Enum like setting
    //but since it's just an on/off check, this should suffice
    const SESSION_STATE_ACTIVE = true;
    const SESSION_STATE_INACTIVE = false;

    //Initial session state is always inactive
    private static $session_state = self::SESSION_STATE_INACTIVE;

    /**
     * Begins a session
     */
    public static function Start()
    {
        if( self::$session_state === self::SESSION_STATE_INACTIVE ) {
            self::$session_state = session_start();
        }
        return self::$session_state;
    }

    /**
     * Ends and destroys a session as well as unsetting an session variables
     */
    public static function Destroy()
    {
        if( self::$session_state === self::SESSION_STATE_ACTIVE ) {
            session_unset();
            session_destroy();
            self::$session_state = self::SESSION_STATE_INACTIVE;
        }
        return !self::$session_state;
    }

    /**
     * Access session variables, returns null if no parameter is found
     * @param $key string
     * @return mixed|null
     *
     */
    public static function Get($key)
    {
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * Assign a value to a session key
     * @param $key string
     * @param $value mixed
     */
    public static function Set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
}