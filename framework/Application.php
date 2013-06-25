<?php
class Application
{
    protected $_defaultComponents;
    protected $_components = array();
    protected $_paths = array();
    protected $_config;
    public $request;
    public $router;
    protected $dispatchRoute;
    private $defaultController = 'default';
    private $defaultAction = 'index';
    public $module;
    public $controllerPath = 'application/controllers/';
    public $modulePath     = 'application/modules/';
    public $controller;




    public function __construct($config)
    {
        $this->_config = $config;
        //SET reversed names for default components
        $this->_defaultComponents = array(
            //'request'   =>array('class'=>'framework/web/Request'),
            //'router'  =>array('class'=>'framework/web/Router'),
        );

        require_once "framework/core/Base.php";
        spl_autoload_register('Base::autoload');

        Base::setLogger(new SimpleLogger());

        Base::setPathofAlias('application',BASE_PATH.DIRECTORY_SEPARATOR.'application');

        $this->init();
    }




    public function init()
    {
        Base::setApp($this);

        foreach($this->_defaultComponents as $name=>$value)
            $this->createComponent($name,$value);

        $this->request   = Request::get_instance();
        $this->router    = Router::get_instance();



    }





    public function run()
    {
        Hooks::fire('app.setted');






        Hooks::fire('route.beforeDecide');
        $this->dispatchRoute = $this->router->decide();
        Hooks::fire('route.afterDecide');

        Hooks::fire('dispatch.before');
        $this->dispatch();
        Hooks::fire('dispatch.after');


    }




    /**
     * Dispatch uri as controller = foo, action = foo
     * Gathers uri from @Request->toDispatch
     */
    public function dispatch()
    {
        $uri = $this->request->toDispatch;
        $uri = trim($uri,'/');


        $vars = explode('/',$uri);

        //Check if module exists
        if($this->modules[$vars[0]]){
            $this->runModule($vars);
        }elseif($this->runController($vars))
        {
            //this runs only controller
        }else{
            Hooks::fire('app.404');
            $this->run404();
        }
    }




    public function runModule($vars)
    {
        if(!file_exists($this->modulePath.$vars[0].'/'.ucfirst($vars[0]).'Module.php'))
            return false;


        $this->controllerPath = 'application/'.$vars[0].'/controllers/';
        $this->module = array_shift($vars);
        $this->runController($vars);
    }





    public function runController($vars = array())
    {
        $controller = array_shift($vars);

        $controller = (null == $controller) ? $this->defaultController : $controller;

        $controllerName = ucfirst($controller).'Controller';
        if(file_exists($this->controllerPath.$controllerName.'.php')){
            include $this->controllerPath.$controllerName.'.php';
            $this->controller = new $controllerName();
            return $this->runAction($vars);
        }
        return false;

    }





    public function runAction($vars)
    {
        $action = array_shift($vars);
        $action = (null == $action)      ? $this->defaultAction     : $action;
        $action = 'action'.ucfirst($action);

        if(method_exists($this->controller,$action))
        {
            call_user_func_array(array($this->controller,$action),$vars);
            return true;
        }elseif(method_exists($this->controller,'action404'))
        {
            call_user_func_array(array($this->controller,'action404'),$vars);
            return true;
        }

        // Go to 404
        return false;
    }





    public function run404()
    {
        exit("<h1>404</h1>");
    }




    public function createComponent($name,$config)
    {
        if(!isset($config['class']))
            throw new SystemException('Where Is Class IN?');

        Base::import($config['class']);
        preg_match('#[a-zA-Z0-9_-]*$#',$config['class'],$m);

        $className = ucfirst($m[0]);
        unset($config['class']);

        $this->_components[$name] = new $className();
        foreach($config as $k=>$v)
            $this->_components[$name]->$k = $v;

        Hooks::fire($name.'.beforeInit');
        $this->_components[$name]->init();
        Hooks::fire($name.'.afterInit');

        return $this->_components[$name];
    }




    public function __destruct()
    {
        Hooks::fire('app.end');
        

    }




    public function __get($name)
    {
        $getter = 'get'.$name;
        if(method_exists($this,$getter))
            return $this->$getter();

        if(isset($this->_components[$name]))
            return $this->_components[$name];

        if($this->magicInitComponent($name))
            return $this->_components[$name];
    }

    public function magicInitComponent($name)
    {

        $config = $this->_config['components'];
        if(isset($config[$name]))
        {
            Base::log('Magic init component: '. $name,'info','system');
            $this->createComponent($name,$config[$name]);
            return true;
        }
        Base::log($name . ' not found in config components','info','system');
        return false;
    }



    public function __set($name,$value)
    {
        $setter = "set".$name;
        if(method_exists($this,$setter))
            return $this->$setter($value);

    }


}