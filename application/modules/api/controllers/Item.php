<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item extends MY_Oauth
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function detail()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $param = $arg[0];
            opn($param);exit();
        }
    }
    /*****************************************************************************/
}

/* End of file Item.php */
/* Location: ./application/controllers/Item.php */
