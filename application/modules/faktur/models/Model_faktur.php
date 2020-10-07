<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_faktur extends CI_Model {

    public function _get_faktur($limit=null, $offset=null)
    {
        $param_faktur['table'] = 'faktur';
        $faktur = $this->model_generic->_get($param_faktur, $limit, $offset);
        foreach ($faktur as $key => $value) {
            $value->faktur_tanggal = date('d M Y', strtotime($value->faktur_tanggal));
            $value->faktur_icon = ($value->faktur_status=='1')?'square-o':'check ';
            $value->faktur_color = ($value->faktur_status=='1')?'green':'green';
        }
        return $faktur;
    }
    

}

/* End of file Model_faktur.php */
/* Location: ./application/modules/faktur/model/Model_faktur.php */