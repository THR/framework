<?php
$hooks = array();
function filesInDir($path = null)
{
    if($path === null)
        return false;

    $path = $path . '/*';
    $files = glob($path);

    foreach($files as $file)
    {
        if(is_dir($file))
        {
            filesInDir($file);
        }else{
            searchHook($file);
        }
    }



}

function searchHook($filename)
{
    global $hooks;
    $file = file_get_contents($filename);
    $pattern = '#Hooks::fire\((.*?)[,|)]#';

    preg_match_all($pattern,$file,$matches);

    if(count($matches[1])<1)
        return false;
    $matches = array_map(function($v){
        return trim($v,'\'');
    },$matches[1]);
    $hooks[$filename] = $matches;
}

filesInDir('framework');
var_dump($hooks);