<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Country extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
    }
    /*****************************************************************************/
    public function country_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param['table'] = 'country';
        $this->db->select('country_name as text, country_id as id');
        $this->db->where('country_disabled', 0);
        $country = $this->model_generic->_get($param);
        $country = json_encode($country, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $country;
        // }
    }
    /*****************************************************************************/
    public function provinsi_ajax()
    {
        // if ($this->input->is_ajax_request()) {

        $param_rgn_province['table'] = 'rgn_province';
        $this->db->where('rgn_province.disabled', 0);
        $rgn_province = $this->model_generic->_get($param_rgn_province);
        $rgn_province = json_encode($rgn_province, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $rgn_province;
        // }

    }
    /*****************************************************************************/
    public function city_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $province_id = $arg[0];
            $this->db->where('province_id', $province_id);
        }

        $param_rgn_city['table'] = 'rgn_city';
        $rgn_city                = $this->model_generic->_get($param_rgn_city);
        $rgn_city                = json_encode($rgn_city, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $rgn_city;

        // }
    }
    /*****************************************************************************/
    /*************** kecamatan ******************************************************/
    public function district_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $city_id = $arg[0];
            $this->db->where('city_id', $city_id);
        }

        $param_rgn_district['table'] = 'rgn_district';
        $rgn_district                = $this->model_generic->_get($param_rgn_district, $this->_limit);
        $rgn_district                = json_encode($rgn_district, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $rgn_district;

        // }
    }
    /*****************************************************************************/
    public function subdistrict_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $district_id = $arg[0];
            $this->db->where('district_id', $district_id);
        }

        $param_rgn_subdistrict['table'] = 'rgn_subdistrict';
        $rgn_subdistrict                = $this->model_generic->_get($param_rgn_subdistrict, $this->_limit);
        $rgn_subdistrict                = json_encode($rgn_subdistrict, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $rgn_subdistrict;

        // }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function kelurahan_ajax()
    {
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $this->db->group_start();
            $this->db->or_like('kelurahan', $this->input->get('search'));
            $this->db->or_like('kecamatan', $this->input->get('search'));
            $this->db->or_like('kabupaten', $this->input->get('search'));
            $this->db->or_like('propinsi', $this->input->get('search'));
            $this->db->or_like('kodepos', $this->input->get('search'));
            $this->db->group_end();
        }
        if ($this->input->get('id')) {
            $this->db->where('kodepos_id', $this->input->get('id'));
        }
        $param_kodepos['table'] = 'kodepos';
        // $this->db->like('propinsi', 'kalimantan');
        $this->db->like('kabupaten', 'Balikpapan');
        $kodepos = $this->model_generic->_get($param_kodepos);
        foreach ($kodepos as $key => $value) {
            $value->id   = $value->kodepos_id;
            $value->text = $value->kelurahan . ', ' . $value->kecamatan . ', ' . $value->jenis_kabupaten . ' ' . $value->kabupaten . ', ' . $value->propinsi . ', ' . $value->kodepos;
        }
        // echo(json_encode(lq()));exit();
        $kodepos = json_encode($kodepos, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $kodepos;
    }
    /*****************************************************************************/
    public function kodepos_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        if ($this->input->get('city_id')) {
            $this->db->where('city_id', $this->input->get('city_id'));
            if ($this->input->get('district_id')) {
                $this->db->where('district_id', $this->input->get('district_id'));
                if ($this->input->get('subdistrict_id')) {
                    $this->db->where('subdistrict_id', $this->input->get('subdistrict_id'));
                }
            }
        }

        $param_rgn_postcode['table'] = 'rgn_postcode';
        $rgn_postcode                = $this->model_generic->_get($param_rgn_postcode, $this->_limit);
        $rgn_postcode                = json_encode($rgn_postcode, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $rgn_postcode;

        // }
    }
    /*****************************************************************************/
}

/* End of file Country.php */
/* Location: ./application/controllers/Country.php */
