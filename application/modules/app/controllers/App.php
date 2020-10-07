<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        // opn(session_id());exit();

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
        $this->load->model('model_app');
        $this->data['status'] = $this->input->get('status');

        $this->passwordHasher = new Hautelook\Phpass\PasswordHash(64, false);
        // $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
        // $xx = new PHPExcel();

    }
    /*****************************************************************************/
    public function index()
    {

        $this->_set_referer();
        if (!$this->_is_allowed(array(role_admin, role_user))) {
            redirect(base . '/login');
        } else {
            if ($this->_is_allowed(array(role_user))) {
                redirect(base . '/customer');
            }
        }
        $param_app['table']   = 'app';
        $total_rows           = $this->model_generic->_count($param_app);
        $_cek_app             = $this->model_generic->_cek($param_app);
        $config_base_url      = base . '/' . controller;
        $this->data['search'] = '';

        if ($this->input->get('order') && $this->input->get('sort')) {
            $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        }

        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('app.app_name', $this->input->get('search'));
        }

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('app_disabled', 0);
        }

        $app_all = $this->model_app->_get_app($this->_limit, $this->_offset);
        // opn(lq());
        // opn($app_all);
        // exit();
        // $app_all = $this->model_generic->_get($param_app, $this->_limit, $this->_offset);
        $this->data['app_all'] = $app_all;

        $this->load->library('pagination');
        $config['base_url']             = $config_base_url;
        $config['total_rows']           = $total_rows;
        $config['query_string_segment'] = 'offset';
        $config['per_page']             = $this->_limit;

        $this->pagination->initialize($config);
        $this->data['paging']     = $this->pagination->create_links();
        $this->data['total_rows'] = $total_rows;
        // opn($this->data['paging'] );exit();

        $this->data['searchbar'] = $this->parser->parse('_searchbar.html', $this->data, true);

        $this->data['view'] = 'grid';
        if ($this->input->get('view')) {
            $view               = $this->input->get('view');
            $this->data['view'] = $view;
            // opn($view);exit();
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
        $this->data['view'] = '';
        $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            $param_app['table']   = 'app';
            $total_rows           = $this->model_generic->_count($param_app);
            $_cek_app             = $this->model_generic->_cek($param_app);
            $config_base_url      = base . '/' . controller;
            $this->data['search'] = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('app.app_name', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_app);

                $this->db->or_like('app.app_name', $this->input->get('search'));

            }
            $app_all = $this->model_generic->_get($param_app, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($app_all);exit();
            $this->data['app_all']    = $app_all;
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
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function add()
    {
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->post()) {
                $param_app             = $this->input->post();
                $param_app['table']    = 'app';
                $param_app['app_slug'] = slug($param_app['app_name'], '_');
                $this->model_generic->_insert($param_app);
                redirect(base . '/app');

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
        if ($this->_is_allowed(array(role_admin))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $app_id = $arg[0];
                if ($this->input->post()) {
                    $param_app = $this->input->post();
                    // opn($param_app);exit();

                    $param_app['app_slug'] = slug($param_app['app_name'], '_');

                    $param_app['table'] = 'app';
                    $this->db->where('app_id', $app_id);
                    $_cek_app = $this->model_generic->_cek($param_app);
                    if ($_cek_app) {
                        $this->db->where('app_id', $app_id);
                        $this->model_generic->_update($param_app);

                        redirect(base . '/app/detail/' . $app_id);
                    }
                    // opn($param);exit();
                } else {

                    $param_app['table'] = 'app';
                    $this->db->where('app_id', $app_id);
                    $app_detail = $this->model_generic->_get($param_app);
                    foreach ($app_detail as $key => $value) {
                    }
                    $this->data['app_detail'] = $app_detail;

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
            $app_id             = $arg[0];
            $param_app['table'] = 'app';
            $this->db->where('app_id', $app_id);
            $app_detail = $this->model_generic->_get($param_app);
            foreach ($app_detail as $key => $value) {
            }
            $this->data['app_detail'] = $app_detail;

            $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function delete()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $app_id = $arg[0];
            if ($this->_is_allowed(array(role_admin))) {
                $param_app['table'] = 'app';

                $this->db->where('app_id', $app_id);
                $_cek = $this->model_generic->_cek($param_app);
                if ($_cek) {

                    $this->db->where('app_id', $app_id);
                    $app = $this->model_generic->_get($param_app);
                    foreach ($app as $key => $value) {
                        $param_user['table'] = 'user';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user);

                        $param_user_role['table'] = 'user_role';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user_role);
                    }
                    $this->db->where('app_id', $app_id);
                    $this->model_generic->_del($param_app);
                }

                // redirect(base . '/' . controller);
                $this->_goto_referer();
            }
        }
    }
    /*****************************************************************************/
    public function app_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_app['table'] = 'app';
        $total_rows         = $this->model_generic->_count($param_app);

        if ($this->input->get('view') && !empty($this->input->get('view'))) {
            $config_base_url = base . '/' . controller . '?view=' . $this->input->get('view');
        } else {
            $config_base_url = base . '/' . controller . '/admin';
        }

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('app_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
            $result[0]['search'] = $this->input->get('search');
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('app_disabled', '0');
            }
            $this->db->group_start();
            $this->db->or_like('app.app_name', $this->input->get('search'));
            $this->db->group_end();

            $total_rows = $this->model_generic->_count($param_app);
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('app_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('app_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('app.app_name', $this->input->get('search'));
            $this->db->or_like('app.app_nomor', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {
            $this->db->where('app_status', $this->input->get('status'));
            $total_rows = $this->model_generic->_count($param_app);
            $this->db->where('app_status', $this->input->get('status'));
        }

        if ($this->input->get('order') && $this->input->get('sort')) {
            $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        }

        $app_all = $this->model_app->_get_app($this->_limit, $this->_offset);
        foreach ($app_all as $key => $value) {
            $value->toggle_btn  = ($value->app_disabled) ? 'default' : 'success';
            $value->toggle_icon = ($value->app_disabled) ? 'toggle-off' : 'toggle-on';
        }
        $this->data['app_all'] = $app_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($app_all);
        // exit();

        $this->load->library('pagination');
        $config['base_url']             = $config_base_url;
        $config['total_rows']           = $total_rows;
        $config['query_string_segment'] = 'offset';
        $config['per_page']             = $this->_limit;
        $config['num_links']            = 1;

        $config['first_link'] = '&laquo;';

        $config['last_link'] = '&raquo;';

        $config['next_link'] = '&rarr;';

        $config['prev_link'] = '&larr;';

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
    public function app_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'app';
            $this->db->where('app_id', $param['app_id']);
            $this->db->where('app_disabled != ' . $param['app_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('app_id', $param['app_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['app_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['app_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['app_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['app_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

                echo json_encode($return);
            }
            // opn($_cek_module);exit();

        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function subscribe()
    {
        if ($this->_is_allowed(array(role_admin, role_user))) {
            $this->load->model('organisation/model_organisation');
            if ($this->input->get()) {
                $organisation_id = $this->input->get('organisation_id');
                $app_ids         = json_decode($this->input->get('app_ids'));
                $_is_mine        = $this->model_organisation->_is_mine($organisation_id, $this->_user_id);
                if ($_is_mine || $this->_is_allowed(array(role_admin))) {
                    foreach ($app_ids as $value) {
                        $app_id = $value;
                        $this->db->where('app_id', $app_id);
                        $param_subscribe['table']             = 'subscribe';
                        $param_subscribe['app_id']            = $app_id;
                        $param_subscribe['subscribe_status']  = 1;
                        $param_subscribe['organisation_id']   = $organisation_id;
                        $param_subscribe['subscribe_expired'] = date('Y-m-d h:i:s', strtotime('+ 3 month', time()));

                        $this->db->where('organisation_id', $organisation_id);
                        $this->db->where('app_id', $app_id);
                        $_cek = $this->model_generic->_cek($param_subscribe);
                        if ($_cek) {
                            $this->db->where('organisation_id', $organisation_id);
                            $this->db->where('app_id', $app_id);
                            $this->model_generic->_update($param_subscribe);
                        } else {
                            $this->model_generic->_insert($param_subscribe);
                        }
                    }
                    redirect(base . '/organisation/detail/' . $organisation_id);
                    // opn($organisation_id);exit();
                    // $this->data['body'] = $this->parser->parse('_subscribe.html', $this->data, true);
                    // $this->parser->parse('dashboard/_index.html', $this->data, false);
                }
            }
        }
    }
    /*****************************************************************************/
}

/* End of file App.php */
/* Location: ./application/controllers/App.php */
