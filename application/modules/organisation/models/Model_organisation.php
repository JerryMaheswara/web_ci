<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_organisation extends CI_Model
{

    /*****************************************************************************/
    public function _get_organisation($limit = null, $offset = null)
    {
        $param_organisation['table'] = 'organisation';
        $organisation                = $this->model_generic->_get($param_organisation, $limit, $offset);
        // foreach ($organisation as $key => $value) {
        //     $value->organisation_tanggal = date('d M Y', strtotime($value->organisation_tanggal));
        // }
        return $organisation;
    }

    /*****************************************************************************/
    public function _get_organisation_id($seller_id = null)
    {
        if (isset($seller_id) && $seller_id != null) {
            $param_organisation['table'] = 'organisation';
            $this->db->where('seller_id  ', $seller_id);
            $organisation = $this->model_generic->_get($param_organisation);
            if ($organisation) {
                return $organisation[0]->organisation_id;
            }
        }
    }
    /*****************************************************************************/
    public function _get_organisation_name($seller_id = null)
    {
        if (isset($seller_id) && $seller_id != null) {
            $param_organisation['table'] = 'organisation';
            $this->db->where('seller_id  ', $seller_id);
            $organisation = $this->model_generic->_get($param_organisation);
            if ($organisation) {
                return $organisation[0]->organisation_name;
            }
        }
    }
    /*****************************************************************************/
    public function _is_mine($organisation_id = null, $seller_id = null)
    {
        if (isset($organisation_id) && $organisation_id != null) {

            $param_organisation['table'] = 'organisation';
            $this->db->where('organisation_id  ', $organisation_id);
            $this->db->where('seller_id  ', $seller_id);
            $_cek = $this->model_generic->_cek($param_organisation);
            return $_cek;
        }
    }
    /*****************************************************************************/
}

/* End of file Model_organisation.php */
/* Location: ./application/modules/organisation/model/Model_organisation.php */
