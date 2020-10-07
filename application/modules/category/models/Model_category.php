<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_category extends CI_Model {

    public function _get_category($limit=null, $offset=null)
    {
        $param_category['table'] = 'category';
        $category = $this->model_generic->_get($param_category, $limit, $offset);
        // foreach ($category as $key => $value) {
        //     $value->category_tanggal = date('d M Y', strtotime($value->category_tanggal));
        // }
        return $category;
    }
    

}

/* End of file Model_category.php */
/* Location: ./application/modules/category/model/Model_category.php */