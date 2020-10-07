<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Catalogue extends MY_Controller
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
        $this->data['_is_allowed'] = $this->_is_allowed(array(role_admin, role_seller)) ? '' : 'hidden destroy';
        $this->load->model('model_catalogue');
        $this->data['status'] = $this->input->get('status');

    }
    /*****************************************************************************/
    public function index()
    {

        $this->_set_referer();
        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            redirect(base . '/login');
        }
        $param_catalogue['table']  = 'catalogue';
        $total_rows           = $this->model_generic->_count($param_catalogue);
        $_cek_catalogue            = $this->model_generic->_cek($param_catalogue);
        $config_base_url      = base . '/' . controller;
        $this->data['search'] = '';
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('catalogue.catalogue_name', $this->input->get('search'));
        }

        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('catalogue_disabled', 0);
        }
        $catalogue_all = $this->model_catalogue->_get_catalogue($this->_limit, $this->_offset);
        // opn($catalogue_all);exit();
        // $catalogue_all = $this->model_generic->_get($param_catalogue, $this->_limit, $this->_offset);
        foreach ($catalogue_all as $key => $value) {
        }
        $this->data['catalogue_all'] = $catalogue_all;

        $this->load->library('pagination');
        $config['base_url']             = $config_base_url;
        $config['total_rows']           = $total_rows;
        $config['query_string_segment'] = 'offset';
        $config['per_page']             = $this->_limit;
        $this->pagination->initialize($config);
        $this->data['paging']     = $this->pagination->create_links();
        $this->data['total_rows'] = $total_rows;

        $this->data['searchbar'] = $this->parser->parse('_searchbar.html', $this->data, true);

        if ($this->input->get('view')) {
            $view = $this->input->get('view');
            switch ($view) {
                case 'grid':
                    $this->data['body'] = $this->parser->parse('_container_view_grid.html', $this->data, true);
                    break;
                case 'list':
                    $this->data['body'] = $this->parser->parse('_container_view_list.html', $this->data, true);
                    break;
                default:
                    $this->data['body'] = $this->parser->parse('_container_view_grid.html', $this->data, true);
                    break;
            }
        } else {
            $this->data['body'] = $this->parser->parse('_container_view_grid.html', $this->data, true);
        }

        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function admin()
    {
        $this->_set_referer();
        if ($this->_is_allowed(array(role_admin, role_seller))) {
            $param_catalogue['table']  = 'catalogue';
            $total_rows           = $this->model_generic->_count($param_catalogue);
            $_cek_catalogue            = $this->model_generic->_cek($param_catalogue);
            $config_base_url      = base . '/' . controller;
            $this->data['search'] = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('catalogue.catalogue_name', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_catalogue);

                $this->db->or_like('catalogue.catalogue_name', $this->input->get('search'));

            }
            $catalogue_all = $this->model_generic->_get($param_catalogue, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($catalogue_all);exit();
            $this->data['catalogue_all']   = $catalogue_all;
            $this->data['total_rows'] = $total_rows;

            $this->load->library('pagination');
            $config['base_url']             = $config_base_url;
            $config['total_rows']           = $total_rows;
            $config['query_string_segment'] = 'offset';
            $config['per_page']             = $this->_limit;
            $this->pagination->initialize($config);
            $this->data['paging']    = $this->pagination->create_links();
            $this->data['searchbar'] = $this->parser->parse('_searchbar.html', $this->data, true);

            $this->data['body'] = $this->parser->parse('_container_view_list.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        } else {
            if ($this->_is_login) {
                redirect(base . '/' . controller);
            } else {
                redirect(base . '/login');
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function listview()
    {
        $this->_set_referer();
        $param_catalogue['table']  = 'catalogue';
        $total_rows           = $this->model_generic->_count($param_catalogue);
        $_cek_catalogue            = $this->model_generic->_cek($param_catalogue);
        $config_base_url      = base . '/' . controller;
        $this->data['search'] = '';
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('catalogue.catalogue_name', $this->input->get('search'));
            $total_rows = $this->model_generic->_count($param_catalogue);

            $this->db->or_like('catalogue.catalogue_name', $this->input->get('search'));

        }
        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('catalogue_disabled', 0);
        }
        $catalogue_all = $this->model_generic->_get($param_catalogue, $this->_limit, $this->_offset);
        // opn(lq());
        // opn($catalogue_all);exit();
        $this->data['catalogue_all']   = $catalogue_all;
        $this->data['total_rows'] = $total_rows;

        $this->load->library('pagination');
        $config['base_url']             = $config_base_url;
        $config['total_rows']           = $total_rows;
        $config['query_string_segment'] = 'offset';
        $config['per_page']             = $this->_limit;
        $this->pagination->initialize($config);
        $this->data['paging'] = $this->pagination->create_links();

        $this->data['offset']    = $this->_offset;
        $this->data['searchbar'] = $this->parser->parse('_searchbar.html', $this->data, true);

        $this->data['body'] = $this->parser->parse('_container_view_list.html', $this->data, true);
        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function add()
    {
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin, role_seller))) {
            if ($this->input->post()) {
                $param_catalogue            = $this->input->post();
                $param_catalogue['table']   = 'catalogue';
                $param_catalogue['user_id'] = $this->_user_id;
                $this->model_generic->_insert($param_catalogue);
                $this->_goto_referer();
                // opn($param);exit();
            } else {

                $this->data['body'] = $this->parser->parse('_add.html', $this->data, true);
                $this->parser->parse('dashboard/_index.html', $this->data, false);
            }
        } else {
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    public function edit()
    {
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin, role_seller))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $catalogue_id = $arg[0];
                if ($this->input->post()) {
                    $param_catalogue            = $this->input->post();
                    $param_catalogue['table']   = 'catalogue';
                    $param_catalogue['user_id'] = $this->_user_id;
                    $this->db->where('catalogue_id', $catalogue_id);
                    $_cek_catalogue = $this->model_generic->_cek($param_catalogue);
                    if ($_cek_catalogue) {
                        $this->db->where('catalogue_id', $catalogue_id);
                        $this->model_generic->_update($param_catalogue);
                        $this->_goto_referer();
                    }
                    // opn($param);exit();
                } else {

                    $param_catalogue['table'] = 'catalogue';
                    $this->db->where('catalogue_id', $catalogue_id);
                    $catalogue_detail = $this->model_generic->_get($param_catalogue);
                    foreach ($catalogue_detail as $key => $value) {
                    }
                    $this->data['catalogue_detail'] = $catalogue_detail;

                    $this->load->library('image/library_image');
                    $param_image['entity_table'] = 'catalogue';
                    $param_image['entity_id']    = $catalogue_id;
                    $this->data['image_upload']  = $this->library_image->add_image($param_image);

                    $this->data['body'] = $this->parser->parse('_edit.html', $this->data, true);
                    $this->parser->parse('dashboard/_index.html', $this->data, false);
                }

            }
        } else {
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    public function detail()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $catalogue_id             = $arg[0];
            $param_catalogue['table'] = 'catalogue';
            $this->db->where('catalogue_id', $catalogue_id);
            $catalogue_detail = $this->model_generic->_get($param_catalogue);
            foreach ($catalogue_detail as $key => $value) {
            }
            $this->data['catalogue_detail'] = $catalogue_detail;

            $this->load->library('image/library_image');
            $param_image['entity_table'] = 'catalogue';
            $param_image['entity_id']    = $catalogue_id;
            $this->data['catalogue_images']   = $this->library_image->refresh($param_image);
            // $this->data['catalogue_images'] = $this->data['image_of'];
            // opn($this->data['catalogue_images']);exit();

            $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function delete()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $catalogue_id = $arg[0];
            if ($this->_is_allowed(array(role_admin, role_seller))) {
                $param_catalogue['table'] = 'catalogue';
                $this->db->where('catalogue_id', $catalogue_id);
                $this->model_generic->_del($param_catalogue);

                redirect(base . '/' . controller . '/admin');
            }
        }
    }
    /*****************************************************************************/
    public function catalogue_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_catalogue['table'] = 'catalogue';
        $total_rows          = $this->model_generic->_count($param_catalogue);
        $config_base_url     = base . '/' . controller . '/admin';

        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('catalogue_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
            $result[0]['search'] = $this->input->get('search');
            if (!$this->_is_allowed(array(role_admin, role_seller))) {
                $this->db->where('catalogue_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('catalogue_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('catalogue.catalogue_name', $this->input->get('search'));
            $this->db->group_end();

            $total_rows = $this->model_generic->_count($param_catalogue);
            if (!$this->_is_allowed(array(role_admin, role_seller))) {
                $this->db->where('catalogue_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('catalogue_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('catalogue.catalogue_name', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {
            $this->db->where('catalogue_status', $this->input->get('status'));
            $total_rows = $this->model_generic->_count($param_catalogue);
            $this->db->where('catalogue_status', $this->input->get('status'));
        }
        $catalogue_all = $this->model_catalogue->_get_catalogue($this->_limit, $this->_offset);
        foreach ($catalogue_all as $key => $value) {
            $value->toggle_btn  = ($value->catalogue_disabled) ? 'default' : 'success';
            $value->toggle_icon = ($value->catalogue_disabled) ? 'toggle-off' : 'toggle-on';
        }
        $this->data['catalogue_all'] = $catalogue_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($catalogue_all);
        // exit();

        $this->load->library('pagination');
        $config['base_url']             = $config_base_url;
        $config['total_rows']           = $total_rows;
        $config['query_string_segment'] = 'offset';
        $config['per_page']             = $this->_limit;
        $this->pagination->initialize($config);

        $result[0]['paging']     = $this->pagination->create_links();
        $result[0]['_tbody']     = $this->parser->parse('_data_view_list.html', $this->data, true);
        $result[0]['_gridview']  = $this->parser->parse('_data_view_grid.html', $this->data, true);
        $result[0]['total_rows'] = $total_rows;

        header('Content-Type: application/json');
        echo json_encode($result);

        // }
    }
    /*****************************************************************************/
    public function catalogue_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'catalogue';
            $this->db->where('catalogue_id', $param['catalogue_id']);
            $this->db->where('catalogue_disabled != ' . $param['catalogue_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('catalogue_id', $param['catalogue_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['catalogue_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['catalogue_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['catalogue_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['catalogue_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

                echo json_encode($return);
            }
            // opn($_cek_module);exit();

        }
    }
    /*****************************************************************************/

    /*****************************************************************************/
}

/* End of file Catalogue.php */
/* Location: ./application/controllers/Catalogue.php */
