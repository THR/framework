<?php
class DefaultController extends BaseController
{

    public function actionIndex($name='tahir')
    {
        //var_dump(func_get_args());
        //var_dump($_GET);
        var_dump(Base::app()->language);
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
}