<?php
class Component
{
    public function init()
    {

    }

    public function __get($name)
    {
        $getter = 'get'.$name;

        if(method_exists($this,$getter))
            return $this->$getter();
    }
}