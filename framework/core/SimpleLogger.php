<?php
class SimpleLogger
{
    protected $logs;
    public function add($msg,$level = 'info' ,$category = 'application')
    {
        $this->logs[] = array($msg,$level,$category,microtime());
    }


    public function getLogsByCategory($cat)
    {
        $return = array();
        foreach($this->logs as $log)
        {
            if($log[2]==$cat)
                $return[] = $log;
        }
        return $return;
    }
    public function getLogs()
    {
        return $this->logs;
    }

}