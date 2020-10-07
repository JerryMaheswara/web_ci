<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Billing extends MY_Controller
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

        $this->data['role_label_id'] = '';
        $this->data['search']        = '';
        $this->data['limit']         = $this->_limit;
        $this->data['offset']        = $this->_offset;

        $_module_id               = $this->library_module->_module_id(strtolower(controller));
        $this->data['breadcrumb'] = $this->library_module->_module_breadcrumb($_module_id);

        $this->data['today']       = date('Y-m-d');
        $this->data['_is_allowed'] = $this->_is_allowed(array(role_admin)) ? '' : 'hidden destroy';
        $this->data['status']      = $this->input->get('status');
    }
    /*****************************************************************************/
    public function index()
    {
        if ($this->_is_allowed(array(role_admin, role_user))) {
            $param_subscribe['table'] = 'subscribe';
            $total_rows = $this->model_generic->_count($param_subscribe);
            $_cek_subscribe = $this->model_generic->_cek($param_subscribe);
            $config_base_url = base . '/'. controller;
            $this->data['search'] = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                // $this->db->or_like('subscribe.subscribe_name', $this->input->get('search'));
            }
            $subscribe = $this->model_generic->_get($param_subscribe, $this->_limit, $this->_offset);
            $this->data['subscribe'] = $subscribe;
            $subscribe = json_encode($subscribe, JSON_PRETTY_PRINT);
            // header('Content-Type: application/json');
            // echo $subscribe;
                        
            $this->data['total_rows']           = $total_rows;
            $this->load->library('pagination');
            $config['base_url']             = $config_base_url; 
            $config['total_rows']           = $total_rows;
            $config['query_string_segment'] = 'offset';
            $config['per_page']             = $this->_limit;
            $this->pagination->initialize($config);
            $this->data['paging'] = $this->pagination->create_links();
            
            

            $this->data['body'] = $this->parser->parse('_index.html', $this->data, true);

            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function admin()
    {
        if ($this->_is_allowed(array(role_admin))) {
            $this->data['content'] = $this->parser->parse('billing.html', $this->data, true);
            $this->data['body']    = $this->parser->parse('dashboard/_content_wrapper.html', $this->data, true);

            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
}

/* End of file Billing.php */
/* Location: ./application/controllers/Billing.php */
