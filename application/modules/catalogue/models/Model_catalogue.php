<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_catalogue extends CI_Model {

    public function _get_catalogue($limit=null, $offset=null)
    {
        $param_catalogue['table'] = 'catalogue';
        $catalogue = $this->model_generic->_get($param_catalogue, $limit, $offset);
        // foreach ($catalogue as $key => $value) {
        //     $value->catalogue_tanggal = date('d M Y', strtotime($value->catalogue_tanggal));
        // }
        return $catalogue;
    }
    

}

/* End of file Model_catalogue.php */
/* Location: ./application/modules/catalogue/model/Model_catalogue.php */