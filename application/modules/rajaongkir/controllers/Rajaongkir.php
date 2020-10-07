<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rajaongkir extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        $this->load->library('library_module');

        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);
        $this->load->model('model_rajaongkir');
    }

    /*****************************************************************************/
    public function province()
    {
        $province_id = null;
        $arg         = func_get_args();
        if (isset($arg[0])) {
            $province_id = $arg[0];
        }

        $province = $this->model_rajaongkir->get_province($province_id);
        if ($this->input->is_ajax_request()) {
            echo json_encode($province);
        } else {

            opn($province);exit();
        }

    }
    /*****************************************************************************/
    public function city()
    {
        $city_id = null;
        $arg     = func_get_args();
        if (isset($arg[0])) {
            $city_id = $arg[0];
        }

        $city = $this->model_rajaongkir->get_city($city_id);
        if ($this->input->is_ajax_request()) {
            echo json_encode($city);
        } else {
            opn($city);exit();
        }
        opn($pro);exit();

    }
    /*****************************************************************************/
    public function _province_grab()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $province_id = $arg[0];
        } else {

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, 'https://api.rajaongkir.com/starter/province');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('key: e3d3f2059dd13e3f38c68a94217af818'));

            $response = curl_exec($curl);
            $err      = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                // echo $response;
                $response = json_decode($response);
                foreach ($response->rajaongkir->results as $value) {
                    $param_province['province_id'] = $value->province_id;
                    $param_province['province']    = $value->province;
                    $param_province['table']       = 'rajaongkir_province';
                    $this->db->where('province_id', $value->province_id);
                    $_cek = $this->model_generic->_cek($param_province);
                    if (!$_cek) {
                        $this->model_generic->_insert($param_province);
                    }

                }
                opn($response);exit();
            }
        }
    }
    /*****************************************************************************/
    public function _city_grab()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $city_id = $arg[0];
        } else {

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, 'https://api.rajaongkir.com/starter/city');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('key: e3d3f2059dd13e3f38c68a94217af818'));

            $response = curl_exec($curl);
            $err      = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                // echo $response;
                $response = json_decode($response);
                foreach ($response->rajaongkir->results as $value) {
                    $param_city['city_id']     = $value->city_id;
                    $param_city['province_id'] = $value->province_id;
                    $param_city['province']    = $value->province;
                    $param_city['type']        = $value->type;
                    $param_city['city_name']   = $value->city_name;
                    $param_city['postal_code'] = $value->postal_code;
                    $param_city['table']       = 'rajaongkir_city';
                    $this->db->where('city_id', $value->city_id);
                    $_cek = $this->model_generic->_cek($param_city);
                    if (!$_cek) {
                        $this->model_generic->_insert($param_city);
                    }

                }
                opn($response);exit();
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function index_()
    {
        $this->data['content'] = $this->parser->parse('rajaongkir.html', $this->data, true);
        $this->data['body']    = $this->parser->parse('dashboard/_content_wrapper.html', $this->data, true);

        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Rajaongkir.php */
/* Location: ./application/controllers/Rajaongkir.php */
