<?php
class Request
{
    private static $instance;
    private $vars;

    public function __construct()
    {
        $parsed = parse_url($_SERVER['REQUEST_URI']);
        $this->vars['basePath']     =  dirname($_SERVER['SCRIPT_NAME']);
        $this->vars['uri']      = trim( str_replace( $this->vars['basePath'],'',$parsed['path'] ), '/' );
        $this->vars['query']    = isset($parsed['query']) ? $parsed['query'] : null;

        if(empty($this->vars['uri'])) $this->vars['uri'] = '/';

        $this->vars['method']   = $_SERVER['REQUEST_METHOD'];

        $this->vars['isAjax']   = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;


        $this->vars['firstUri']     = $this->vars['uri'];
        $this->vars['toDispatch']   = $this->vars['uri'];
        $this->vars['toDispatchParams']   = array();
    }

    public function __set($name,$value)
    {
        if(isset($this->vars[$name]))
            $this->vars[$name] = $value;
    }

    public function __get($name)
    {
        if(isset($this->vars[$name]))
            return $this->vars[$name];
    }

    public static function get_instance()
    {
        if(self::$instance === null)
            self::$instance = new Request();
        return self::$instance;
    }
}