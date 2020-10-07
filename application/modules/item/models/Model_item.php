<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_item extends CI_Model {

    /*****************************************************************************/
    public function _get_item($limit=null, $offset=null)
    {
        $param_item['table'] = 'item';
        $item = $this->model_generic->_get($param_item, $limit, $offset);
        foreach ($item as $key => $value) {
            $value->item_price_format = number_format($value->item_price,0,',','.');
            
        //     $value->item_tanggal = date('d M Y', strtotime($value->item_tanggal));
        }
        return $item;
    }
    
    /*****************************************************************************/

    public function _is_mine($item_id = null, $user_id = null)
    {
        if (isset($item_id) && $item_id != null) {

            $param_item['table'] = 'item';
            $this->db->where('item_id  ', $item_id);
            $this->db->where('user_id  ', $user_id);
            $_cek = $this->model_generic->_cek($param_item);
            return $_cek;
        }
    }
    /*****************************************************************************/
}

/* End of file Model_item.php */
/* Location: ./application/modules/item/model/Model_item.php */