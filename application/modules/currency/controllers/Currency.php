 <?php
defined('BASEPATH') or exit('No direct script access allowed');

class Currency extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
    }

    /*****************************************************************************/
    public function currency_ajax()
    {
        if ($this->input->is_ajax_request()) {
            $param['table'] = 'currency'; 
            $this->db->select('currency_id,currency_code,country_name,currency_spelling,currency_symbol');
            $this->db->where('currency_disabled', 0);
            $currency = $this->model_generic->_get($param);
            foreach ($currency as $key => $value) {
                $value->id = $value->currency_id;
                $value->text = $value->currency_code . ' (' . $value->currency_spelling . ')';
            }
            $currency = json_encode($currency, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $currency;
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Currency.php */
/* Location: ./application/controllers/Currency.php */
