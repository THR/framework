<?php

error_reporting(-1);
require_once "framework/Application.php";

$app = new Application();



Hooks::add('route.beforeDecide',function(){
    $request = Request::get_instance();
    if(substr($request->uri,0,2) == 'en' )
    {

       $request->toDispatch ='/default/database/';
    }
});


/*
Hooks::add('request.beforeInit',function(){
   var_dump(Base::app()->request);
});

Hooks::add('request.afterInit',function(){
   var_dump(Base::app()->request);
});
*/

/*
Router::add('blog.index','GET blog','blog/default/index');
Router::add('blog.showPosta','<module>/show/<id:\d+>','blog/post/show');
Router::add('blog.showPost','blog/<slug>.html','blog/post/show');
*/
Hooks::add('app.end',function(){
    var_dump(Base::getLogger()->getLogsByCategory('SQL'));
});
Router::add('tahir','tahir/<name>','default/index');
$app->run();
