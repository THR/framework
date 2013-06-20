<?php

class Router
{
    private static $instance;
    public $routes;
    public $routeName;
    public $routeUrl;
    public $params;

    public static function add($name,$pattern,$handler)
    {
        $ins = self::get_instance();
        $ins->routes[$name] = array($handler,$pattern);
    }

    public function decide()
    {

        $request = Request::get_instance();
        //var_dump($request);
        $uri = $request->uri;

        foreach($this->routes as $name=>$route)
        {
            $params = array();
            $patternParams = array();

            list($handler,$pattern) = $route;

            // IF any method type defined in pattern
            // lets recognize it
            // IE: GET blog/show
            if(strpos($pattern," ") !== false)
                list($method,$pattern) = explode(" ",$pattern);

            // if request method and rule method not same then go
            if(isset($method) && $method !== $request->method)
                continue;

            //define vars from pattern
            preg_match_all('/\<.+?\>/',$pattern,$vars);
            $vars = $vars[0];

            // IF pattern has vars like <id>
            // then form up them
            if($vars)
            {
                foreach($vars as $var)
                {
                    $var =  trim($var,'<>');

                    $var = explode(":",$var);

                    $varName = $var[0];
                    $varPat  = isset($var[1]) ? '('.$var[1].')' : '(.+)';

                    $params[$varName] = $varPat;
                    //var_dump($params);

                }

                $pattern =  str_replace($vars,$params,$pattern);
            }else {
                //$pattern = $pattern;
            }
            if(preg_match('#^'.$pattern.'$#',$uri))
            {
                preg_match('#^'.$pattern.'$#',$uri,$m);
                array_shift($m);
                //var_dump($m);

                //Combine route params with matched params
                if(count($m)){
                    $params = array_combine(array_keys($params),$m);
                    $request->toDispatchParams = $params;
                    self::makeGet($params);
                }

                //var_dump($params);

                $this->routeName = $name;
                $this->routeUrl  = $handler;
                $request->toDispatch = $handler .'/'. implode('/',$params);
                return $handler;
            }

        }

        return $uri;
    }

    public static function createLink($name,$params = array())
    {
        $ins = self::get_instance();
        $url = $ins->routes{$name}[1];
        foreach($params as $k=>$v)
        {

            $url =  preg_replace('#\<'.$k.'.*?\>#',$v,$url);
        }
        return $url;
    }

    public static function get_instance()
    {
        if(self::$instance === null)
            self::$instance = new Router();
        return self::$instance;
    }

    public static function makeGet($params = array())
    {
        foreach($params as $k=>$v)
            $_GET[$k] = $v;
    }
}