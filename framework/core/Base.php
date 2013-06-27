<?php
define('FW_PATH',dirname(dirname(__FILE__)));
define('BASE_PATH',dirname(FW_PATH));
define('LOGGING',true);
class Base
{
    private static $app;
    private static $_classMap = array();
    private static $_includePaths = array();
    private static $_coreClasses = array(
        'Component'=>'core/Component.php',
        'Hooks'=> 'core/Hooks.php',
        'BaseController'=>'core/BaseController.php',
        'Request'=>'web/Request.php',
        'Router'=>'web/Router.php',
        'QueryBuilder'=>'db\QueryBuilder.php',
        //'Database'=>'db\Database.php',
        'SimpleLogger'=>'core\SimpleLogger.php',
        'DbCommander'=>'db\DbCommander.php',
        'DbConnector'=>'db\DbConnector.php',
    );
    private static $_aliases = array('system'=>FW_PATH,'base'=>BASE_PATH);

    private static $_logger;

    public static function app()
    {
        return self::$app;
    }

    public static function setApp($app)
    {
        if(self::$app === null)
            self::$app = $app;
        else
            exit('Sadece bir defa uygulama oluşturabilirsiniz');
    }

    public static function import($path)
    {

            //if aliased
            if(($pos=strpos($path,'.')) !== false)
            {
                $alias = substr($path,0,$pos);
                if(isset(self::$_aliases[$alias]))
                {
                    // If wanted to include a directory
                    if(strpos($path,'*') !== false)
                    {

                    $path = self::getPathofAlias($alias) .DIRECTORY_SEPARATOR. substr($path,$pos+1);
                    $path = str_replace('.',DIRECTORY_SEPARATOR,$path);
                    $path = rtrim($path,'*');
                    if(is_dir($path)){
                        self::$_includePaths[] = $path;
                        return true;
                    }
                    else
                        exit('Alias bulunamadı');

                    }else{
                        $name = explode('.',$path);
                        $className = array_pop($name);
                        $name[0] = self::getPathofAlias($name[0]);
                        $path = implode(DIRECTORY_SEPARATOR,$name) . DIRECTORY_SEPARATOR.$className;
                        if(file_exists($path.'.php'))
                            self::$_classMap[$className] = $path;
                        else
                            exit('File couldn\'t found:' . $path);
                    }
                }

            }
    }

    public static function autoload($name)
    {
        if(isset(self::$_classMap[$name])){
            include self::$_classMap[$name].'.php';
            return true;
        }
        if(isset(self::$_coreClasses[$name])){
            include FW_PATH.DIRECTORY_SEPARATOR.self::$_coreClasses[$name];
        }
        foreach(self::$_includePaths as $path)
        {
            if(file_exists($path.DIRECTORY_SEPARATOR.$name.'.php'))
                include $path.DIRECTORY_SEPARATOR.$name.'.php';
        }


    }
    public static function getPathofAlias($alias)
    {
        if(isset(self::$_aliases[$alias]))
            return self::$_aliases[$alias];
        else
            return null;
    }

    public static function setPathofAlias($alias,$path)
    {
        if(!isset(self::$_aliases[$alias]))
            self::$_aliases[$alias] = $path;

        return self::$_aliases[$alias];
    }

    public static function setLogger($logger)
    {
        self::$_logger = $logger;
    }

    public static function getLogger()
    {
        return self::$_logger;
    }

    /**
     * @param $msg
     * @param string $level Level of the message (info,warning,error)
     * @param $category
     */
    public static function log($msg,$level = 'info', $category = 'application')
    {
        if(!LOGGING)
            return;

        self::$_logger->add($msg,$level,$category);

    }
}