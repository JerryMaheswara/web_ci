<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category extends MY_Controller
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
        $this->load->model('model_category');
        $this->data['status'] = $this->input->get('status');
 
        $this->data['menu_active_category'] = 'active'; 

        $this->data['popular_brand']  = $this->parser->parse('popular_brand.html', $this->data, true);
        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);

    }
    /*****************************************************************************/
    public function index()
    {
        $param_category['table'] = 'category';
        $category_all            = $this->model_generic->_get($param_category);
        foreach ($category_all as $value) {
            $value->category_image = file_exists('./files/images/' . controller . '/' . $value->category_image . '.png') ? controller . '/' . $value->category_image . '.png' : 'default-category.png';

            $category_thumbnail_default[0]['category_thumbnail_image'] = 'default-category.png'; 
            $category_thumbnail_default[1]['category_thumbnail_image'] = 'default-category.png'; 

            // $category_thumbnail_default = $mim;
            // opn($category_thumbnail_default);exit();

            $param['table']                 = 'image';
            $this->db->where('entity_table', controller);
            $this->db->where('entity_id', $value->category_id);
            $category_thumbnail = $this->model_generic->_get($param);
            if ( !empty($category_thumbnail) ) {
                foreach ($category_thumbnail as $ct_value) {
                    $ct_value->category_thumbnail_image = file_exists('./files/images/' . controller . '/' . $ct_value->image_filename . '.'.$ct_value->image_ext) ? controller . '/' . $ct_value->image_filename . '.'.$ct_value->image_ext : 'default-category.png';
                }
                $value->category_image_additional = $category_thumbnail;
            }else{
                $value->category_image_additional = $category_thumbnail_default;
            }
            // opn($image_of);exit();
        }
        $this->data['category_all'] = $category_all;
        // opn($category_all);exit();

        $this->data['topbar']       = $this->parser->parse('welcome/_topbar.html', $this->data, true);
        $this->data['menu_desktop'] = $this->parser->parse('welcome/_menu_desktop.html', $this->data, true);
        $this->data['menu_mobile']  = $this->parser->parse('welcome/_menu_mobile.html', $this->data, true);
        // $this->data['customizer']     = $this->parser->parse('welcome/_customizer.html', $this->data, true);

        $this->data['header'] = $this->parser->parse('welcome/_header.html', $this->data, true);
        $this->data['footer'] = $this->parser->parse('welcome/_footer.html', $this->data, true);
        $this->data['body']   = $this->parser->parse('body.html', $this->data, true);

        $this->parser->parse('welcome/_index.html', $this->data, false);

    }
    /*****************************************************************************/
    public function admin()
    {

        $this->_set_referer();
        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            redirect(base . '/login');
        }
        $param_category['table'] = 'category';
        $total_rows              = $this->model_generic->_count($param_category);
        $_cek_category           = $this->model_generic->_cek($param_category);
        $config_base_url         = base . '/' . controller;
        $this->data['search']    = '';
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('category.category_name', $this->input->get('search'));
        }

        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('category_disabled', 0);
        }
        $category_all = $this->model_category->_get_category($this->_limit, $this->_offset);
        // opn($category_all);exit();
        // $category_all = $this->model_generic->_get($param_category, $this->_limit, $this->_offset);
        foreach ($category_all as $key => $value) {
        }
        $this->data['category_all'] = $category_all;

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
            $this->data['body'] = $this->parser->parse('_container_view_list.html', $this->data, true);
        }

        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function admin_()
    {
        $this->_set_referer();
        if ($this->_is_allowed(array(role_admin, role_seller))) {
            $param_category['table'] = 'category';
            $total_rows              = $this->model_generic->_count($param_category);
            $_cek_category           = $this->model_generic->_cek($param_category);
            $config_base_url         = base . '/' . controller;
            $this->data['search']    = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('category.category_name', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_category);

                $this->db->or_like('category.category_name', $this->input->get('search'));

            }
            $category_all = $this->model_generic->_get($param_category, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($category_all);exit();
            $this->data['category_all'] = $category_all;
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
        $param_category['table'] = 'category';
        $total_rows              = $this->model_generic->_count($param_category);
        $_cek_category           = $this->model_generic->_cek($param_category);
        $config_base_url         = base . '/' . controller;
        $this->data['search']    = '';
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('category.category_name', $this->input->get('search'));
            $total_rows = $this->model_generic->_count($param_category);

            $this->db->or_like('category.category_name', $this->input->get('search'));

        }
        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('category_disabled', 0);
        }
        $category_all = $this->model_generic->_get($param_category, $this->_limit, $this->_offset);
        // opn(lq());
        // opn($category_all);exit();
        $this->data['category_all'] = $category_all;
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
                $param_category            = $this->input->post();
                $param_category['table']   = 'category';
                $param_category['user_id'] = $this->_user_id;
                $this->model_generic->_insert($param_category);
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
                $category_id = $arg[0];
                if ($this->input->post()) {
                    $param_category = $this->input->post();
                    // opn($param_category);exit();

                    $category_image_before = $param_category['category_image_before'];
                    unset($param_category['category_image_before']);
                    if ($_FILES['category_image_file']['error'] == 0) {

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

                        if (!$this->upload->do_upload('category_image_file')) {
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
                        if (file_exists('./files/images/' . controller . '/' . $category_image_before . '.png') && $category_image_before != 'default') {
                            unlink('./files/images/' . controller . '/' . $category_image_before . '.png');
                        }
                        $param_category['category_image'] = $file_name;

                    }

                    $param_category['table'] = 'category';

                    if (!isset($param_category['category_parent']) && $param_category['category_level'] > 1) {
                        $param_category['category_level']  = 1;
                        $param_category['category_parent'] = 0;
                    }
                    if ($param_category['category_level'] == 1) {
                        $param_category['category_parent'] = 0;
                    }

                    /// cek dulu sub categorinya
                    $param_category_sub['table'] = 'category';
                    $this->db->where('category_parent', $category_id);
                    $sub_category = $this->model_generic->_get($param_category_sub);
                    foreach ($sub_category as $sc_value) {
                        $param_category_sub['category_level'] = $param_category['category_level'] + 1;
                        $this->db->where('category_parent', $category_id);
                        $this->model_generic->_update($param_category_sub);
                    }
                    // opn($sub_category);exit();

                    $param_category['user_id'] = $this->_user_id;
                    $this->db->where('category_id', $category_id);
                    $_cek_category = $this->model_generic->_cek($param_category);
                    if ($_cek_category) {
                        $this->db->where('category_id', $category_id);
                        $this->model_generic->_update($param_category);
                        $this->_goto_referer();
                    }
                    // opn($param);exit();
                } else {

                    $param_category['table'] = 'category';
                    $this->db->where('category_id', $category_id);
                    $category_detail = $this->model_generic->_get($param_category);
                    foreach ($category_detail as $key => $value) {
                        $value->remove_button_display = file_exists('./files/images/' . controller . '/' . $value->category_image . '.png') ? '' : 'hidden';
                        $value->category_image        = file_exists('./files/images/' . controller . '/' . $value->category_image . '.png') ? controller . '/' . $value->category_image . '.png' : 'default-category.png';
                    }
                    $this->data['category_detail'] = $category_detail;
                    // opn($category_detail);exit();

                    $this->load->library('image/library_image');

                    $param_image['entity_table'] = 'category';
                    $param_image['entity_id']    = $category_id;
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
            $category_id             = $arg[0];
            $param_category['table'] = 'category';
            $this->db->where('category_id', $category_id);
            $category_detail = $this->model_generic->_get($param_category);
            $category_parent = 0;
            foreach ($category_detail as $key => $value) {

                $category_parent       = $value->category_parent;
                $value->category_image = file_exists('./files/images/' . controller . '/' . $value->category_image . '.png') ? controller . '/' . $value->category_image . '.png' : 'default-category.png';

            }
            // opn($category_detail);exit();
            $this->data['category_detail'] = $category_detail;

            $param_category_parent['table'] = 'category';
            $this->db->where('category_id', $category_parent);
            $parent_category               = $this->model_generic->_get($param_category_parent);
            $this->data['parent_category'] = $parent_category;
            // opn($parent_category);exit();

            $param_category_sub['table'] = 'category';
            $this->db->where('category_parent', $category_id);
            $sub_category               = $this->model_generic->_get($param_category_sub);
            $this->data['sub_category'] = $sub_category;
            // opn($sub_category);exit();

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
                $category_id = $arg[0];

                $param_category['table'] = 'category';
                $this->db->where('category_parent', $category_id);
                $_cek = $this->model_generic->_cek($param_category);

                if (!$_cek) {
                    $param_category['table'] = 'category';
                    $this->db->where('category_id', $category_id);
                    $this->model_generic->_del($param_category);
                    redirect(base . '/' . controller . '/admin');
                } else {
                    echo 'Not allowed';
                    redirect(base . '/' . controller . '/detail/' . $category_id);

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
    public function category_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_category['table'] = 'category';
        $total_rows              = $this->model_generic->_count($param_category);
        $config_base_url         = base . '/' . controller . '/admin';

        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('category_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
            $result[0]['search'] = $this->input->get('search');
            if (!$this->_is_allowed(array(role_admin, role_seller))) {
                $this->db->where('category_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('category_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('category.category_name', $this->input->get('search'));
            $this->db->group_end();

            $total_rows = $this->model_generic->_count($param_category);
            if (!$this->_is_allowed(array(role_admin, role_seller))) {
                $this->db->where('category_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('category_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('category.category_name', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {
            $this->db->where('category_status', $this->input->get('status'));
            $total_rows = $this->model_generic->_count($param_category);
            $this->db->where('category_status', $this->input->get('status'));
        }
        $category_all = $this->model_category->_get_category($this->_limit, $this->_offset);
        foreach ($category_all as $key => $value) {
            $value->toggle_btn  = ($value->category_disabled) ? 'default' : 'success';
            $value->toggle_icon = ($value->category_disabled) ? 'toggle-off' : 'toggle-on';
            // $value->category_image = file_exists('./files/images/'.controller.'/')

            $value->category_image = file_exists('./files/images/' . controller . '/' . $value->category_image . '.png') ? controller . '/' . $value->category_image . '.png' : 'default-category.png';

            // $param_image['table'] = 'image';

            // $this->db->where('entity_table', 'category');
            // $this->db->where('entity_id', $value->category_id);
            // $image = $this->model_generic->_get($param_image);
            // if ($image) {
            //     $value->category_image = $image[0]->image_path . '/thumbnail/' . $image[0]->image_filename . '_128.' . $image[0]->image_ext;
            // } else {
            //     $value->category_image = 'default-category.png';
            // }

        }
        $this->data['category_all'] = $category_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($category_all);
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
    public function category_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'category';
            $this->db->where('category_id', $param['category_id']);
            $this->db->where('category_disabled != ' . $param['category_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('category_id', $param['category_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['category_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['category_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['category_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['category_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

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
            $category_id             = $arg[0];
            $param_category['table'] = 'category';
            $this->db->where('category_id', $category_id);
            $category = $this->model_generic->_get($param_category);
            foreach ($category as $value) {
                if (file_exists('./files/images/' . controller . '/' . $value->category_image . '.png')) {
                    unlink('./files/images/' . controller . '/' . $value->category_image . '.png');
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
            $param_category['table'] = 'category';
            $category_level          = $this->input->post('category_level');
            $category_id             = $this->input->post('category_id');

            $param_category_sub['table'] = 'category';
            $this->db->where('category_parent', $category_id);
            $sub_category = $this->model_generic->_get($param_category_sub);
            foreach ($sub_category as $value) {
                $this->db->where('category_id != ', $value->category_id);
            }

            $this->db->where('category_level', $category_level);
            $this->db->where('category_id != ', $category_id);
            $this->db->select('category_id as id');
            $this->db->select('category_name as text');
            $category               = $this->model_generic->_get($param_category, $this->_limit, $this->_offset);
            $this->data['category'] = $category;
            $category               = json_encode($category, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $category;
        }

        // }
    }
    /*****************************************************************************/
}

/* End of file Category.php */
/* Location: ./application/controllers/Category.php */
