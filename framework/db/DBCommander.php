<?php

class DbCommander {

    public $pdo;
    public $query;
    public $stmt;

    /**
     * Type: Insert|Update|Delete|Select
     * Table: table name
     * @var array
     */
    public $qParts = array();

    /**
     * Params to bind
     * @var array
     */
    public $params = array();

    public $fields = array();
    public $placeholders = array();

    public $lastQuery;





    public function __construct(DbConnector $connection,$query = null)
    {
        $this->pdo = $connection->getPdo();
        if($query)
            $this->query = $query;
    }


    public function query()
    {

        if($this->query == null)
            $this->prepareQuery();

        $this->stmt = $this->pdo->prepare($this->query);
    }

    public function execute()
    {
        if($this->query == null)
            $this->prepareQuery();

        $this->stmt = $this->pdo->prepare($this->query);
        $this->stmt->execute($this->params);


        Base::log('Query (Executed): '.$this->readableQuery(),'info','SQL');
        if(!isset($this->qParts['type']))
            $this->findType();

        if($this->qParts['type'] === 'insert')
            return $this->pdo->lastInsertId();
        return $this->stmt->rowCount();
    }

    public function prepareQuery()
    {
        switch($this->qParts['type']){
            case 'insert':
                $this->insertQuery();
                break;
            case 'update':
                $this->updateQuery();
                break;
            case 'delete':
                $this->deleteQuery();
                break;
            case 'select':
                $this->selectQuery();
                break;
            default:
                //doSomething
                break;
        }
    }

    protected  function insertQuery()
    {
        $query[] = "INSERT INTO";
        $query[] = $this->qParts['table'];
        $query[] = '('.$this->getFields().')';
        $query[] = 'VALUES';
        $query[] = '('.$this->getPlaceholders().')';

        $this->query = implode(' ',$query);

    }

    public function insert($tableName,$params=array(),$execute = false)
    {
        $this->reset();
        $this->qParts['type']   = 'insert';
        $this->qParts['table']  = $tableName;
        $this->bindArray($params);

        if($execute)
            return $this->execute();
        else
            return $this;
    }


    public function update($tableName,$params,$where,$execute = false)
    {
        $this->reset();
        $this->qParts['type']   = 'update';
        $this->qParts['table']  = $tableName;
        $this->qParts['where']  = $where;
        $this->bindArray($params);

        if($execute)
            return $this->execute();
        else
            return $this;
    }



    public function updateQuery()
    {
        $query[] = "UPDATE";
        $query[] = $this->qParts['table'];
        $query[] = "SET";
        $query[] = $this->updateSetString();
        $query[] = "WHERE";

        //@todo do where condition
        $query[] = $this->whereCondition();

        $this->query = implode(' ',$query);
    }

    public function updateSetString()
    {

        foreach($this->fields as $v)
        {
            $query[] =  $v.' = ?';
        }

        return implode(', ',$query);
    }

    public function delete($tableName,$where)
    {
        $this->reset();
        $this->qParts['type'] = 'delete';
        $this->qParts['table'] = $tableName;
        $this->qParts['where'] = $where;
    }
    public function where()
    {
        
    }
    public function whereCondition()
    {
        $where = $this->qParts['where'];

        if(isset($this->qParts['whereType']))
        {



        }else {


            if(is_string($where))
                return $where;

            if(is_int($where))
                return 'id = '. $where;

        }
    }

    public function bindArray($params)
    {
        $this->fields = array_keys($params);


        foreach($params as $v)
        {
            $this->bind($v);
        }


    }

    public function bind($param)
    {
        $this->placeholders[] = '?';
        $this->params[]       = $param;
    }

    /**
     * @return string imploded with commas EG: "id, username, password"
     */
    public function getFields()
    {
        return implode(', ',$this->fields);
    }

    /**
     * @return string imploded with commas EG: ":id, :username, :passowrd"
     */
    public function getPlaceholders()
    {
        return implode(', ', $this->placeholders);
    }

    /**
     * @return string Readable query like "INSERT INTO (id) VALUES (1)" (replaced placeholders to values
     */
    public function readableQuery()
    {
        return str_replace(array_keys($this->params),$this->params,$this->query);
    }

    public function findType()
    {
        $pos = strpos($this->query,' ');
        $pos =  strtolower( substr($this->query,0,$pos) );
        $this->qParts['type'] = $pos;
    }

    public function reset()
    {
        $this->params = null;
        $this->fields = null;
        $this->placeholders = null;
        $this->lastQuery = $this->query;
        $this->query = null;
        $this->qParts = null;
    }

}