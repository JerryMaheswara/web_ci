<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_user extends CI_Model {

    public function _get_user($limit=null, $offset=null)
    {
        $param_user['table'] = 'user';
        $user = $this->model_generic->_get($param_user, $limit, $offset);
        // foreach ($user as $key => $value) {
        //     $value->user_tanggal = date('d M Y', strtotime($value->user_tanggal));
        // }
        return $user;
    }
    

}

/* End of file Model_user.php */
/* Location: ./application/modules/user/model/Model_user.php */