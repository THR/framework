<?php

class DbCommander {

    public $pdo;
    public $query;
    public $stmt;

    /**
     * Type: Insert|Update|Delete|Select
     * Table: table name
     * Where:
     * WhereType: array|string
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


    public function update($tableName,$params,$execute = false)
    {
        $this->reset();
        $this->qParts['type']   = 'update';
        $this->qParts['table']  = $tableName;
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

    public function deleteQuery()
    {
        $query[] = "DELETE";
        $query[] = "FROM";
        $query[] = $this->qParts['table'];
        $query[] = $this->whereCondition();

        $this->query = implode(" ",$query);

    }

    public function where($condition,$op = '=',$glue='AND')
    {
        $this->qParts['whereOperator'] = $op;
        $this->qParts['whereGlue']     = $glue;

        if( is_array($condition) )
        {
            $this->qParts['whereType'] = 'array';
            $this->qParts['where'] = $condition;
        }
        elseif(is_int($condition))
        {
            $this->qParts['whereType'] = 'pk';
            $this->qParts['where']     = $condition;
        }
        else
        {
            $this->qParts['whereType'] = 'string';
            $this->qParts['where'] = $condition;
        }
        return $this;
    }

    public function whereCondition()
    {
        if(!isset($this->qParts['where']))
        {
            return '';
        }

        $query[] = "WHERE";

        if($this->qParts['whereType'] === 'array')
        {
            $array = $this->qParts['where'];
            //array('id'=43) TO id = ?

            $keys = array_keys($array);



        }
        elseif($this->qParts['whereType'] === 'string')
        {

        }
        elseif($this->qParts['whereType'] === 'pk')
        {

        }
        var_dump($this->qParts);

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
        $pos = stripos($this->query,' ');
        $pos = substr($this->query,0,$pos) ;
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