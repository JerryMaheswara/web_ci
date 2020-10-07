<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_anggota extends CI_Model {

    public function _get_anggota($limit=null, $offset=null)
    {
        $param_anggota['table'] = 'anggota';
        $anggota = $this->model_generic->_get($param_anggota, $limit, $offset);
        // foreach ($anggota as $key => $value) {
        //     $value->anggota_tanggal = date('d M Y', strtotime($value->anggota_tanggal));
        // }
        return $anggota;
    }
    

}

/* End of file Model_anggota.php */
/* Location: ./application/modules/anggota/model/Model_anggota.php */