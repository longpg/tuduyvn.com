<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function getModel($modelName)
    {
        if (!property_exists(get_class($this), $modelName))
            $this->load->model($modelName);
        return $this->$modelName;
    }
}
