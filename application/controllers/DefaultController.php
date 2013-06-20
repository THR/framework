<?php
class DefaultController
{

    public function actionIndex($name='tahir')
    {
        var_dump(func_get_args());
    }
    public function actionDatabase()
    {
        $db = new QueryBuilder('bugraguney.com.tr','bugragun_ar','bugragun_ar','ASD123qwe');

        $data = array('name'=>'tahir','value'=>microtime());

        var_dump($db->insert('lorem',$data));

        var_dump($db->query("select * FROM lorem"));
    }
}