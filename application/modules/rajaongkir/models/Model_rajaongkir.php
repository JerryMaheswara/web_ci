<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_rajaongkir extends CI_Model
{

    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    public function get_province($province_id = null)
    {
        if (isset($province_id)) {
            $this->db->where('province_id', $province_id);
        }

        $param_province['table'] = 'rajaongkir_province';
        $province                = $this->model_generic->_get($param_province);
        foreach ($province as $value) {
            $value->id    = $value->province_id;
            $value->text  = $value->province;
            $value->label = $value->province;
        }
        return $province;

    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function get_city($city_id = null)
    {
        if (isset($city_id)) {
            $this->db->where('city_id', $city_id);
        }

        $param_city['table'] = 'rajaongkir_city';
        $city                = $this->model_generic->_get($param_city);
        foreach ($city as $value) {
            $value->id    = $value->city_id;
            $value->text  = $value->city_name;
            $value->label = $value->city_name;
        }
        return $city;

    }
    /*****************************************************************************/
    /*****************************************************************************/

}

/* End of file Model_rajaongkir.php */
/* Location: ./application/modules/rajaongkir/models/Model_rajaongkir.php */
