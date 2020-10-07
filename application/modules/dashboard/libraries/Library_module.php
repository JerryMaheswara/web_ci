<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Library_module
{
    protected $ci;
    /*****************************************************************************/

    public function __construct()
    {
        $this->ci = &get_instance();

        $this->ci->data['module_left'] = array();
        $this->ci->data['modules']     = array();

        $this->ci->_db_ = isset($_SESSION['_db_']) ? $_SESSION['_db_'] : $this->ci->db->database;

        if (isset($_SESSION['user_info'])) {
            $this->ci->data['table_schema_before'] = $this->ci->_db_;

            $this->ci->db->where('t2.module_parent', 0);
            $this->ci->data['modules'] = $this->_my_module($this->ci->_db_);

            $this->ci->db->where('t2.module_parent', 0);
            $this->ci->data['module_left'] = $this->_my_module($this->ci->_db_);
        } else {

            $this->ci->db->where('module_parent', 0);
            $this->ci->data['modules'] = $this->_my_module($this->ci->_db_);

            $this->ci->db->where('module_parent', 0);
            $this->ci->data['module_left'] = $this->_my_module($this->ci->_db_);
        }

        $this->ci->data['_module_left_'] = $this->ci->parser->parse('module_left.html', $this->ci->data, true);

    }

    /*****************************************************************************/

    public function _my_module($_db_ = null)
    {
        if (isset($_SESSION['user_info'])) {
            $my_role = $_SESSION['user_info'][0]->user_role;
            $my_role = implode(',', $my_role);
            // opn($my_role);exit();
            $param['table'] = $_db_ ? $_db_ . '.module_permission t1' : 'module_permission t1';
            $this->ci->db->where('role_label_id IN (' . $my_role . ')');
            $this->ci->db->join($_db_ ? $_db_ . '.module t2' : 'module t2', 't2.module_id = t1.module_id', 'left');
            $this->ci->db->order_by('t2.module_rank', 'asc');
            // $this->ci->db->order_by('t2.module_type', 'asc');
            // $this->ci->db->order_by('t2.module_name', 'asc');
            $this->ci->db->group_by('t2.module_id');
            $module_permission = $this->ci->model_generic->_get($param);

            foreach ($module_permission as $key => $value) {
                $param_has['table'] = $_db_ ? $_db_ . '.module' : 'module';
                $this->ci->db->where('module_parent', $value->module_id);
                $has_children        = $this->ci->model_generic->_count($param_has);
                $value->has_children = $has_children;
                if ($has_children) {
                    $value->{'mc_' . $value->module_level} = $this->_module_children($_db_, $value->module_id, $value->module_level);
                }

                $value->module_url = !empty($value->module_url) ? $value->module_url : 'dashboard/module/' . $value->module_id;

                $param_role['table'] = $_db_ ? $_db_ . '.module_permission' : 'module_permission';
                $this->ci->db->where('module_id', $value->module_id);
                $role       = $this->ci->model_generic->_get($param_role);
                $role_label = array();
                foreach ($role as $mp_key => $mp_value) {
                    $role_label[] = $mp_value->role_label_id;
                }
                $value->module_permission = json_encode($role_label);
                $value->mp                = implode(',', $role_label);

                $entity_count = 0;
                $tbl_exists   = $this->ci->db->table_exists($value->module_table, 1);
                if ($tbl_exists) {
                    $param_cek['table'] = $value->module_table;
                    $entity_count       = $this->ci->model_generic->_count($param_cek);
                }
                $value->entity_count       = ($has_children > 0) ? $has_children : $entity_count;
                $value->module_color       = $value->module_color ?: stc($value->module_name);
                $value->is_has_children    = ($has_children > 0) ? '' : 'hidden destroy';
                $value->is_has_no_children = ($has_children > 0) ? 'hidden destroy' : '';
                $value->module_url         = ($has_children > 0) ? 'dashboard/module/' . $value->module_id : $value->module_url;
                $value->toggle_btn         = ($value->module_disabled) ? 'default' : 'success';
                $value->toggle_icon        = ($value->module_disabled) ? 'toggle-off' : 'toggle-on';

            }
            // opn($module_permission);exit();
            return $module_permission;
        } else {
            $param_module['table'] = 'module';
            $this->ci->db->order_by('module_rank', 'asc');
            $this->ci->db->where('module_disabled', 0);
            $module = $this->ci->model_generic->_get($param_module);
            foreach ($module as $key => $value) {
                $param_has['table'] = $_db_ ? $_db_ . '.module' : 'module';
                $this->ci->db->where('module_parent', $value->module_id);
                $has_children        = $this->ci->model_generic->_count($param_has);
                $value->has_children = $has_children;
                if ($has_children) {
                    $value->{'mc_' . $value->module_level} = $this->_module_children($_db_, $value->module_id, $value->module_level);
                }
                $value->module_url = !empty($value->module_url) ? $value->module_url : 'dashboard/module/' . $value->module_id;

                $entity_count = 0;
                $tbl_exists   = $this->ci->db->table_exists($value->module_table, 1);
                if ($tbl_exists) {
                    $param_cek['table'] = $value->module_table;
                    $entity_count       = $this->ci->model_generic->_count($param_cek);
                }
                $value->entity_count       = ($has_children > 0) ? $has_children : $entity_count;
                $value->module_color       = $value->module_color ?: stc($value->module_name);
                $value->is_has_children    = ($has_children > 0) ? '' : 'hidden destroy';
                $value->is_has_no_children = ($has_children > 0) ? 'hidden destroy' : '';
                // $value->module_url         = ($has_children > 0) ? 'dashboard/module/' . $value->module_id : $value->module_url;
            }

            return $module;
        }

    }
    /*****************************************************************************/
    public function _module_children($_db_, $module_id, $module_level)
    {
        $this->ci->db->where('module_parent', $module_id);
        $param_module_parent['table'] = $_db_ ? $_db_ . '.module' : 'module';
        $this->ci->db->order_by('module.module_rank', 'asc');
        if (!$this->ci->_is_allowed(array(role_admin))) {
            $this->ci->db->where('module_disabled', 0);
        }
        $module_children = $this->ci->model_generic->_get($param_module_parent);
        $branch          = array();
        foreach ($module_children as $key => $value) {
            $param_has['table'] = $_db_ ? $_db_ . '.module' : 'module';
            $this->ci->db->where('module_parent', $value->module_id);
            $has_children              = $this->ci->model_generic->_count($param_has);
            $value->module_url         = !empty($value->module_url) ? $value->module_url : 'dashboard/module/' . $value->module_id;
            $value->has_children       = $has_children;
            $value->is_has_children    = ($has_children > 0) ? '' : 'hidden destroy';
            $value->is_has_no_children = ($has_children > 0) ? 'hidden destroy' : '';
            if ($has_children) {
                $value->{$value->module_level} = $this->_module_children($_db_, $value->module_id, $value->module_level);
            }
            foreach ($value as $mc_key => $mc_value) {
                $mc['mc_' . $module_level . '_' . $mc_key] = $mc_value;
            }

            $branch[$key] = $mc;
        }
        // $value->{'mc_'.$module_level} = $mc;
        return $branch;
        // $tbl_exists          = $this->ci->db->table_exists($value->module_table, 1);

    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function _module_parent($_db_, $module_parent_id, $module_level, $module_level_first)
    {

        $this->ci->db->where('module_id > 0');
        $this->ci->db->where('module_id', $module_parent_id);
        $param_module_parent['table'] = $_db_ ? $_db_ . '.module' : 'module';
        $this->ci->db->order_by('module.module_rank', 'asc');
        if (!$this->ci->_is_allowed(array(role_admin))) {
            $this->ci->db->where('module_disabled', 0);
        }
        $module_parent = $this->ci->model_generic->_get($param_module_parent);
        // opn($module_parent);exit();
        $branch = array();
        foreach ($module_parent as $key => $value) {

            $param_has['table'] = $_db_ ? $_db_ . '.module' : 'module';
            $this->ci->db->where('module_id > 0 ');
            $this->ci->db->where('module_id', $value->module_parent);
            $has_parent = $this->ci->model_generic->_count($param_has);
            // $value->module_url = !empty($value->module_url) ? $value->module_url : 'dashboard/module/' . $value->module_id;
            $value->has_parent       = $has_parent;
            $value->is_has_parent    = ($has_parent > 0) ? '' : 'hidden destroy';
            $value->is_has_no_parent = ($has_parent > 0) ? 'hidden destroy' : '';
            $value->module_url       = !empty($value->module_url) ? $value->module_url : 'dashboard/module/' . $value->module_id;

            // $up                      = $module_level - ($module_level - 1);
            // $up = $value->module_level - ($value->module_level - 1);
            // $up = $module_level - ($module_level - 1) ;
            // $value->{$up + 1} = array();
            $mpss               = $module_level_first - $value->module_level;
            $value->{$mpss + 1} = array();
            if ($has_parent) {
                $value->{$mpss + 1} = $this->_module_parent($_db_, $value->module_parent, $value->module_level, $module_level_first);
            }
            // $xx = ($module_level > $value->module_level) ? $value->module_level - 1 : 1;
            // $up = $value->module_level;
            // opn($up);

            foreach ($value as $mp_key => $mp_value) {
                $mp['mp_' . ($mpss) . '_' . $mp_key] = $mp_value;
            }
            $this->ci->breadcrumb[$mp_key]['breadcrumb_name'] = $value->module_name;
            $this->ci->breadcrumb[$mp_key]['breadcrumb_url']  = $value->module_url;
            // $branch[$key] = $mp;

        }
        return $branch;

    }
    /*****************************************************************************/
    public function _module_breadcrumb($module_id = null)
    {

        if ($module_id) {
            $param_module['table'] = 'module';
            $this->ci->db->where('module_id', $module_id);
            $module_detail = $this->ci->model_generic->_get($param_module);
            // opn($module_detail);exit();
            // $treeview_menu = '';
            $breadcrumb = array();

            // $this->ci->breadcrumb = array();
            foreach ($module_detail as $key => $value) {

                $param_has['table'] = $this->ci->_db_ ? $this->ci->_db_ . '.module' : 'module';
                $this->ci->db->where('module_id', $value->module_parent);
                $has_parent        = $this->ci->model_generic->_count($param_has);
                $value->has_parent = $has_parent;
                $module_level      = $value->module_level - ($value->module_level - 1);
                // $value->{'mp_0_' . $module_level} = array();
                $first = 1;
                if ($has_parent) {
                    $this->_module_parent($this->ci->_db_, $value->module_parent, $value->module_level, $value->module_level);
                }
                $value->module_url = !empty($value->module_url) ? $value->module_url : 'dashboard/module/' . $value->module_id;

                $this->ci->breadcrumb[$key]['breadcrumb_name'] = $value->module_name;
                $this->ci->breadcrumb[$key]['breadcrumb_url']  = $value->module_url . '#';

                $treeview_menu = $value->module_slug;
                // $this->ci->db->where('module_id', $value->module_parent);
                // $module_parent = $this->ci->model_generic->_get($param_module);
                // $value->mp = $module_parent;
                // $value->mp = $this->library_module->_module_parent($this->_db_, $value->module_parent);
            }
            $breadcrumb = ($this->ci->breadcrumb);
            // opn($breadcrumb);exit();
            // opn($breadcrumb);exit();
            // opn($module_detail);exit();
            $this->data['treeview_menu'] = $treeview_menu;

            $this->ci->data['breadcrumb']    = $breadcrumb;
            $this->ci->data['module_detail'] = $module_detail;

            // $this->data['aside_left'] = $this->parser->parse('_aside_left.html', $this->data, true);

            // $this->ci->db->where('t2.module_parent', $module_id);
            // $this->data['module_children'] = $this->library_module->_my_module($this->_db_);

            return $this->ci->parser->parse('module_breadcrumb.html', $this->ci->data, true);
        } else {
            return $this->ci->parser->parse('module_dashboard.html', $this->ci->data, true);

        }
    }
    /*****************************************************************************/
    public function _module_id($module_slug)
    {
        $param_module['table'] = 'module';
        $this->ci->db->where('module_slug', $module_slug);
        $_cek_module = $this->ci->model_generic->_cek($param_module);
        if ($_cek_module) {
            $module_id = '';
            $this->ci->db->where('module_slug', $module_slug);
            $module = $this->ci->model_generic->_get($param_module);
            foreach ($module as $key => $value) {
                $module_id = $value->module_id;
            }
            return $module_id;
        }

        # code...
    }
    /*****************************************************************************/

}

/* End of file Library_module.php */
/* Location: ./application/modules/dashboard/libraries/Library_module.php */
