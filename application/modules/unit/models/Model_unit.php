<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_unit extends CI_Model {

    public function _get_unit($limit=null, $offset=null)
    {
        $param_unit['table'] = 'unit';
        $unit = $this->model_generic->_get($param_unit, $limit, $offset);
        // foreach ($unit as $key => $value) {
        //     $value->unit_tanggal = date('d M Y', strtotime($value->unit_tanggal));
        // }
        return $unit;
    }
    

}

/* End of file Model_unit.php */
/* Location: ./application/modules/unit/model/Model_unit.php */