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

        echo '---- INSERT ---------';
        var_dump(
            $com->
                insert('lipsum',array('name'=>uniqid(),'value'=>__FILE__))->
                execute()
        );
        echo '--- WHERE ARRAY  ---------------';
        var_dump( $com->update('lipsum',array('name'=>rand(1,1000),'value'=>'update'))->where(array('id'=>rand(1,20)))->execute() );

        echo '--- WHERE PK  ---------------';
        var_dump( $com->update('lipsum',array('name'=>rand(1,1000),'value'=>'update'))->where(rand(1,23))->execute() );

        echo '--- WHERE String with bind  ---------------';
        var_dump(
            $com->
                update('lipsum',array('name'=>rand(1,1000),'value'=>'string with param'))->
                where('id < ?',array(rand(1,10)))->
                execute()
        );

        echo '---- DELETE  -------';
        var_dump(
            $com->
                delete('lipsum')->
                where('id < ?',array(rand(1,100)))->
                execute()
        );



        Base::import('application.helpers.HtmlHelper');
        $logs = Base::getLogger()->getLogsByCategory('SQL');
        $last = $logs[0][3];
        $logs = array_map(
            function($e) use ($last){
                $e[4] = $e[3]-$last;
                $last = $e[3];
                return $e;
            },$logs
        );
        HtmlHelper::makeTable($logs,array('Mesaj','Level','Type','Zaman','Fark'));
    }
}