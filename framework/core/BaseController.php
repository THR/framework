<?php

class BaseController
{

    /**
     * Controllerdaki actionlar için varsayılan layout dosyası
     * @var null
     */
    public $layout = null;

    /**
     * @param string $_file  View File Name
     * @param array $_param  Params which can be used in render file
     * @return string Output
     * @throws SystemException
     */
    public function renderPartial($_file,$_param=array())
    {

        if(file_exists(Base::app()->theme->viewPath . $_file . '.php'))
            $_file = Base::app()->theme->viewPath . $_file . '.php';
        elseif(file_exists(Base::app()->viewPath . $_file.'.php'))
            $_file = Base::app()->viewPath . $_file.'.php';
        else
            #@todo exception throw new SystemException('View File Not Found:'.$_file);


        if(is_array($_param))
            extract($_param);


        ob_start();
        ob_implicit_flush(false);
        require($_file);
        return ob_get_clean();


    }

    /**
     * @param $_file string view File Name
     * @param array $_param array Params which can be used in render file
     * @param bool $_return if true it returns output otherwise echoes output
     * @return string output
     * @throws SystemException
     */
    public function render($_file,$_param = array(),$_return = false)
    {

        $output = $this->renderPartial($_file,$_param);
        if(!empty($this->layout))
        {
            $output = $this->renderPartial($this->layout,array('content'=>$output));
        }

        if($_return)
            return $output;
        else
            echo $output;
    }

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