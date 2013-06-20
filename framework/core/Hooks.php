<?php

class Hooks
{
    private static $instance;
    public $hooks = array();

    public static function add($hookName,$callback){
        $ins = self::get_instance();
        $ins->hooks[$hookName][] = $callback;
    }

    public static function fire($hookName,$params = array())
    {
        $ins = self::get_instance();
        if(isset($ins->hooks[$hookName]))
        {
            foreach($ins->hooks[$hookName] as $callback)
            {
                call_user_func_array($callback,$params);
            }
        }
    }

    public static function get_instance()
    {
        if(self::$instance === null)
        {
            self::$instance = new Hooks();
        }
        return self::$instance;
    }
}