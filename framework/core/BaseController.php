<?php

class BaseController
{

    // Layout for render
    public $layout = null;
    /**
     * Default 404 Action for each controller.
     *
     * When action couldn't found, system looks for action404
     * method which defined in own controller.
     */
    public function action404()
    {
        Hooks::fire('controller.404');
    }
}