<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_card extends CI_Model {

    public function _get_card($limit=null, $offset=null)
    {
        $param_card['table'] = 'card';
        $card = $this->model_generic->_get($param_card, $limit, $offset);
        // foreach ($card as $key => $value) {
        //     $value->card_tanggal = date('d M Y', strtotime($value->card_tanggal));
        // }
        return $card;
    }
    

}

/* End of file Model_card.php */
/* Location: ./cardlication/modules/card/model/Model_card.php */