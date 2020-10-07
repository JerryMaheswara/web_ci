<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends MY_Controller {

  /*****************************************************************************/
  public function index()
  {
    session_destroy();
    redirect(base); 
  }
  /*****************************************************************************/

}

/* End of file Logout.php */
/* Location: ./application/controllers/Logout.php */