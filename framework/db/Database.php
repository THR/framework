<?php
class Database
{
    public $pdo;
    public $stmt;

    public function __construct($host,$db,$user,$pass)
    {
        $dsn = 'mysql:host='.$host.';dbname='.$db;
        try{
            $this->pdo = new PDO($dsn,$user,$pass);
        }catch (PDOException $e)
        {
            echo $e->getMessage();
            Hooks::fire('database.connection.error',array('error'=>$e));
            Base::log('PDO Connection Error: '.$e->getMessage(),'error','DB');
        }


    }

    public function query($query,$params = array())
    {
        Hooks::fire('database.beforeQuery',array('query'=>$query));
        try{
            $this->stmt = $this->pdo->prepare($query);
            $this->stmt->execute($params);
            Base::log($query,'info','SQL');
            return $this->stmt;
        }catch (PDOException $e)
        {
            echo $e->getMessage();
            Hooks::fire('database.query.error',array('error'=>$e));
            Base::log('PDO Connection Error: '.$e->getMessage(),'error','DB');
        }
    }

    public function fetchAll($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetchAll($fetchMode);
    }

    public function fetch($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetch($fetchMode);
    }

    public function insert($tableName,$params = null)
    {
        Hooks::fire('database.beforeInsert',array('table'=>$tableName,'params'=>&$params));
        if($params === null)
            return false;

        var_dump($params);
    }
}