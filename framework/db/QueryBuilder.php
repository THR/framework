<?php

class QueryBuilder
{
    public $dbh;
    private $stmt;

    protected $sql;
    protected $select;
    protected $from;
    protected $condition;
    protected $group;
    protected $order;
    protected $limit;


    public function __construct($host,$dbname,$user,$pass)
    {

        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        try
        {
            $this->dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        }catch(PDOException $e)
        {
            Base::log($e->getMessage(),'error','database');
        }
    }

    public function query($query,$params = array(),$fetchmode = PDO::FETCH_ASSOC)
    {

        $this->stmt = $this->dbh->prepare($query);
        $this->stmt->execute($params);

        Base::log($this->stmt->queryString,'info','SQL');

        if(strpos($query,'select') === 0)
        {
            return $this->stmt->fetchAll($fetchmode);
        }elseif(stripos($query, 'insert') === 0 ||  stripos($query, 'update') === 0 || stripos($query, 'delete') === 0)
        {
            return $this->stmt->rowCount();
        }else
        {
            return null;
        }


    }



    public function insert($table,$data)
    {
        $keys = array_keys($data);
        $key_handlers = array_map(function($e){ return ':'.$e; },$keys);
        $sth  = $this->dbh->prepare('INSERT INTO '.$table.' ('.implode(',',$keys).') VALUES ('.implode(',',$key_handlers).')');
        $sth->execute($data);
        Base::log($sth->queryString,'info','SQL');
        return $this->dbh->lastInsertId();
    }

    public function select($var)
    {
        $this->select = $var;
        return $this;
    }

    public function from($table)
    {
        $this->from = $table;
        return $this;
    }

    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }




}