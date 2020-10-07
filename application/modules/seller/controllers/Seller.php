<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Seller extends MY_Controller
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
        // $this->load->model('user/model_user');
        $this->data['status'] = $this->input->get('status');

        $this->passwordHasher = new Hautelook\Phpass\PasswordHash(64, false);
        // $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
        // $xx = new PHPExcel();

    }
    /*****************************************************************************/
    public function index()
    {

        $this->_set_referer();
        // if (!$this->_is_allowed(array(role_admin, role_user))) {
        //     redirect(base . '/' . controller . '/login');
        // } else {
        //     if ($this->_is_allowed(array(role_user))) {
        //         redirect(base . '/' . controller . '/profile');
        //     }
        // }
        // $param_user_role['table'] = 'user_role';
        // $this->db->where('role_label_id', role_seller);
        // $total_rows            = $this->model_generic->_count($param_user_role);

        // $config_base_url       = base . '/' . controller;
        // $this->data['search']  = '';

        // if ($this->input->get('order') && $this->input->get('sort')) {
        //     $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        // }

        // if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
        //     $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
        //     $this->data['search'] = $this->input->get('search');
        //     $this->db->or_like('user.user_name_first', $this->input->get('search'));
        // }

        // if (!$this->_is_allowed(array(role_admin))) {
        //     $this->db->where('user.user_disabled', 0);
        // }
        // $param_user_role['table'] = 'user_role';
        // $this->db->where('role_label_id', role_seller);
        // $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
        // $seller_all = $this->model_generic->_get($param_user_role, $this->_limit, $this->_offset);
        // // opn(lq());
        // // opn($seller_all); exit();
        // foreach ($seller_all as $key => $value) {
        //     $value->user_avatar = file_exists(BASE . '/files/images/' . controller . '/' . $value->user_avatar . '.png') ? controller . '/' . $value->user_avatar . '.png' : 'default.png';
        // }
        // $this->data['seller_all'] = $seller_all;
        // $this->data['user_all'] = $seller_all;
        // // opn($seller_all);exit();

        // $this->load->library('pagination');
        // $config['base_url']             = $config_base_url;
        // $config['total_rows']           = $total_rows;
        // $config['query_string_segment'] = 'offset';
        // $config['per_page']             = $this->_limit;

        // $this->pagination->initialize($config);
        // $this->data['paging']     = $this->pagination->create_links();
        // $this->data['total_rows'] = $total_rows;
        // opn($this->data['paging'] );exit();

        $this->data['searchbar'] = $this->parser->parse('_searchbar.html', $this->data, true);

        $this->data['view'] = 'list';
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
                    $this->data['body'] = $this->parser->parse('_container_view_list.html', $this->data, true);
                    break;
            }
        } else {
            $this->data['body'] = $this->parser->parse('_container_view_list.html', $this->data, true);
        }

        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function admin()
    {
        $this->data['view'] = '';
        $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            $param_user['table'] = 'user';
            $total_rows            = $this->model_generic->_count($param_user);
            $_cek_user           = $this->model_generic->_cek($param_user);
            $config_base_url       = base . '/' . controller;
            $this->data['search']  = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('user.user_name_first', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_user);

                $this->db->or_like('user.user_name_first', $this->input->get('search'));

            }
            $user_all = $this->model_generic->_get($param_user, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($user_all);exit();
            $this->data['user_all'] = $user_all;
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
    public function profile()
    {
        $this->_set_referer();
        // opn($_SESSION);exit();

        if ($this->_is_allowed(array(role_admin, role_user))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                if ($this->_is_allowed(array(role_admin))) {
                    $user_id = $arg[0];
                } else {
                    $user_id = $this->_user_id;
                    redirect(base . '/' . controller . '/profile');
                }
            } else {
                $user_id = $this->_user_id;
                if ($this->_is_allowed(array(role_admin))) {
                    redirect(base . '/' . controller);
                }
            }
            $this->data['user_id_before'] = $user_id;

            $param_user['table'] = 'user';
            $this->db->where('user.user_id', $user_id);
            // $this->db->join('user', 'user.user_id = user.user_id', 'left');
            $this->db->join('kodepos', 'kodepos.kodepos_id = user.kodepos_id', 'left');
            $user_detail = $this->model_generic->_get($param_user, $this->_limit, $this->_offset);
            foreach ($user_detail as $value) {
                $value->user_avatar = file_exists('./files/images/' . controller . '/' . $value->user_avatar . '.png') ? controller . '/' . $value->user_avatar . '.png' : 'default.png';
            }
            $this->data['user_detail'] = $user_detail;
            // opn($user_detail);exit();

            $param_item['table'] = 'item';
            $this->db->where('user_id', $user_id);
            $user_item = $this->model_generic->_get($param_item);
            foreach ($user_item as $value) {

                $param_image['table'] = 'image';
                $this->db->where('entity_table', 'item');
                $this->db->where('entity_id', $value->item_id);
                $item_images = $this->model_generic->_get($param_image);
                foreach ($item_images as $im_value) {
                    if (file_exists('./files/images/item/' . $im_value->image_filename . '.' . $im_value->image_ext)) {
                        $im_value->image_original = 'item/' . $im_value->image_filename . '.' . $im_value->image_ext;
                        $im_value->image_128      = 'item/thumbnail/' . $im_value->image_filename . '_128.' . $im_value->image_ext;
                        $im_value->image_256      = 'item/thumbnail/' . $im_value->image_filename . '_256.' . $im_value->image_ext;
                        $im_value->image_512      = 'item/thumbnail/' . $im_value->image_filename . '_512.' . $im_value->image_ext;
                    } else {
                        $im_value->image_original = 'default.png';
                        $im_value->image_128      = 'default.png';
                        $im_value->image_256      = 'default.png';
                        $im_value->image_512      = 'default.png';
                    }
                }
                $value->item_images = $item_images;
                $value->item_price_format = number_format($value->item_price);

            }
            // opn($user_item);exit();
            $this->data['user_item'] = $user_item;
            $this->data['body']        = $this->parser->parse('_profile.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        } else {
            redirect(base . '/' . controller . '/login');
        }
    }
    /*****************************************************************************/
    public function add()
    {
        redirect(base.'/seller/register');
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->post()) {
                $param_user = $this->input->post();
                // opn($param_user);exit();

                $param_user['table']           = 'user';
                $param_user['user_password']   = $this->passwordHasher->HashPassword($param_user['user_password']);
                $param_user['user_name_first'] = $param_user['user_name_first'];
                $param_user['user_slug']       = slug($param_user['user_name_first'], ''); //. ' ' . $param['user_name_last'], '_');
                $param_user['user_birthday']   = $param_user['user_tanggal_lahir'];
                $param_user['user_gender']     = $param_user['user_jenis_kelamin'];

                // $ugn = strtolower(substr($param_user['user_slug'], 0, 8));
                // // $ufn = strtoupper(substr($param['user_name_last'], 0, 2));
                // $ubd = substr($param_user['user_birthday'], -5);
                // $ubd = explode('-', $ubd);
                // $ubd = $ubd[1] . $ubd[0];

                // $param_user['user_name'] = $param_user['user_nomor']; // $ugn . $ubd;

                $this->model_generic->_insert($param_user);
                $user_id = $this->db->insert_id();

                $param_user_role['table'] = 'user_role';
                $this->db->where('user_id', $user_id);
                $this->model_generic->_del($param_user_role);

                $param_user_role['user_id']       = $user_id;
                $param_user_role['role_label_id'] = role_seller;
                $this->model_generic->_insert($param_user_role);

                // $param_user['user_id'] = $user_id;
                // $param_user['table']   = 'user';
                // $this->model_generic->_insert($param_user);

                // $param_user['table'] = 'user';

                // $param_user['user_password']   = $this->passwordHasher->HashPassword('123456');
                // $param_user['user_name_first'] = $param_user['user_name_first'];
                // $param_user['user_birthday']   = $param_user['user_tanggal_lahir'];
                // $param_user['user_gender']     = $param_user['user_jenis_kelamin'];
                // $param_user['user_slug']       = slug($param['user_name_first'], '_'); //. ' ' . $param['user_name_last'], '_');
                // $this->model_generic->_insert($param_user);

                // $param_user['user_id'] = $user_id;
                // $param_user['table']   = 'user';
                // $this->model_generic->_insert($param_user);
                // $this->_goto_referer();
                redirect(base.'/seller');
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

        if ($this->_is_allowed(array(role_member))) {
            $user_id = $this->_user_id;
        }

        // $this->_set_referer();
        $arg = func_get_args();
        if (isset($arg[0]) && $this->_is_allowed(array(role_admin))) {
            $user_id = $arg[0];
        }
        if ($this->input->post()) {
            $param_user = $this->input->post();
            // opn($param_user);exit();
            $user_avatar_before = $param_user['user_avatar_before'];
            unset($param_user['user_avatar_before']);

            if ($_FILES['user_avatar_file']['error'] == 0) {
                // opn($_FILES['user_avatar']);exit();
                $upload_path = './files/images/' . controller;
                $file_name   = md5(time() . uniqid());

                if (!is_dir($upload_path)) {
                    mkdir($upload_path);
                }
                chmod($upload_path, 0777);
                $config['upload_path']   = './files/images/' . controller;
                $config['allowed_types'] = 'png';
                // $config['max_size']         = 1024;
                $config['max_width']        = 512;
                $config['max_height']       = 512;
                $config['file_name']        = $file_name;
                $config['overwrite']        = true;
                $config['file_ext_tolower'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('user_avatar_file')) {
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
                if (file_exists('./files/images/' . controller . '/' . $user_avatar_before . '.png') && $user_avatar_before != 'default') {
                    unlink('./files/images/' . controller . '/' . $user_avatar_before . '.png');
                }
                $param_user['user_avatar'] = $file_name;
                if ($user_id == $this->_user_id) {
                    $_SESSION['user_info'][0]->user_avatar = file_exists('./files/images/' . controller . '/' . $file_name . '.png') ? controller . '/' . $file_name . '.png' : 'default.png';
                }

            }

            $param_user['table'] = 'user';
            $this->db->where('user_id', $user_id);
            $_cek_user = $this->model_generic->_cek($param_user);
            if ($_cek_user) {
                $this->db->where('user_id', $user_id);
                // $param_user['user_avatar'] = $param_user['user_nomor'];

                $this->model_generic->_update($param_user);
                $this->db->where('user_id', $user_id);
                $user = $this->model_generic->_get($param_user);
                // opn($_SESSION['user_info']);exit();
                redirect(base . '/' . controller . '/profile/' . $user[0]->user_id);
            }
        } else {

            $param_user['table'] = 'user';
            $this->db->where('user_id', $user_id);
            $user_detail = $this->model_generic->_get($param_user);
            // opn($_SESSION['user_info']);exit();
            foreach ($user_detail as $key => $value) {
                $value->remove_button_display = file_exists('./files/images/' . controller . '/' . $value->user_avatar . '.png') ? '' : 'hidden destroy';
                $value->user_avatar         = file_exists('./files/images/' . controller . '/' . $value->user_avatar . '.png') ? controller . '/' . $value->user_avatar . '.png' : 'default.png';
            }
            $this->data['user_detail'] = $user_detail;
            // opn($user_detail);exit();

            $this->data['body'] = $this->parser->parse('_edit.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function detail()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $user_id             = $arg[0];
            $param_user['table'] = 'user';
            $this->db->where('user_id', $user_id);
            $user_detail = $this->model_generic->_get($param_user);
            foreach ($user_detail as $key => $value) {
                $value->user_avatar                 = file_exists('./files/images/' . controller . '/' . $value->user_avatar . '.png') ? controller . '/' . $value->user_avatar . '.png' : 'default.png';
                $value->user_tanggal_expired_format = date('m/Y', strtotime($value->user_tanggal_expired));
            }
            $this->data['user_detail'] = $user_detail;

            $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function delete()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $user_id = $arg[0];
            if ($this->_is_allowed(array(role_admin))) {
                $param_user['table'] = 'user';

                $this->db->where('user_id', $user_id);
                $_cek = $this->model_generic->_cek($param_user);
                if ($_cek) {

                    $this->db->where('user_id', $user_id);
                    $user = $this->model_generic->_get($param_user);
                    foreach ($user as $key => $value) {
                        $param_user['table'] = 'user';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user);

                        $param_user_role['table'] = 'user_role';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user_role);
                    }
                    $this->db->where('user_id', $user_id);
                    $this->model_generic->_del($param_user);
                }

                // redirect(base . '/' . controller);
                $this->_goto_referer();
            }
        }
    }
    /*****************************************************************************/
    public function seller_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_user_role['table'] = 'user_role';
        $this->db->where('role_label_id', role_seller);
        $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
        $total_rows            = $this->model_generic->_count($param_user_role);
        // opn($total_rows);exit();
        if ($this->input->get('view') && !empty($this->input->get('view'))) {
            $config_base_url = base . '/' . controller . '?view=' . $this->input->get('view');
        } else {
            $config_base_url = base . '/' . controller . '/admin';
        }

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('user.user_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
            $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
            $result[0]['search'] = $this->input->get('search');
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('user_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('user_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('user_name_first', $this->input->get('search'));
            $this->db->or_like('user_name_last', $this->input->get('search'));
            $this->db->group_end();
            $this->db->where('user_role.role_label_id', role_seller);

            $total_rows = $this->model_generic->_count($param_user_role);

            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('user_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('user_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('user_name_first', $this->input->get('search'));
            $this->db->or_like('user_name_last', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {
            $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
            $this->db->where('user.user_status', $this->input->get('status'));
            $this->db->where('user_role.role_label_id', role_seller);
            $total_rows = $this->model_generic->_count($param_user_role);

            $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
            $this->db->where('user.user_status', $this->input->get('status'));
        }

        if ($this->input->get('order') && $this->input->get('sort')) {
            $this->db->order_by('user.'.$this->input->get('order'), 'user.'.$this->input->get('sort'));
        }

        $this->db->where('role_label_id', role_seller);
        $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
        $user_all = $this->model_generic->_get($param_user_role, $this->_limit, $this->_offset);
        // opn($user_all);exit();
        foreach ($user_all as $key => $value) {
            $value->toggle_btn            = ($value->user_disabled) ? 'default' : 'success';
            $value->toggle_icon           = ($value->user_disabled) ? 'toggle-off' : 'toggle-on';
            $value->user_avatar         = file_exists('./files/images/' . controller . '/' . $value->user_avatar . '.png') ? controller . '/' . $value->user_avatar . '.png' : 'default.png';
            $value->user_created_format = date('F d, Y', strtotime($value->{'user_created'}));



            $this->db->where('user_id', $value->user_id);
            $value->jumlah_item = $this->db->get('item')->num_rows();

        }
        $this->data['user_all'] = $user_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($user_all);
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
        // opn($result);exit();
        header('Content-Type: application/json');
        echo json_encode($result);

        // }
    }
    /*****************************************************************************/
    public function user_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'user';
            $this->db->where('user_id', $param['user_id']);
            $this->db->where('user_disabled != ' . $param['user_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('user_id', $param['user_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['user_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['user_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['user_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['user_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

                echo json_encode($return);
            }
            // opn($_cek_module);exit();

        }
    }
    /*****************************************************************************/
    public function excel()
    {

        if ($this->input->post()) {
            $param = $this->input->post();
            // opn($_FILES);exit();

            if ($_FILES['file_user']) {
                $fileName = time() . $_FILES['file_user']['name'];
                // echo $fileName;exit();
                // $param = $this->input->post();
                // opn($fileName);exit();
                //////////////-----------------------------------------

                $config['upload_path']   = './files/excel_upload/'; //buat folder dengan nama assets di root folder
                $config['file_name']     = $fileName;
                $config['allowed_types'] = 'xls|xlsx|csv';
                $config['max_size']      = 10000;

                $this->load->library('upload');
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('file_user')) {
                    $this->upload->display_errors();
                }

                $file_user   = $this->upload->data();
                $inputFileName = './files/excel_upload/' . $file_user['file_name'];
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader     = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel   = $objReader->load($inputFileName);
                    // opn($objPHPExcel);exit();
                } catch (Exception $e) {
                    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                }

                $sheet         = $objPHPExcel->getSheet(0);
                $highestRow    = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // opn($sheet);exit();

                for ($row = 2; $row <= $highestRow; $row++) {
                    //  Read a row of data into an array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);

                    // $stringDate = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][2], 'YYYY-MM-DD');
                    $param_user['user_nomor']               = $rowData[0][0];
                    $param_user['user_name_first']          = $rowData[0][1];
                    $param_user['user_tanggal_lahir']       = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][2], 'YYYY-MM-DD');
                    $param_user['user_kategori']            = $rowData[0][3];
                    $param_user['user_avatar']              = $rowData[0][4];
                    $param_user['user_jenis_kelamin']       = $rowData[0][5];
                    $param_user['user_status']              = $rowData[0][6];
                    $param_user['user_nomor_pasangan']      = $rowData[0][7];
                    $param_user['user_name_first_pasangan'] = $rowData[0][8];
                    $param_user['user_tanggal_anniversary'] = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][9], 'YYYY-MM-DD');
                    $param_user['user_tanggal_expired']     = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][10], 'YYYY-MM-DD');
                    // opn($rowData);exit();

                    $param_user['table'] = 'user';
                    // opn($param_user); //exit();
                    $this->db->where('user_nomor', $param_user['user_nomor']);
                    $cek = $this->model_generic->_cek($param_user);
                    if (!$cek) {

                        $param_user['table']           = 'user';
                        $param_user['user_password']   = $this->passwordHasher->HashPassword('123456');
                        $param_user['user_name_first'] = $param_user['user_name_first'];
                        $param_user['user_slug']       = slug($param_user['user_name_first'], ''); //. ' ' . $param['user_name_last'], '_');
                        $param_user['user_birthday']   = $param_user['user_tanggal_lahir'];
                        $param_user['user_gender']     = $param_user['user_jenis_kelamin'];

                        $ugn = strtolower(substr($param_user['user_slug'], 0, 8));
                        // $ufn = strtoupper(substr($param['user_name_last'], 0, 2));
                        $ubd = substr($param_user['user_birthday'], -5);
                        $ubd = explode('-', $ubd);
                        $ubd = $ubd[1] . $ubd[0];

                        $param_user['user_name'] = $param_user['user_nomor']; // $ugn . $ubd;

                        $this->model_generic->_insert($param_user);
                        $user_id = $this->db->insert_id();

                        $param_user_role['table'] = 'user_role';
                        $this->db->where('user_id', $user_id);
                        $this->model_generic->_del($param_user_role);

                        $param_user_role['user_id']       = $user_id;
                        $param_user_role['role_label_id'] = 1;
                        $this->model_generic->_insert($param_user_role);

                        $param_user['user_id'] = $user_id;
                        $param_user['table']   = 'user';
                        $this->model_generic->_insert($param_user);
                    } else {

                        $this->db->where('user_nomor', $param_user['user_nomor']);
                        $cek = $this->model_generic->_update($param_user);
                    }
                }
                delete_files($file_user['file_path']);
                redirect(base . '/' . controller);
            }
        } else {

            $this->data['body'] = $this->parser->parse('_import.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function create_favicon()
    {
        $source      = BASE . '/files/images/logo.png';
        $destination = BASE . '/files/images/favicon.ico';

        $ico_lib = new PHP_ICO($source);
        $ico_lib->save_ico($destination);
    }
    /*****************************************************************************/
    public function logout()
    {
        session_destroy();
        redirect(base . '/' . controller . '/');
    }
    /*****************************************************************************/
    public function login()
    {
        if ($this->input->post()) {
            $param                 = $this->input->post();
            $param_user['table'] = 'user';
            $this->db->where('user_email', $param['user_email']);
            $user        = $this->model_generic->_get($param_user);
            $passwordMatch = false;
            opn($passwordMatch); //exit();
            foreach ($user as $value) {
                $passwordMatch          = $this->passwordHasher->CheckPassword($param['user_password'], $value->user_password);
                $value->user_role       = array(1);
                $value->user_id         = $value->user_id;
                $value->user_name_first = $value->user_name;
                $value->user_name_last  = '';
                $value->user_avatar     = file_exists('./files/images/' . controller . '/' . $value->user_avatar . '.png') ? 'user/' . $value->user_avatar . '.png' : 'default.png';
            }
            // opn($passwordMatch); //exit();
            if ($passwordMatch) {
                $_SESSION['user_info'] = $user;
            }
            // opn($param);exit();
            redirect(base . '/' . controller . '/');
        } else {
            session_destroy();
            // opn($_SESSION);exit();
            $this->parser->parse('_login.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function register()
    {
        if ($this->input->post()) {
            $param_user = $this->input->post();
            unset($param_user['user_password_confirm']);
            unset($param_user['agree']);
            $param_user['user_verification_code'] = md5(base64_encode(md5($param_user['user_email'])));
            $param_user['user_password']          = $this->passwordHasher->HashPassword($param_user['user_password']);
            $param_user['user_slug']              = slug($param_user['user_name_first'], '_');
            // opn($param_user);exit();
            $param_user['table'] = 'user';
            $this->model_generic->_insert($param_user);

            $user_id = $this->db->insert_id();

            $param_user_role['user_id']       = $user_id;
            $param_user_role['table']         = 'user_role';
            $param_user_role['role_label_id'] = role_seller;
            $this->model_generic->_insert($param_user_role);

            redirect(base . '/login');
        } else {
            $this->parser->parse('_register.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function check_email()
    {
        if ($this->input->post('user_email')) {
            $param_user['table'] = 'user';
            $this->db->where('user_email', $this->input->post('user_email'));
            $total_rows = $this->model_generic->_count($param_user);
            if ($total_rows == 1) {
                $response['status']  = '0';
                $response['message'] = 'Email already taken.';
                echo json_encode($response);
            } else {
                $response['status']  = '1';
                $response['message'] = 'Email available.';
                echo json_encode($response);
            }
        }
    }
    /*****************************************************************************/
    public function remove_avatar()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $user_id             = $arg[0];
            $param_user['table'] = 'user';
            $this->db->where('user_id', $user_id);
            $user = $this->model_generic->_get($param_user);
            foreach ($user as $value) {
                if (file_exists('./files/images/' . controller . '/' . $value->user_avatar . '.png')) {
                    unlink('./files/images/' . controller . '/' . $value->user_avatar . '.png');

                    $this->db->where('user_id', $user_id);
                    $param_user['user_avatar'] = '';
                    $this->model_generic->_update($param_user);
                }
                $res['status']  = 1;
                $res['message'] = 'Avatar deleted.';
                echo json_encode($res);

            }
        }

    }
    /*****************************************************************************/
}

/* End of file Seller.php */
/* Location: ./application/controllers/Seller.php */
