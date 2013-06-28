<?php
class DefaultController extends BaseController
{

    public function actionIndex($name='tahir')
    {
        //var_dump(func_get_args());
        //var_dump($_GET);
        Base::app()->db;
    }


    public function actionDatabase()
    {
        Base::import('system.db.Database');
        Base::import('application.helpers.HtmlHelper');
        $db = new Database('bugraguney.com.tr','bugragun_ar','bugragun_ar','ASD123qwe');

        $result = $db->query('INSERT INTO lorem (name,value) VALUES (?,?)',array('tahir','uzaktan'));
        var_dump($result);

        $result = $db->query('SELECT name,value FROM lorem ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
        HtmlHelper::makeTable($result,array('Name','Value'));

        var_dump($db->insert('lorem',array('tahir','insert function')));



        var_dump(Base::getPathofAlias('application'));
        $logs = Base::getLogger()->getLogsByCategory('SQL');
        HtmlHelper::makeTable($logs,array('Mesaj','Level','Type','Zaman'));
    }

    public function actionCommander()
    {
        $com = Base::app()->db->commander();


        /*var_dump( Base::app()->db->commander("UPDATE lipsum SET name = ".time()." WHERE id>60")->execute() );
        echo '--------------';
        var_dump( $com->insert('lipsum',array('name'=>'commander','value'=>'tahir'))->execute() );
        echo '------------------';
        var_dump( $com->update('lipsum',array('name'=>'commander','value'=>'update'))->where(rand(1,50))->execute() );
        */
        echo '--- WHERE ARRAY  ---------------';
        var_dump( $com->update('lipsum',array('name'=>rand(1,1000),'value'=>'update'))->where(array('id'=>rand(1,20)))->execute() );

        echo '--- WHERE PK  ---------------';
        var_dump( $com->update('lipsum',array('name'=>rand(1,1000),'value'=>'update'))->where(rand(1,23))->execute() );

        echo '--- WHERE String with bind  ---------------';
        var_dump(
            $com->
                update('lipsum',array('name'=>rand(1,1000),'value'=>'string with param'))->
                where('id < ?',array(10))->
                execute()
        );



        Base::import('application.helpers.HtmlHelper');
        $logs = Base::getLogger()->getLogsByCategory('SQL');

        HtmlHelper::makeTable($logs,array('Mesaj','Level','Type','Zaman'));
        var_dump($com);
    }
}