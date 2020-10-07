<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
        $this->data['content_header'] = __CLASS__;
        $this->data['treeview_menu']  = controller;

        // $this->data['is_mobile'] = $this->agent->is_mobile?:'';
        // opn($this->agent);exit();
        $this->data['is_desktop'] = $this->agent->is_mobile ? 'mobile hidden destroy ' : 'desktop';

        $this->load->library('library_module');
        $this->data['breadcrumb'] = $this->library_module->_module_breadcrumb();

        $this->data['aside_left']  = $this->parser->parse('_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('_footer.html', $this->data, true);
        // $this->data['body']        = $this->parser->parse('_content_wrapper.html', $this->data, true);
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function module()
    {
        $this->_set_referer();
        if ($this->_is_login) {
            $this->data['treeview_menu']      = method;
            $this->data['content_sub_header'] = ucfirst(method);
            $arg                              = func_get_args();
            if (isset($arg[0])) {
                $module_id = $arg[0];
                // $this->db->where('module_parent', $module_id);
                // $module_children               = $this->_my_module();
                // $this->data['module_children'] = $module_children;

                $param_module['table'] = 'module';
                $this->db->where('module_id', $module_id);
                $module_detail = $this->model_generic->_get($param_module);
                // $treeview_menu = '';
                foreach ($module_detail as $key => $value) {

                    $param_has['table'] = $this->_db_ ? $this->_db_ . '.module' : 'module';
                    $this->db->where('module_id', $value->module_parent);
                    $has_parent                       = $this->model_generic->_count($param_has);
                    $value->has_parent                = $has_parent;
                    $module_level                     = $value->module_level - ($value->module_level - 1);
                    $value->{'mp_0_' . $module_level} = array();
                    if ($has_parent) {
                        $value->{'mp_0_' . $module_level} = $this->library_module->_module_parent($this->_db_, $value->module_parent, $value->module_level, $value->module_level);
                    }
                    $treeview_menu = $value->module_slug;
                    // $this->db->where('module_id', $value->module_parent);
                    // $module_parent = $this->model_generic->_get($param_module);
                    // $value->mp = $module_parent;
                    // $value->mp = $this->library_module->_module_parent($this->_db_, $value->module_parent);
                }

                // opn($module_detail);exit();
                $this->data['treeview_menu'] = $treeview_menu;
                $this->data['module_detail'] = $module_detail;
                // $this->data['aside_left']    = $this->parser->parse('_aside_left.html', $this->data, true);

                $this->db->where('module_parent', $module_id);
                $this->data['module_children'] = $this->library_module->_my_module($this->_db_);

                $this->data['breadcrumb'] = $this->library_module->_module_breadcrumb($module_id);
                $this->data['body']       = $this->parser->parse('module_level.html', $this->data, true);
            } else {

                // $modules = $this->_my_module();
                // opn($modules);exit();
                // $this->data['modules'] = $modules;

                if ($this->_is_allowed(array(role_admin))) {

                    $param['table'] = 'information_schema.tables';
                    $this->db->where('TABLE_NAME', 'module_permission');
                    $this->db->select('table_schema');
                    $information_schema               = $this->model_generic->_get($param);
                    $this->data['information_schema'] = $information_schema;
                    // opn($information_schema);exit();

                    // $this->db->where('t2.module_parent', $module_id);

                    // $this->data['body'] = $this->parser->parse('module_level.html', $this->data, true);
                    $this->data['body'] = $this->parser->parse('module_admin.html', $this->data, true);
                } else {
                    $this->data['body'] = '<center>
                <h1>Ops...</h1>
                <i class="fa fa-fw fa-exclamation-triangle fa-4x text-yellow"></i>
                <h4>
                Authentication required...!</h4><hr/>
                <a class="btn btn-success" href="' . base . '/login"><i class="fa fa-fw fa-lock"></i> Login</a></center>';
                }
            }
            $this->parser->parse('_index.html', $this->data, false);
        } else {
            redirect(base . '/customer/login');
        }
    }
    /*****************************************************************************/
    public function list_view()
    {
        redirect(base . '/dashboard');
        # code...
    }
    /*****************************************************************************/
    public function admin()
    {
        redirect(base . '/dashboard/module');
        # code...
    }
    /*****************************************************************************/
    public function index()
    {
        // opn($_SESSION);exit();
        $this->data['content_sub_header'] = __CLASS__;
        $this->data['treeview_menu']      = controller;
        if ($this->_is_allowed(array(role_admin, role_seller, role_member))) {
            $this->data['body'] = $this->parser->parse('dashboard.html', $this->data, true);
            $this->parser->parse('_index.html', $this->data, false);
        } else {
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    public function aside_left_ajax()
    {
        // $this->data['module_left'] = $this->library_module->_my_module($this->_db_);
        if ($this->input->is_ajax_request()) {
            echo $this->parser->parse('module_left.html', $this->data, true);
        } else {
            return $this->parser->parse('module_left.html', $this->data, true);
        }
    }
    /*****************************************************************************/
    public function module_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        if ($this->_is_allowed(array(role_admin))) {

            // $this->db->where('t2.module_parent', 0);
            $this->data['modules'] = $this->library_module->_my_module($this->_db_);
            $this->parser->parse('module_tbody.html', $this->data, false);
        }
        // }
    }
    /*****************************************************************************/
    public function module_children_ajax()
    {
        if ($this->_is_allowed(array(role_admin)) && $this->input->is_ajax_request()) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                // $param['table'] = 'module';
                // // $param['module_id'] = $arg[0];
                // $this->db->where('module_parent', $arg[0]);
                // $module_all = $this->model_generic->_get($param);
                $module_all = $this->_modules($arg[0]);
                // opn($module_all);exit();
                $module_all = json_encode($module_all);
                echo $module_all;
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function module_level_ajax()
    {
        if ($this->_is_allowed(array(role_admin)) && $this->input->is_ajax_request()) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $module_level   = $arg[0];
                $param['table'] = $this->_db_ ? $this->_db_ . '.module' : 'module';
                $this->db->select('module_id as id, module_name as text');
                $this->db->where('module_level', $module_level);
                $module_level = $this->model_generic->_get($param);
                $module_level = json_encode($module_level);
                echo $module_level;
            }
        }
    }
    /*****************************************************************************/
    public function module_delete($module_id)
    {
        if ($this->input->is_ajax_request()) {
            if ($this->_is_allowed(array(role_admin))) {
                if (isset($module_id)) {
                    $param['table'] = $this->_db_ ? $this->_db_ . '.module' : 'module';
                    $this->db->where('module_id', $module_id);
                    $this->model_generic->_del($param);
                }
            }
        }
    }
    /*****************************************************************************/
    public function module_post()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->_is_allowed(array(role_admin))) {
                if ($this->input->post()) {
                    $param             = $this->input->post();
                    $module_permission = isset($param['module_permission']) ? $param['module_permission'] : array();
                    unset($param['module_permission']);
                    $param['module_parent'] = $param['module_level'] == 1 ? 0 : $param['module_parent'];
                    $param['module_slug']   = slug($param['module_name'], '_');

                    $param_cek['table'] = $this->_db_ . '.module';
                    $this->db->or_where('module_id', $param['module_id']);
                    $this->db->or_where('module_name', $param['module_name']);
                    $_cek = $this->model_generic->_cek($param_cek);
                    if (!$_cek) {
                        $param['table'] = $this->_db_ . '.module';
                        $this->model_generic->_insert($param);
                        $module_id = $this->db->insert_id();
                    } else {
                        $param_cek_lagi['table'] = $this->_db_ . '.module';
                        $this->db->where('module_id', $param['module_id']);
                        $_cek_lagi = $this->model_generic->_cek($param_cek_lagi);
                        // opn($_cek_lagi);exit();
                        if ($_cek_lagi) {
                            $param['table'] = $this->_db_ . '.module';
                            $this->db->where('module_id', $param['module_id']);
                            $this->model_generic->_update($param);
                            $module_id = $param['module_id'];
                        } else {
                            exit('Aye kidding me...?');
                        }
                    }

                    $param_module_permission['table'] = $this->_db_ . '.module_permission';
                    $this->db->where('module_id', $module_id);
                    $this->model_generic->_del($param_module_permission);

                    $param_module_permission['module_id']            = $module_id;
                    $param_module_permission['module_permission_id'] = false;
                    if ($module_permission != false) {
                        foreach ($module_permission as $key => $value) {
                            if ($value != 2) {
                                $param_module_permission['role_label_id'] = $value;
                                $this->model_generic->_insert($param_module_permission);
                            }
                        }
                        $param_module_permission['role_label_id'] = 2;
                        $this->model_generic->_insert($param_module_permission);
                    } else {
                        $param_module_permission['role_label_id'] = 2;
                        $this->model_generic->_insert($param_module_permission);
                    }
                    // opn($param_module_permission);exit();

                }

            }
        }
    }
    /*****************************************************************************/
    public function module_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'module';
            $this->db->where('module_id', $param['module_id']);
            $this->db->where('module_disabled != ' . $param['module_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('module_id', $param['module_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['module_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['module_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['module_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['module_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

                echo json_encode($return);
            }
            // opn($_cek_module);exit();

        }
    }
    /*****************************************************************************/
    public function role_ajax()
    {
        $param['table'] = $this->_db_ . '.role_label';
        $this->db->select('role_label_id as id, role_label_name as text');
        $role_label = $this->model_generic->_get($param);
        $role_label = json_encode($role_label, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $role_label;

    }
    /*****************************************************************************/
    public function change_table_schema()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('table_schema')) {
                $_SESSION['_db_'] = $this->input->post('table_schema');
                echo $this->input->post('table_schema');
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */
