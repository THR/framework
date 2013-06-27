<?php

class DbConnector extends Component
{

    protected  $_pdo;
    public $dsn;
    public $username;
    public $password;

    public function init()
    {
        $this->_pdo = new PDO($this->dsn,$this->username,$this->password);
        $this->_pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }

    public function getPdo()
    {
        return $this->_pdo;
    }
    public function commander($query = null)
    {
        return new DbCommander($this,$query);
    }


}