<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_setting extends CI_Model {

    /*****************************************************************************/ 
    /*****************************************************************************/
    public function _get_app_id()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $app_name           = $arg[0];
            $param_app['table'] = 'app';
            $this->db->where('app_slug', strtolower($app_name));
            $_cek = $this->model_generic->_cek($param_app);
            if ($_cek) {
                $this->db->where('app_slug', strtolower($app_name));
                $app = $this->model_generic->_get($param_app);
                return $app[0]->app_id;
                // opn($app[0]->app_id);exit();
            }
            // opn($_cek);exit();
        }
    }
    /*****************************************************************************/
    public function _is_subsscribed($organisation_id = null, $app_id = null)
    {
        if (isset($organisation_id) && isset($app_id)) {
            $this->db->where('organisation_id', $organisation_id);
            $this->db->where('app_id', $app_id);
            $param_subscribe['table'] = 'subscribe';
            $_cek                     = $this->model_generic->_cek($param_subscribe);
            if ($_cek) {
                return true;
            }
            return false;
        }
    }
    /*****************************************************************************/
    public function _get_subscribe_status($organisation_id = null, $app_id = null)
    {
        if (isset($organisation_id) && isset($app_id)) {
            $this->db->where('organisation_id', $organisation_id);
            $this->db->where('app_id', $app_id);
            $param_subscribe['table'] = 'subscribe';
            $subscribe                = $this->model_generic->_get($param_subscribe);
            if ($subscribe) {
                return $subscribe[0]->subscribe_status;
            }
        }
    }
    /*****************************************************************************/
    

}

/* End of file Model_setting.php */
/* Location: ./application/modules/setting/models/Model_setting.php */