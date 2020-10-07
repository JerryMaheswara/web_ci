<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_brand extends CI_Model {

    public function _get_brand($limit=null, $offset=null)
    {
        $param_brand['table'] = 'brand';
        $brand = $this->model_generic->_get($param_brand, $limit, $offset);
        // foreach ($brand as $key => $value) {
        //     $value->brand_tanggal = date('d M Y', strtotime($value->brand_tanggal));
        // }
        return $brand;
    }
    

}

/* End of file Model_brand.php */
/* Location: ./application/modules/brand/model/Model_brand.php */