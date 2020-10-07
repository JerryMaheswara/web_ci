<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Brand extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();


        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        $this->load->library('library_module');


        $this->data['role_label_id'] = '';
        $this->data['search']        = '';
        $this->data['limit']         = $this->_limit;
        $this->data['offset']        = $this->_offset;

        $_module_id               = $this->library_module->_module_id(strtolower(controller));
        $this->data['breadcrumb'] = $this->library_module->_module_breadcrumb($_module_id);

        $this->data['today']       = date('Y-m-d');
        $this->data['_is_allowed'] = $this->_is_allowed(array(role_admin, role_seller)) ? '' : 'hidden destroy';
        $this->load->model('model_brand');
        $this->data['status'] = $this->input->get('status');

        $this->data['menu_active_home']     = '';
        $this->data['menu_active_brand'] = 'active';
        $this->data['menu_active_item']     = '';
        
        
        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);

    }
    /*****************************************************************************/
    function index()
    {
        $param_brand['table'] = 'brand';
        $brand_all =  $this->model_generic->_get($param_brand);
        foreach ($brand_all as $value) {
            $value->brand_image = file_exists('./files/images/' . controller . '/' . $value->brand_image . '.png') ? controller . '/' . $value->brand_image . '.png' : 'default-brand.png';
        }
        $this->data['brand_all'] = $brand_all;
        // opn($brand_all);exit();
        $this->data['topbar']         = $this->parser->parse('welcome/_topbar.html', $this->data, true);
        $this->data['menu_desktop']   = $this->parser->parse('welcome/_menu_desktop.html', $this->data, true);
        $this->data['menu_mobile']    = $this->parser->parse('welcome/_menu_mobile.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('welcome/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('welcome/_footer.html', $this->data, true);
        $this->data['body'] = $this->parser->parse('grid.html', $this->data, true);

        $this->parser->parse('welcome/_index.html', $this->data, false);
        
    }
    /*****************************************************************************/
    public function admin()
    {

        $this->_set_referer();
        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            redirect(base . '/login');
        }
        $param_brand['table'] = 'brand';
        $total_rows              = $this->model_generic->_count($param_brand);
        $_cek_brand           = $this->model_generic->_cek($param_brand);
        $config_base_url         = base . '/' . controller;
        $this->data['search']    = '';
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('brand.brand_name', $this->input->get('search'));
        }

        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('brand_disabled', 0);
        }
        $brand_all = $this->model_brand->_get_brand($this->_limit, $this->_offset);
        // opn($brand_all);exit();
        // $brand_all = $this->model_generic->_get($param_brand, $this->_limit, $this->_offset);
        foreach ($brand_all as $key => $value) {
        }
        $this->data['brand_all'] = $brand_all;

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
    public function admin_()
    {
        $this->_set_referer();
        if ($this->_is_allowed(array(role_admin, role_seller))) {
            $param_brand['table'] = 'brand';
            $total_rows              = $this->model_generic->_count($param_brand);
            $_cek_brand           = $this->model_generic->_cek($param_brand);
            $config_base_url         = base . '/' . controller;
            $this->data['search']    = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('brand.brand_name', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_brand);

                $this->db->or_like('brand.brand_name', $this->input->get('search'));

            }
            $brand_all = $this->model_generic->_get($param_brand, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($brand_all);exit();
            $this->data['brand_all'] = $brand_all;
            $this->data['total_rows']   = $total_rows;

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
        $param_brand['table'] = 'brand';
        $total_rows              = $this->model_generic->_count($param_brand);
        $_cek_brand           = $this->model_generic->_cek($param_brand);
        $config_base_url         = base . '/' . controller;
        $this->data['search']    = '';
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('brand.brand_name', $this->input->get('search'));
            $total_rows = $this->model_generic->_count($param_brand);

            $this->db->or_like('brand.brand_name', $this->input->get('search'));

        }
        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('brand_disabled', 0);
        }
        $brand_all = $this->model_generic->_get($param_brand, $this->_limit, $this->_offset);
        // opn(lq());
        // opn($brand_all);exit();
        $this->data['brand_all'] = $brand_all;
        $this->data['total_rows']   = $total_rows;

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
                $param_brand            = $this->input->post();
                $param_brand['table']   = 'brand';
                $param_brand['user_id'] = $this->_user_id;
                $this->model_generic->_insert($param_brand);
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
                $brand_id = $arg[0];
                if ($this->input->post()) {
                    $param_brand = $this->input->post();
                    // opn($param_brand);exit();

                    $brand_image_before = $param_brand['brand_image_before'];
                    unset($param_brand['brand_image_before']);
                    if ($_FILES['brand_image_file']['error'] == 0) {

                        // opn($_FILES['seller_image']);exit();
                        $upload_path = './files/images/' . controller;
                        $file_name   = md5(time() . uniqid());

                        if (!is_dir($upload_path)) {
                            mkdir($upload_path);
                        }
                        chmod($upload_path, 0777);
                        $config['upload_path']   = './files/images/' . controller;
                        $config['allowed_types'] = 'png';
                        // $config['max_size']         = 1024;
                        $config['max_width']        = 256;
                        $config['max_height']       = 256;
                        $config['file_name']        = $file_name;
                        $config['overwrite']        = true;
                        $config['file_ext_tolower'] = true;

                        $this->load->library('upload', $config);

                        if (!$this->upload->do_upload('brand_image_file')) {
                            $error['message']       = strip_tags($this->upload->display_errors());
                            $error['allowed_types'] = $config['allowed_types'];
                            // $error['max_size']      = $config['max_size'] . ' KB ';
                            $error['max_width']  = $config['max_width'] . ' pixels';
                            $error['max_height'] = $config['max_height'] . ' pixels';

                            $this->data['error'] = array($error);
                            $this->data['body']  = $this->parser->parse('_error.html', $this->data, true);
                            $parse               = $this->parser->parse('dashboard/_index.html', $this->data, false);
                            echo ($parse);
                            exit();
                        }
                        if (file_exists('./files/images/' . controller . '/' . $brand_image_before . '.png') && $brand_image_before != 'default') {
                            unlink('./files/images/' . controller . '/' . $brand_image_before . '.png');
                        }
                        $param_brand['brand_image'] = $file_name;

                    }

                    $param_brand['table'] = 'brand';

                    if (!isset($param_brand['brand_parent']) && $param_brand['brand_level'] > 1) {
                        $param_brand['brand_level']  = 1;
                        $param_brand['brand_parent'] = 0;
                    }
                    if ($param_brand['brand_level'] == 1) {
                        $param_brand['brand_parent'] = 0;
                    }

                    /// cek dulu sub categorinya
                    $param_brand_sub['table'] = 'brand';
                    $this->db->where('brand_parent', $brand_id);
                    $sub_brand = $this->model_generic->_get($param_brand_sub);
                    foreach ($sub_brand as $sc_value) {
                        $param_brand_sub['brand_level'] = $param_brand['brand_level'] + 1;
                        $this->db->where('brand_parent', $brand_id);
                        $this->model_generic->_update($param_brand_sub);
                    }
                    // opn($sub_brand);exit();

                    $param_brand['user_id'] = $this->_user_id;
                    $this->db->where('brand_id', $brand_id);
                    $_cek_brand = $this->model_generic->_cek($param_brand);
                    if ($_cek_brand) {
                        $this->db->where('brand_id', $brand_id);
                        $this->model_generic->_update($param_brand);
                        $this->_goto_referer();
                    }
                    // opn($param);exit();
                } else {

                    $param_brand['table'] = 'brand';
                    $this->db->where('brand_id', $brand_id);
                    $brand_detail = $this->model_generic->_get($param_brand);
                    foreach ($brand_detail as $key => $value) {
                        $value->remove_button_display = file_exists('./files/images/' . controller . '/' . $value->brand_image . '.png') ? '' : 'hidden';
                        $value->brand_image        = file_exists('./files/images/' . controller . '/' . $value->brand_image . '.png') ? controller . '/' . $value->brand_image . '.png' : 'default-brand.png';
                    }
                    $this->data['brand_detail'] = $brand_detail;
                    // opn($brand_detail);exit();
                    // $this->load->library('image/library_image');

                    // $param_image['entity_table'] = 'brand';
                    // $param_image['entity_id']    = $brand_id;
                    // $this->data['image_upload']  = $this->library_image->add_image($param_image);

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
            $brand_id             = $arg[0];
            $param_brand['table'] = 'brand';
            $this->db->where('brand_id', $brand_id);
            $brand_detail = $this->model_generic->_get($param_brand);
            $brand_parent = 0;
            foreach ($brand_detail as $key => $value) {

                $brand_parent       = $value->brand_parent;
                $value->brand_image = file_exists('./files/images/' . controller . '/' . $value->brand_image . '.png') ? controller . '/' . $value->brand_image . '.png' : 'default-brand.png';

            }
            // opn($brand_detail);exit();
            $this->data['brand_detail'] = $brand_detail;

            $param_brand_parent['table'] = 'brand';
            $this->db->where('brand_id', $brand_parent);
            $parent_brand               = $this->model_generic->_get($param_brand_parent);
            $this->data['parent_brand'] = $parent_brand;
            // opn($parent_brand);exit();

            $param_brand_sub['table'] = 'brand';
            $this->db->where('brand_parent', $brand_id);
            $sub_brand               = $this->model_generic->_get($param_brand_sub);
            $this->data['sub_brand'] = $sub_brand;
            // opn($sub_brand);exit();

            $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function delete()
    {
        if ($this->_is_allowed(array(role_admin, role_seller))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $brand_id = $arg[0];

                $param_brand['table'] = 'brand';
                $this->db->where('brand_parent', $brand_id);
                $_cek = $this->model_generic->_cek($param_brand);

                if (!$_cek) {
                    $param_brand['table'] = 'brand';
                    $this->db->where('brand_id', $brand_id);
                    $this->model_generic->_del($param_brand);
                    redirect(base . '/' . controller . '/admin');
                } else {
                    echo 'Not allowed';
                    redirect(base . '/' . controller . '/detail/' . $brand_id);

                }
            }
        }
    }
    /*****************************************************************************/
    public function tree()
    {

        $this->data['body'] = $this->parser->parse('_tree.html', $this->data, true);
        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    # kebawah in ajax semua
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    public function brand_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_brand['table'] = 'brand';
        $total_rows              = $this->model_generic->_count($param_brand);
        $config_base_url         = base . '/' . controller . '/admin';

        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('brand_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
            $result[0]['search'] = $this->input->get('search');
            if (!$this->_is_allowed(array(role_admin, role_seller))) {
                $this->db->where('brand_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('brand_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('brand.brand_name', $this->input->get('search'));
            $this->db->group_end();

            $total_rows = $this->model_generic->_count($param_brand);
            if (!$this->_is_allowed(array(role_admin, role_seller))) {
                $this->db->where('brand_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('brand_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('brand.brand_name', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {
            $this->db->where('brand_status', $this->input->get('status'));
            $total_rows = $this->model_generic->_count($param_brand);
            $this->db->where('brand_status', $this->input->get('status'));
        }
        $brand_all = $this->model_brand->_get_brand($this->_limit, $this->_offset);
        foreach ($brand_all as $key => $value) {
            $value->toggle_btn  = ($value->brand_disabled) ? 'default' : 'success';
            $value->toggle_icon = ($value->brand_disabled) ? 'toggle-off' : 'toggle-on';
            // $value->brand_image = file_exists('./files/images/'.controller.'/')

            $value->brand_image = file_exists('./files/images/' . controller . '/' . $value->brand_image . '.png') ? controller . '/' . $value->brand_image . '.png' : 'default-brand.png';

            // $param_image['table'] = 'image';

            // $this->db->where('entity_table', 'brand');
            // $this->db->where('entity_id', $value->brand_id);
            // $image = $this->model_generic->_get($param_image);
            // if ($image) {
            //     $value->brand_image = $image[0]->image_path . '/thumbnail/' . $image[0]->image_filename . '_128.' . $image[0]->image_ext;
            // } else {
            //     $value->brand_image = 'default-brand.png';
            // }

        }
        $this->data['brand_all'] = $brand_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($brand_all);
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
    /*****************************************************************************/
    public function brand_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'brand';
            $this->db->where('brand_id', $param['brand_id']);
            $this->db->where('brand_disabled != ' . $param['brand_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('brand_id', $param['brand_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['brand_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['brand_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['brand_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['brand_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

                echo json_encode($return);
            }
            // opn($_cek_module);exit();

        }
    }
    /*****************************************************************************/

    /*****************************************************************************/
    public function remove_image()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $brand_id             = $arg[0];
            $param_brand['table'] = 'brand';
            $this->db->where('brand_id', $brand_id);
            $brand = $this->model_generic->_get($param_brand);
            foreach ($brand as $value) {
                if (file_exists('./files/images/' . controller . '/' . $value->brand_image . '.png')) {
                    unlink('./files/images/' . controller . '/' . $value->brand_image . '.png');
                }
                $res['status']  = 1;
                $res['message'] = 'Avatar deleted.';
                echo json_encode($res);

            }
        }

    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function parent()
    {
        // if ($this->input->is_ajax_request()) {
        if ($this->input->post()) {
            $param_brand['table'] = 'brand';
            $brand_level          = $this->input->post('brand_level');
            $brand_id             = $this->input->post('brand_id');

            $param_brand_sub['table'] = 'brand';
            $this->db->where('brand_parent', $brand_id);
            $sub_brand = $this->model_generic->_get($param_brand_sub);
            foreach ($sub_brand as $value) {
                $this->db->where('brand_id != ', $value->brand_id);
            }

            $this->db->where('brand_level', $brand_level);
            $this->db->where('brand_id != ', $brand_id);
            $this->db->select('brand_id as id');
            $this->db->select('brand_name as text');
            $brand               = $this->model_generic->_get($param_brand, $this->_limit, $this->_offset);
            $this->data['brand'] = $brand;
            $brand               = json_encode($brand, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $brand;
        }

        // }
    }
    /*****************************************************************************/
}

/* End of file Brand.php */
/* Location: ./application/controllers/Brand.php */
