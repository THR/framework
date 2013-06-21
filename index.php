<?php

error_reporting(-1);
require_once "framework/Application.php";

$app = new Application();


/*
Hooks::add('route.beforeDecide',function(){

    if(substr(Base::app()->request->uri,0,2) == 'en' )
    {
        Base::app()->request->uri = substr(Base::app()->request->uri,3);
    }
    var_dump(Base::app()->request);
});

*/
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


Hooks::add('database.beforeInsert',function($table,$params){
    echo "<p>$table tablosuna ".print_r($params,true)." verileri eklenmeye başlıyor</p>";
});
Router::add('tahir','tahir/<name>','default/index');
Router::add('database','database','default/database');
$app->run();
