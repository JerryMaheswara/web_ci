<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MY_Oauth
{

    /*****************************************************************************/
    protected $scope         = '';
    protected $state         = '';
    protected $client_id     = '';
    protected $redirect_uri  = '';
    protected $response_type = '';
    /*****************************************************************************/
    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');

        // $x = email_to_username('jsas@asda.das');
        // $y = username_to_email($x);
        // opn($x);
        // opn($y);exit();

    }
    /*****************************************************************************/
    public function index()
    {
        echo '--';
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function detail()
    {
        // $this->require_scope("userinfo");
        $arg = func_get_args();
        if (isset($arg[0])) {
            $param = $arg[0];
            opn($param);exit();
        }
    }
    /*****************************************************************************/
}

/* End of file Profile.php */
/* Location: ./application/controllers/Profile.php */
