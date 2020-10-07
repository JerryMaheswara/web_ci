<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item extends MY_Controller
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
        $this->load->model('model_item');
        $this->data['status'] = $this->input->get('status');

        $this->data['menu_active_home']     = '';
        $this->data['menu_active_category'] = '';
        $this->data['menu_active_item']     = 'active';

        $param_category['table'] = 'category';
        $category_all            = $this->model_generic->_get($param_category);

        $this->data['category_all'] = $category_all;

        $this->data['shop_category'] = $this->parser->parse('shop_category.html', $this->data, true);
        $this->data['aside_left']    = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right']   = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']        = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']        = $this->parser->parse('dashboard/_footer.html', $this->data, true);

    }
    /*****************************************************************************/
    public function search()
    {
        if ($this->input->get('search')) {

            $this->data['topbar']       = $this->parser->parse('welcome/_topbar.html', $this->data, true);
            $this->data['menu_desktop'] = $this->parser->parse('welcome/_menu_desktop.html', $this->data, true);
            $this->data['menu_mobile']  = $this->parser->parse('welcome/_menu_mobile.html', $this->data, true);
            $this->data['header']       = $this->parser->parse('welcome/_header.html', $this->data, true);
            $this->data['footer']       = $this->parser->parse('welcome/_footer.html', $this->data, true);

            $this->db->like('item_name', $this->input->get('search'));
            $item_all = $this->db->get('item')->result();
            // opn($item_all);exit();


            if ($item_all) {
                
                foreach ($item_all as $value) {
                    $value->is_available     = ($value->item_available > 0) ? 'is_available' : 'hidden destroy';
                    $value->is_not_available = ($value->item_available == 0 || empty($value->item_available)) ? 'is_not_available' : 'hidden destroy';

                    $param_wishlist['table'] = 'wishlist';
                    $this->db->where('user_id', $this->_user_id);
                    $this->db->where('item_id', $value->item_id);
                    $_cek = $this->model_generic->_cek($param_wishlist);

                    $value->my_wishlist = ($_cek) ? 'my_wishlist active' : '';
                    $value->iteration   = ($_cek) ? '2' : '1';

                    // $value->item_image = file_exists('./files/images/' . controller . '/' . $value->item_image . '.png') ? controller . '/' . $value->item_image . '.png' : 'default-item.png';
                    $value->item_price_format = number_format($value->item_price, 0, ',', '.');

                    $param_image['table'] = 'image';
                    $this->db->where('entity_table', 'item');
                    $this->db->where('entity_id', $value->item_id);
                    $value->item_image_main = 'default-item.png';
                    $images                 = $this->model_generic->_get($param_image);
                    $value->image_all       = $images;
                    if ($images) {

                        foreach ($images as $im_value) {
                            $item_image_main = $images[0]->image_path . '/' . $images[0]->image_filename . '.' . $images[0]->image_ext;
                        }
                        $value->item_image_main = $item_image_main;
                    }

                    // $param_image['table'] = 'image';
                    // $this->db->where('entity_table', 'item');
                    // $this->db->where('entity_id', $value->item_id);
                    // $item_images = $this->model_generic->_get($param_image);
                    // if ($item_images) {

                    //     foreach ($item_images as $im_value) {
                    //         if (file_exists('./files/images/' . controller . '/' . $im_value->image_filename . '.' . $im_value->image_ext)) {
                    //             $im_value->image_original = controller . '/' . $im_value->image_filename . '.' . $im_value->image_ext;
                    //             $im_value->image_128      = controller . '/thumbnail/' . $im_value->image_filename . '_128.' . $im_value->image_ext;
                    //             $im_value->image_256      = controller . '/thumbnail/' . $im_value->image_filename . '_256.' . $im_value->image_ext;
                    //             $im_value->image_512      = controller . '/thumbnail/' . $im_value->image_filename . '_512.' . $im_value->image_ext;
                    //         }
                    //     }
                    // }else{
                    //     $item_images[0]['image_original'] = 'default-item.png';
                    //     $item_images[0]['image_128'] = 'default-item.png';
                    //     $item_images[0]['image_256'] = 'default-item.png';
                    //     $item_images[0]['image_512'] = 'default-item.png';
                    // }
                    // $value->item_images = $item_images;

                }
                $this->data['item_all'] = $item_all;

                $this->data['body'] = $this->parser->parse('body_grid.html', $this->data, true);
            }else{
                $this->data['body'] = '<center class="page-title"><h4>Item Not Found.</h4></center>' ;
            }

            $this->parser->parse('welcome/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function index()
    {
        // opn($this->data['query_strings']);exit();

        if ($this->input->get('category_id')) {
            $param_item['table'] = 'item_category';
            $this->db->where('category_id', $this->input->get('category_id'));
            $this->db->join('item', 'item.item_id = item_category.item_id', 'left');

        } else {

            if ($this->input->get('seller_id')) {
                $this->db->where('user_id', $this->input->get('seller_id'));
            }
            $param_item['table'] = 'item';
        }

        $item_all = $this->model_generic->_get($param_item, $this->_limit, $this->_offset);
        foreach ($item_all as $value) {
            $value->is_available     = ($value->item_available > 0) ? 'is_available' : 'hidden destroy';
            $value->is_not_available = ($value->item_available == 0 || empty($value->item_available)) ? 'is_not_available' : 'hidden destroy';

            $param_wishlist['table'] = 'wishlist';
            $this->db->where('user_id', $this->_user_id);
            $this->db->where('item_id', $value->item_id);
            $_cek = $this->model_generic->_cek($param_wishlist);

            $value->my_wishlist = ($_cek) ? 'my_wishlist active' : '';
            $value->iteration   = ($_cek) ? '2' : '1';

            // $value->item_image = file_exists('./files/images/' . controller . '/' . $value->item_image . '.png') ? controller . '/' . $value->item_image . '.png' : 'default-item.png';
            $value->item_price_format = number_format($value->item_price, 0, ',', '.');

            $param_image['table'] = 'image';
            $this->db->where('entity_table', 'item');
            $this->db->where('entity_id', $value->item_id);
            $value->item_image_main = 'default-item.png';
            $images                 = $this->model_generic->_get($param_image);
            $value->image_all       = $images;
            if ($images) {

                foreach ($images as $im_value) {
                    $item_image_main = $images[0]->image_path . '/' . $images[0]->image_filename . '.' . $images[0]->image_ext;
                }
                $value->item_image_main = $item_image_main;
            }

            // $param_image['table'] = 'image';
            // $this->db->where('entity_table', 'item');
            // $this->db->where('entity_id', $value->item_id);
            // $item_images = $this->model_generic->_get($param_image);
            // if ($item_images) {

            //     foreach ($item_images as $im_value) {
            //         if (file_exists('./files/images/' . controller . '/' . $im_value->image_filename . '.' . $im_value->image_ext)) {
            //             $im_value->image_original = controller . '/' . $im_value->image_filename . '.' . $im_value->image_ext;
            //             $im_value->image_128      = controller . '/thumbnail/' . $im_value->image_filename . '_128.' . $im_value->image_ext;
            //             $im_value->image_256      = controller . '/thumbnail/' . $im_value->image_filename . '_256.' . $im_value->image_ext;
            //             $im_value->image_512      = controller . '/thumbnail/' . $im_value->image_filename . '_512.' . $im_value->image_ext;
            //         }
            //     }
            // }else{
            //     $item_images[0]['image_original'] = 'default-item.png';
            //     $item_images[0]['image_128'] = 'default-item.png';
            //     $item_images[0]['image_256'] = 'default-item.png';
            //     $item_images[0]['image_512'] = 'default-item.png';
            // }
            // $value->item_images = $item_images;

        }
        $this->data['item_all'] = $item_all;
        // opn($item_all);exit();

        $this->data['topbar']       = $this->parser->parse('welcome/_topbar.html', $this->data, true);
        $this->data['menu_desktop'] = $this->parser->parse('welcome/_menu_desktop.html', $this->data, true);
        $this->data['menu_mobile']  = $this->parser->parse('welcome/_menu_mobile.html', $this->data, true);
        // $this->data['customizer']     = $this->parser->parse('welcome/_customizer.html', $this->data, true);

        $this->data['header'] = $this->parser->parse('welcome/_header.html', $this->data, true);
        $this->data['footer'] = $this->parser->parse('welcome/_footer.html', $this->data, true);

        if ($this->input->get('view')) {
            if ($this->input->get('view') == 'grid') {
                $this->data['body'] = $this->parser->parse('body_grid.html', $this->data, true);

            } else {
                $this->data['body'] = $this->parser->parse('body_list.html', $this->data, true);

            }
        } else {
            $this->data['body'] = $this->parser->parse('body_grid.html', $this->data, true);
        }
        // $item_id               = 1;
        // $this->data['item_id'] = $item_id;
        $this->parser->parse('welcome/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function admin()
    {

        $this->_set_referer();
        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            redirect(base . '/login');
        }
        $param_item['table']  = 'item';
        $total_rows           = $this->model_generic->_count($param_item);
        $_cek_item            = $this->model_generic->_cek($param_item);
        $config_base_url      = base . '/' . controller;
        $this->data['search'] = '';
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('item.item_name', $this->input->get('search'));
        }

        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('item_disabled', 0);
        }
        if ($this->_is_allowed(array(role_seller))) {
            $this->db->join('user', 'user.user_id = item.user_id', 'left');
        }
        $item_all = $this->model_item->_get_item($this->_limit, $this->_offset);
        // opn($item_all);exit();
        // $item_all = $this->model_generic->_get($param_item, $this->_limit, $this->_offset);
        foreach ($item_all as $key => $value) {
            $value->item_price_format = number_format($value->item_price, 0, ',', '.');

        }
        $this->data['item_all'] = $item_all;

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
            $param_item['table']  = 'item';
            $total_rows           = $this->model_generic->_count($param_item);
            $_cek_item            = $this->model_generic->_cek($param_item);
            $config_base_url      = base . '/' . controller;
            $this->data['search'] = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('item.item_name', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_item);

                $this->db->or_like('item.item_name', $this->input->get('search'));

            }
            $item_all = $this->model_generic->_get($param_item, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($item_all);exit();
            $this->data['item_all']   = $item_all;
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
        $param_item['table']  = 'item';
        $total_rows           = $this->model_generic->_count($param_item);
        $_cek_item            = $this->model_generic->_cek($param_item);
        $config_base_url      = base . '/' . controller;
        $this->data['search'] = '';
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('item.item_name', $this->input->get('search'));
            $total_rows = $this->model_generic->_count($param_item);

            $this->db->or_like('item.item_name', $this->input->get('search'));

        }
        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('item_disabled', 0);
        }
        $item_all = $this->model_generic->_get($param_item, $this->_limit, $this->_offset);
        // opn(lq());
        // opn($item_all);exit();
        $this->data['item_all']   = $item_all;
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
                $param_item = $this->input->post();

                $category_ids = $param_item['category_ids'];
                unset($param_item['category_ids']);

                $param_item['table']   = 'item';
                $param_item['user_id'] = $this->_user_id;
                // $param_item['seller_id'] = $this->_user_id;
                $this->model_generic->_insert($param_item);
                $item_id = $this->db->insert_id();

                // $param_item_category['table'] = 'item_category';
                // $this->db->where('item_category.item_id', $item_id);
                // $this->model_generic->_del($param_item_category);

                foreach ($category_ids as $value) {
                    $param_item_category['item_id']     = $item_id;
                    $param_item_category['category_id'] = $value;
                    $this->model_generic->_insert($param_item_category);
                }

                // $this->_goto_referer();
                // opn($param);exit();
                redirect(base . '/' . controller . '/edit/' . $item_id);
            } else {

                $param_unit['table']    = 'unit';
                $unit_all               = $this->model_generic->_get($param_unit);
                $this->data['unit_all'] = $unit_all;
                $this->data['body']     = $this->parser->parse('_add.html', $this->data, true);
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
                $item_id = $arg[0];

                if ($this->model_item->_is_mine($item_id, $this->_user_id) || $this->_is_allowed(array(role_admin))) {
                    if ($this->input->post()) {
                        $param_item          = $this->input->post();
                        $param_item['table'] = 'item';

                        $category_ids = $param_item['category_ids'];
                        unset($param_item['category_ids']);

                        $param_item_category['table'] = 'item_category';
                        $this->db->where('item_category.item_id', $item_id);
                        $this->model_generic->_del($param_item_category);

                        foreach ($category_ids as $value) {
                            $param_item_category['item_id']     = $item_id;
                            $param_item_category['category_id'] = $value;
                            $this->model_generic->_insert($param_item_category);
                        }
                        // opn($category_ids);exit();

                        // $param_item['seller_id'] = $this->_user_id;
                        $param_item['user_id'] = $this->_user_id;
                        $this->db->where('item_id', $item_id);
                        $_cek_item = $this->model_generic->_cek($param_item);
                        if ($_cek_item) {
                            $this->db->where('item_id', $item_id);
                            $this->model_generic->_update($param_item);
                            // $this->_goto_referer();
                            redirect(base . '/' . controller . '/admin');
                        }
                        // opn($param);exit();
                    } else {

                        $param_item['table'] = 'item';
                        $this->db->where('item_id', $item_id);
                        $item_detail = $this->model_generic->_get($param_item);
                        foreach ($item_detail as $key => $value) {

                        }
                        $this->data['item_detail'] = $item_detail;
                        // opn($item_detail);exit();

                        $param_category['table']    = 'category';
                        $category_all               = $this->model_generic->_get($param_category);
                        $this->data['category_all'] = $category_all;
                        // opn($category_all);exit();

                        $param_unit['table'] = 'unit';
                        $unit_all            = $this->model_generic->_get($param_unit);
                        foreach ($unit_all as $value) {
                            $value->unit_id_nya = $value->unit_id;
                        }
                        $this->data['unit_all'] = $unit_all;
                        // opn($unit_all);exit();

                        $this->load->library('image/library_image');
                        $param_image['entity_table'] = 'item';
                        $param_image['entity_id']    = $item_id;
                        $this->data['image_upload']  = $this->library_image->add_image($param_image);

                        $this->data['body'] = $this->parser->parse('_edit.html', $this->data, true);
                        $this->parser->parse('dashboard/_index.html', $this->data, false);
                    }
                } else {
                    redirect(base . '/' . controller . '/detail/' . $item_id);
                }

            }
        } else {
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    public function detail()
    {

        // opn($angka);exit();
        // $angka       = str_split('123478');
        // $id_penyakit = 1;
        // foreach ($angka as $value) {
        //     $id_penyakit = $id_penyakit * $value;
        // }
        // echo $id_penyakit;

        // $kampret = '2';
        // if (strpos('123478', $kampret) !== false) {
        //     echo 'kutuan';
        // } else {
        //     echo 'gtau';
        // }
        // exit();

        $arg = func_get_args();
        if (isset($arg[0])) {
            $item_id               = $arg[0];
            $this->data['item_id'] = $item_id;
            $param_item['table']   = 'item';
            $this->db->where('item_id', $item_id);
            $this->db->join('user', 'user.user_id = item.user_id', 'left');
            $item_detail = $this->model_generic->_get($param_item);
            foreach ($item_detail as $key => $value) {
                $value->is_available = ($value->item_available > 0) ? 'available' : 'hidden destroy';

                $param_wishlist['table'] = 'wishlist';
                $this->db->where('user_id', $this->_user_id);
                $this->db->where('item_id', $value->item_id);
                $_cek = $this->model_generic->_cek($param_wishlist);

                $value->my_wishlist = ($_cek) ? 'my_wishlist active' : '';
                $value->iteration   = ($_cek) ? '2' : '1';

                $value->item_price_format = number_format($value->item_price, 0, ',', '.');

                $param_image['table'] = 'image';
                $this->db->where('entity_table', 'item');
                $this->db->where('entity_id', $value->item_id);
                $item_images = $this->model_generic->_get($param_image);
                // opn($item_images);exit();
                $value->item_image_main = 'default-item.png';
                if ($item_images) {

                    foreach ($item_images as $im_value) {
                        $item_image_main = $im_value->image_path . '/' . $im_value->image_filename . '.' . $im_value->image_ext;
                        if (file_exists('./files/images/' . controller . '/' . $im_value->image_filename . '.' . $im_value->image_ext)) {
                            $im_value->image_original = controller . '/' . $im_value->image_filename . '.' . $im_value->image_ext;
                            $im_value->image_128      = controller . '/thumbnail/' . $im_value->image_filename . '_128.' . $im_value->image_ext;
                            $im_value->image_256      = controller . '/thumbnail/' . $im_value->image_filename . '_256.' . $im_value->image_ext;
                            $im_value->image_512      = controller . '/thumbnail/' . $im_value->image_filename . '_512.' . $im_value->image_ext;
                        }
                    }
                    $value->item_image_main = $item_image_main;
                } else {
                    $item_images[0]['image_original'] = 'default-item.png';
                    $item_images[0]['image_128']      = 'default-item.png';
                    $item_images[0]['image_256']      = 'default-item.png';
                    $item_images[0]['image_512']      = 'default-item.png';
                }
                $value->item_images = $item_images;
                // opn($item_images);exit();

            }
            $this->data['item_detail'] = $item_detail;
            // opn($item_detail);exit();
            // $this->load->library('image/library_image');
            // $param_image['entity_table'] = 'item';
            // $param_image['entity_id']    = $item_id;
            // $this->data['item_images']   = $this->library_image->refresh($param_image);
            // $this->data['item_images'] = $this->data['image_of'];
            // opn($this->data['item_images']);exit();
            // opn($item_images);exit();

            $param_item_category['table'] = 'item_category';
            $this->db->where('item_id', $item_id);
            $this->db->join('category', 'category.category_id = item_category.category_id', 'left');
            $item_category = $this->model_generic->_get($param_item_category);
            foreach ($item_category as $value) {

            }
            $this->data['item_category'] = $item_category;
            // opn($item_category);exit();

            $this->data['topbar']       = $this->parser->parse('welcome/_topbar.html', $this->data, true);
            $this->data['menu_desktop'] = $this->parser->parse('welcome/_menu_desktop.html', $this->data, true);
            $this->data['menu_mobile']  = $this->parser->parse('welcome/_menu_mobile.html', $this->data, true);
            // $this->data['customizer']     = $this->parser->parse('welcome/_customizer.html', $this->data, true);

            $this->data['header'] = $this->parser->parse('welcome/_header.html', $this->data, true);
            $this->data['footer'] = $this->parser->parse('welcome/_footer.html', $this->data, true);

            #-------------------#
            # review item -- begin
            $param_review['table'] = 'review';
            $this->db->where('table_name', 'item');
            $this->db->where('table_id', $item_id);
            $review_count               = $this->model_generic->_count($param_review);
            $this->data['review_count'] = $review_count;

            $this->db->where('table_name', 'item');
            $this->db->where('table_id', $item_id);
            $review_all        = $this->model_generic->_get($param_review);
            $review_star_total = 0;
            foreach ($review_all as $value) {
                $review_star_total += $value->review_star;
            }
            $review_average = round($review_star_total / ($review_count ?: 1), 2);
            $whole_average  = round($review_average, 2);
            for ($i = 1; $i <= $review_average; $i++) {
                $this->data['review_average_star_' . $i] = 'filled';
            }
            $this->data['review_average'] = $review_average;
            $this->data['whole_average']  = round($whole_average);

            $this->db->where('table_name', 'item');
            $this->db->where('table_id', $item_id);
            $this->db->where('user_id', $this->_user_id);
            $review_post = $this->model_generic->_count($param_review);
            if ($review_post) {
                $this->data['review_post'] = $this->parser->parse('review_done.html', $this->data, true);
            } else {
                $this->data['review_post'] = $this->parser->parse('review_post.html', $this->data, true);

            }
            $this->data['review_grid'] = $this->parser->parse('review_grid.html', $this->data, true);
            # review item -- end
            #-------------------#

            $this->data['body'] = $this->parser->parse('detail.html', $this->data, true);
            $this->parser->parse('welcome/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function delete()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $item_id = $arg[0];
            if ($this->_is_allowed(array(role_admin, role_seller))) {

                if ($this->_is_allowed(array(role_admin)) || ($this->model_item->_is_mine($item_id, $this->_user_id) && $this->_is_allowed(array(role_seller)))) {
                    $param_item['table'] = 'item';
                    $this->db->where('item_id', $item_id);
                    $this->model_generic->_del($param_item);

                    $param_image['table'] = 'image';
                    // $param_image['entity_table'] = 'item';
                    // $param_image['entity_id']    = $item_id;
                    $this->db->where('entity_table', 'item');
                    $this->db->where('entity_id', $item_id);

                    $item_image = $this->model_generic->_get($param_image);
                    foreach ($item_image as $value) {
                        $path = BASE . '/files/images/' . $value->image_path;
                        if (file_exists($path . '/' . $value->image_filename . '.' . $value->image_ext)) {
                            unlink($path . '/' . $value->image_filename . '.' . $value->image_ext); // hapus file utama
                            $image_filename = explode('.', $value->image_filename);
                            $thumbnails     = glob($path . '/thumbnail/' . $image_filename[0] . '*');
                            array_walk($thumbnails, function ($thumbnail) {
                                unlink($thumbnail); // hapus file thumbnail
                            });
                        }
                    }
                    // opn($item_image);exit();

                    $this->db->where('entity_table', 'item');
                    $this->db->where('entity_id', $item_id);
                    $this->model_generic->_del($param_image);

                    redirect(base . '/' . controller . '/admin');
                }

            }
        }
    }
    /*****************************************************************************/
    public function item_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_item['table'] = 'item';
        if ($this->_is_allowed(array(role_seller))) {
            $this->db->where('item.user_id', $this->_user_id);
        }
        $total_rows      = $this->model_generic->_count($param_item);
        $config_base_url = base . '/' . controller . '/admin';

        if (!$this->_is_allowed(array(role_admin, role_seller))) {
            $this->db->where('item_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
            $result[0]['search'] = $this->input->get('search');
            if ($this->_is_allowed(array(role_seller))) {
                $this->db->where('item.user_id', $this->_user_id);
            }
            if (!$this->_is_allowed(array(role_admin, role_seller))) {
                $this->db->where('item_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('item_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('item.item_name', $this->input->get('search'));
            $this->db->group_end();

            if ($this->_is_allowed(array(role_seller))) {
                $this->db->where('item.user_id', $this->_user_id);
            }
            $total_rows = $this->model_generic->_count($param_item);
            if (!$this->_is_allowed(array(role_admin, role_seller))) {
                $this->db->where('item_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('item_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('item.item_name', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {

            if ($this->_is_allowed(array(role_seller))) {
                $this->db->where('item.user_id', $this->_user_id);
            }
            $this->db->where('item_status', $this->input->get('status'));
            $total_rows = $this->model_generic->_count($param_item);
            $this->db->where('item_status', $this->input->get('status'));

            if ($this->_is_allowed(array(role_seller))) {
                $this->db->where('item.user_id', $this->_user_id);
            }
        }

        if ($this->_is_allowed(array(role_seller))) {
            $this->db->where('item.user_id', $this->_user_id);
        }
        $this->db->join('user', 'user.user_id = item.user_id', 'left');
        $item_all = $this->model_item->_get_item($this->_limit, $this->_offset);
        foreach ($item_all as $key => $value) {

            $value->toggle_btn  = ($value->item_disabled) ? 'default' : 'success';
            $value->toggle_icon = ($value->item_disabled) ? 'toggle-off' : 'toggle-on';
        }
        $this->data['item_all'] = $item_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($item_all);
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
    public function item_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'item';
            $this->db->where('item_id', $param['item_id']);
            $this->db->where('item_disabled != ' . $param['item_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('item_id', $param['item_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['item_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['item_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['item_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['item_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

                echo json_encode($return);
            }
            // opn($_cek_module);exit();

        }
    }
    /*****************************************************************************/
    public function item_category()
    {
        // if ($this->input->is_ajax_request()) {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $item_id                 = $arg[0];
            $param_category['table'] = 'item_category';
            $this->db->where('item_category.item_id', $item_id);
            $item_category = $this->model_generic->_get($param_category);
            $cat           = array();
            foreach ($item_category as $value) {
                $cat[] = $value->category_id;
            }
            header('Content-Type: application/json');
            echo json_encode($cat, JSON_PRETTY_PRINT);
        }

        // }
    }
    /*****************************************************************************/
    public function review_ajax()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $param_review          = $this->input->post();
                $param_review['table'] = 'review';
                $item_id               = $param_review['item_id'];
                if (isset($param_review['item_id']) && isset($param_review['action']) && $param_review['action'] == 'add') {
                    $param_review['table_name'] = 'item';
                    $param_review['table_id']   = $item_id;
                    $param_review['user_id']    = $this->_user_id;
                    unset($param_review['action']);
                    unset($param_review['item_id']);
                    $this->model_generic->_insert($param_review);
                }

                $limit  = isset($param_review['limit']) ? $param_review['limit'] : 5;
                $offset = isset($param_review['offset']) ? $param_review['offset'] : 0;

                $this->db->where('table_name', 'item');
                $this->db->where('table_id', $item_id);
                $this->db->join('user', 'user.user_id = review.user_id', 'left');
                $review_all = $this->model_generic->_get($param_review, $limit, $offset);
                foreach ($review_all as $value) {
                    $value->review_author_name   = $value->user_name_first . ' ' . $value->user_name_last;
                    $value->review_author_avatar = file_exists('./files/images/user/' . $value->user_avatar . '.png') ? 'user/' . $value->user_avatar . '.png' : 'default.png';
                    $review_star                 = $value->review_star;
                    for ($i = 1; $i <= $review_star; $i++) {
                        $value->{'rating_star_' . $i} = 'filled';
                    }
                }
                $this->data['review_all'] = $review_all;
                $res['review_data']       = $this->parser->parse('review_data.html', $this->data, true);

                $this->db->where('table_name', 'item');
                $this->db->where('table_id', $item_id);
                $review_count        = $this->model_generic->_count($param_review);
                $res['review_count'] = $review_count;

                header('Content-Type: application/json');
                echo json_encode($res);
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Item.php */
/* Location: ./application/controllers/Item.php */
