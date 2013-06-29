<?php
/**
 * #FRAMEWORKNAME#
 *
 * An open source PHP framework
 *
 * @package		#FRAMEWORKNAME#
 * @license		http://codeigniter.com/user_guide/license.html
 * @since		Version 1.0
 */
class DbCommander {

    public $pdo;
    public $query;
    public $stmt;

    /**
     * Type: Insert|Update|Delete|Select
     * Table: table name
     * Where:
     * WhereType: array|string|string_with_array|pk
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

    const WHERE_ARRAY   = 0;
    const WHERE_PK      = 1;
    const WHERE_STRING  = 2;
    const WHERE_STRING_WITH_PARAMS  = 3;


    /**
     * Constructor
     * @param DbConnector $connection The database connection
     * @param null $query You can pass any sql stament to be executed
     */

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

    /**
     * Executes the sql stament
     * This method executes non-query sql statements like that insert, delete, update
     *
     * if sql statement is an insert query returns inserted row
     * else affected row count
     * @return mixed
     */
    public function execute()
    {
        if($this->query == null)
            $this->prepareQuery();

        try{
            $this->stmt = $this->pdo->prepare($this->query);
            $this->stmt->execute($this->params);
        }catch (PDOException $e)
        {
            echo $this->query;
            var_dump($e);
        }


        Base::log('Query (Executed): '.$this->readableQuery(),'info','SQL');
        if(!isset($this->qParts['type']))
            $this->findType();

        if($this->qParts['type'] === 'insert')
            return $this->pdo->lastInsertId();
        return $this->stmt->rowCount();
    }

    /**
     * Constructor'da bir sql gönderilmemişse
     * DbCommander::qParts'dan bir sql oluşturmaya çalışır
     *
     * DbCommander::execute ve DbCommander::query den çağırılarak çalıştırılır
     */
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


    protected  function insertQuery()
    {
        $query[] = "INSERT INTO";
        $query[] = $this->qParts['table'];
        $query[] = '('.$this->getFields().')';
        $query[] = 'VALUES';
        $query[] = '('.$this->getPlaceholders().')';

        $this->query = implode(' ',$query);

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

    public function delete($tableName)
    {
        $this->reset();
        $this->qParts['type'] = 'delete';
        $this->qParts['table'] = $tableName;
        return $this;
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



        if( is_array($condition) )
        {
            $this->qParts['whereType'] = self::WHERE_ARRAY;
            $this->qParts['where'] = $condition;
            $this->qParts['whereOperator'] = $op;
            $this->qParts['whereGlue']     = $glue;
        }
        elseif(is_int($condition))
        {
            $this->qParts['whereType'] = self::WHERE_PK;
            $this->qParts['where']     = $condition;
        }
        else
        {
            if(is_array($op))
            {
                $this->qParts['whereType'] = self::WHERE_STRING_WITH_PARAMS;
                $this->qParts['where'] = $condition;
                $this->qParts['whereParams'] = $op;
            }else {
                $this->qParts['whereType'] = self::WHERE_STRING;
                $this->qParts['where'] = $condition;
            }
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

        if($this->qParts['whereType'] === self::WHERE_ARRAY)
        {
            $array = $this->qParts['where'];
            //array('id'=43) TO id = ?

            $keys = array_keys($array);

            foreach($keys as $key)
            {
                $querySub[] = $key . ' = ?';
                $this->bind($this->qParts['where'][$key]);
            }

            $query[] = implode($this->qParts['whereGlue'],$querySub);


        }
        elseif($this->qParts['whereType'] === self::WHERE_STRING_WITH_PARAMS)
        {
            $query[] = $this->qParts['where'];
            $this->bindArray($this->qParts['whereParams']);
        }
        elseif($this->qParts['whereType'] === self::WHERE_STRING)
        {
            $query[] = $this->qParts['where'];
        }
        elseif($this->qParts['whereType'] === self::WHERE_PK)
        {
            $this->bind($this->qParts['where']);
            $query[] =  'id = ?';
        }
        return implode(" ",$query);

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
        $keys = array();
        $values = array();

        # build a regular expression for each parameter
        foreach ($this->params as $key=>$value)
        {
            if (is_string($key))
            {
                $keys[] = '/:'.$key.'/';
            }
            else
            {
                $keys[] = '/[?]/';
            }

            if(is_numeric($value))
            {
                $values[] = intval($value);
            }
            else
            {
                $values[] = '"'.$value .'"';
            }
        }

        $query = preg_replace($keys, $values, $this->query, 1, $count);
        return $query;
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