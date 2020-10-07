<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_seller extends CI_Model {

    public function _get_seller($limit=null, $offset=null)
    {
        $param_seller['table'] = 'seller';
        $seller = $this->model_generic->_get($param_seller, $limit, $offset);
        // foreach ($seller as $key => $value) {
        //     $value->seller_tanggal = date('d M Y', strtotime($value->seller_tanggal));
        // }
        return $seller;
    }
    

}

/* End of file Model_seller.php */
/* Location: ./application/modules/seller/model/Model_seller.php */