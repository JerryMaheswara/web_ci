<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        $this->load->library('library_module');

        // $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        // $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        // $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        // $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);
        $this->data['_is_allowed'] = $this->_is_allowed(array(role_admin, role_seller)) ? '' : 'hidden destroy';

        $this->data['header']       = $this->parser->parse('welcome/_header.html', $this->data, true);
        $this->data['footer']       = $this->parser->parse('welcome/_footer.html', $this->data, true);
        $this->data['topbar']       = $this->parser->parse('welcome/_topbar.html', $this->data, true);
        $this->data['menu_desktop'] = $this->parser->parse('welcome/_menu_desktop.html', $this->data, true);
        $this->data['menu_mobile']  = $this->parser->parse('welcome/_menu_mobile.html', $this->data, true);

        $param_wishlist['table'] = 'wishlist';
        $this->db->where('user_id', $this->_user_id);
        $wishlist_badge = $this->model_generic->_count($param_wishlist);

        $this->data['wishlist_badge'] = $wishlist_badge;

        $this->data['account_info'] = $this->parser->parse('_account_info.html', $this->data, true);

        $this->data['info_active'] = method;
        $this->passwordHasher      = new Hautelook\Phpass\PasswordHash(64, false);

    }

    /*****************************************************************************/
    public function index()
    {
        redirect(base . '/' . controller . '/profile');
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function profile()
    {
        if ($this->_is_login) {

            if ($this->_is_allowed(array(role_admin))) {
                $arg = func_get_args();
                if (isset($arg[0])) {
                    $user_id     = $arg[0];
                    $table_aktif = 'user';

                    $this->data['table_aktif'] = $table_aktif;
                    $param_member['table']     = $table_aktif;
                    $this->db->where($table_aktif . '_id', $user_id);
                    $account_detail = $this->model_generic->_get($param_member);
                    foreach ($account_detail as $value) {
                        $value->my_name_display   = $value->{$table_aktif . '_name_display'};
                        $value->my_name_first     = $value->{$table_aktif . '_name_first'};
                        $value->my_name_last      = $value->{$table_aktif . '_name_last'};
                        $value->my_created_format = date('F d, Y', strtotime($value->{$table_aktif . '_created'}));
                        $value->my_avatar         = (file_exists('./files/images/' . $table_aktif . '/' . $value->{$table_aktif . '_avatar'} . '.png')) ? $table_aktif . '/' . $value->{$table_aktif . '_avatar'} . '.png' : 'default.png';
                    }
                    $this->data['my_account_detail'] = $account_detail;

                }
            } else {
                $user_id = $this->_user_id;
            }

            // opn($user_id);exit();
            $arg = func_get_args();
            if (isset($arg[0]) && $this->input->get('mode')) {
                $user_id               = $arg[0];
                $table_aktif           = $this->input->get('mode');
                $param_member['table'] = $table_aktif;
                $this->db->where($table_aktif . '_id', $user_id);
                $account_detail = $this->model_generic->_get($param_member);
                if (!$account_detail) {
                    $this->data['body'] = $this->parser->parse('no_profile.html', $this->data, true);
                } else {
                    // opn($member);exit();
                    foreach ($account_detail as $value) {
                        $value->my_name_display   = $value->{$table_aktif . '_name_display'};
                        $value->my_name_first     = $value->{$table_aktif . '_name_first'};
                        $value->my_name_last      = $value->{$table_aktif . '_name_last'};
                        $value->my_created_format = date('F d, Y', strtotime($value->{$table_aktif . '_created'}));
                        $value->my_avatar         = (file_exists('./files/images/' . $table_aktif . '/' . $value->{$table_aktif . '_avatar'} . '.png')) ? $table_aktif . '/' . $value->{$table_aktif . '_avatar'} . '.png' : 'default.png';
                    }
                    $this->data['other_account_detail'] = $account_detail;
                    $this->data['other_account_info']   = $this->parser->parse('_account_info.html', $this->data, true);
                    $this->data['body']                 = $this->parser->parse('other_profile.html', $this->data, true);
                }
                if ($user_id == $this->_user_id) {
                    $this->data['body'] = $this->parser->parse('my_profile.html', $this->data, true);
                }
            } else {
                // opn($_SESSION);exit();
                $this->data['body'] = $this->parser->parse('my_profile.html', $this->data, true);
            }
            $this->parser->parse('welcome/_index.html', $this->data, false);

        }
    }
    /*****************************************************************************/
    public function profile_update()
    {
        if ($this->_is_login) {
            // if ($this->input->is_ajax_request()) {
            $table_aktif = $_SESSION['table_aktif'];
            if ($this->input->post()) {
                $param_update = $this->input->post();

                $avatar_before = $param_update['avatar_before'];
                unset($param_update['avatar_before']);
                if ($_FILES['avatar']['error'] == 0) {
                    // opn($_FILES);exit();

                    // opn($_FILES['seller_avatar']);exit();
                    $upload_path = './files/images/' . $table_aktif;
                    $file_name   = md5(time() . uniqid());

                    if (!is_dir($upload_path)) {
                        mkdir($upload_path);
                    }
                    chmod($upload_path, 0777);
                    $config['upload_path']   = $upload_path;
                    $config['allowed_types'] = 'png';
                    // $config['max_size']         = 1024;
                    $config['max_width']        = 256;
                    $config['max_height']       = 256;
                    $config['file_name']        = $file_name;
                    $config['overwrite']        = true;
                    $config['file_ext_tolower'] = true;

                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('avatar')) {
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
                    if (file_exists('./files/images/' . $table_aktif . '/' . $avatar_before . '.png') && $avatar_before != 'default.png') {
                        unlink('./files/images/' . $table_aktif . '/' . $avatar_before . '.png');
                    }
                    $param_update[$table_aktif . '_avatar'] = $file_name;
                    if ($seller_id == $this->_user_id) {
                        $_SESSION['user_info'][0]->user_avatar = file_exists('./files/images/' . $table_aktif . '/' . $file_name . '.png') ? $table_aktif . '/' . $file_name . '.png' : 'default.png';
                    }

                }

                unset($param_update[$table_aktif . '_password_confirm']);
                // opn($param_update);exit();
                if (empty($param_update[$table_aktif . '_password'])) {
                    unset($param_update[$table_aktif . '_password']);
                } else {
                    $param_update[$table_aktif . '_password'] = $this->passwordHasher->HashPassword($param_update[$table_aktif . '_password']);
                }
                $param_update[$table_aktif . '_slug'] = slug($param_update[$table_aktif . '_name_first'], '_');

                $param_update['table'] = $table_aktif;
                $this->db->where($table_aktif . '_id', $param_update[$table_aktif . '_id']);
                $_cek = $this->model_generic->_cek($param_update);
                if ($_cek) {
                    $this->db->where($table_aktif . '_id', $param_update[$table_aktif . '_id']);
                    $this->model_generic->_update($param_update);
                }
                // opn($update);exit();
                redirect(base . '/' . controller);
            }
            // }
        }

    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function remove_avatar()
    {
        $table_aktif = $_SESSION['table_aktif'];

        $arg = func_get_args();
        if (isset($arg[0])) {
            $id             = $arg[0];
            $param['table'] = $table_aktif;
            $this->db->where($table_aktif . '_id', $id);
            $avatar = $this->model_generic->_get($param);
            foreach ($avatar as $value) {
                if (file_exists('./files/images/' . $table_aktif . '/' . $value->{$table_aktif . '_avatar'} . '.png')) {
                    unlink('./files/images/' . $table_aktif . '/' . $value->{$table_aktif . '_avatar'} . '.png');

                    $param[$table_aktif . '_avatar'] = '';
                    $this->db->where($table_aktif . '_id', $id);
                    $this->model_generic->_update($param);
                }
                $res['status']  = 1;
                $res['message'] = 'Avatar deleted.';
                echo json_encode($res);
            }

        }

    }
    /*****************************************************************************/
    public function order()
    {
        $this->data['body'] = $this->parser->parse('_order.html', $this->data, true);

        $this->parser->parse('welcome/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function address()
    {
        if ($this->_is_login) {

            if ($this->input->post()) {
                $param_billing            = $this->input->post();
                $param_billing['table']   = 'billing';

                if (!isset($param_billing['billing_coverage'])) {
                    $param_billing['billing_coverage'][] = $param_billing['billing_subdistrict'];
                }
                $param_billing['billing_coverage'] = implode('|', $param_billing['billing_coverage']);
                $param_billing['user_id'] = $this->_user_id;
                $param_billing['role_label_id'] = $_SESSION['user_info'][0]->user_role[0]; 
                // opn($param_billing);exit();
                $this->db->where('user_id', $this->_user_id);
                $cek = $this->model_generic->_cek($param_billing);
                if ($cek) {
                    $this->db->where('user_id', $this->_user_id);
                    $this->model_generic->_update($param_billing);
                }else{
                    $this->model_generic->_insert($param_billing);
                }
                if (isset($_SESSION['referer'])) {
                    $this->_goto_referer();
                }else{
                    redirect(base . '/' . controller . '/address', 'refresh');
                }

            } else {

                $param_kodepos['table'] = 'kodepos';
                $this->db->like('kabupaten', 'Balikpapan');
                $this->db->group_by('kecamatan');
                $coverage = $this->model_generic->_get($param_kodepos);
                foreach ($coverage as $value) {
                    $this->db->where('kecamatan', $value->kecamatan);
                    $kelurahan = $this->model_generic->_get($param_kodepos);
                    $kel = array();
                    foreach ($kelurahan as $kel_key => $kel_value) {
                        foreach ($kel_value as $kv_key=>$kv_value) {
                            $kel[$kel_key]['kel_'.$kv_key] = $kv_value;
                        }
                    }
                    $value->kelurahan = $kel;
                }
                $this->data['coverage'] = $coverage;
                // opn($coverage);exit();


                $param_kodepos['table'] = 'kodepos';
                // $this->db->where('kecamatan', $Value);
                $kecamatan               = $this->model_generic->_cek($param_kodepos);
                $this->data['kecamatan'] = $kecamatan;

                $param_billing['table'] = 'billing';
                $this->db->where('user_id', $this->_user_id);
                $cek = $this->model_generic->_cek($param_billing);
                $this->data['is_seller'] = $this->_is_allowed(array(role_seller))?'is_seller':'hidden destroy';

                if ($cek) {
                    $param_billing['table'] = 'billing';

                    $this->db->where('user_id', $this->_user_id);
                    $account_address               = $this->model_generic->_get($param_billing);
                    $this->data['account_address'] = $account_address;
                    $this->data['body']            = $this->parser->parse('_address_exist.html', $this->data, true);
                } else {
                    // $this->data['account_address'] = array();
                    $this->data['body'] = $this->parser->parse('_address_new.html', $this->data, true);

                }
                $this->parser->parse('welcome/_index.html', $this->data, false);
            }
        }

    }
    /*****************************************************************************/
    public function wishlist_add()
    {
        if ($this->_is_login) {
            if ($this->input->is_ajax_request()) {
                if ($this->input->post()) {
                    $param_wishlist = $this->input->post();
                    // opn($param);exit();

                    $param_wishlist['table']   = 'wishlist';
                    $param_wishlist['user_id'] = $this->_user_id;
                    $this->db->where('user_id', $this->_user_id);
                    $this->db->where('item_id', $param_wishlist['item_id']);
                    $_cek = $this->model_generic->_cek($param_wishlist);
                    if (!$_cek) {
                        $this->model_generic->_insert($param_wishlist);
                    }
                }
            }
        }
    }
    /*****************************************************************************/
    public function wishlist_remove()
    {
        if ($this->_is_login) {
            if ($this->input->is_ajax_request()) {
                if ($this->input->post()) {
                    $param_wishlist = $this->input->post();
                    // opn($param_wishlist);exit();

                    $param_wishlist['table'] = 'wishlist';
                    $this->db->where('user_id', $this->_user_id);
                    $this->db->where('item_id', $param_wishlist['item_id']);
                    $_cek = $this->model_generic->_cek($param_wishlist);
                    if ($_cek) {
                        $this->db->where('user_id', $this->_user_id);
                        $this->db->where('item_id', $param_wishlist['item_id']);
                        $this->model_generic->_del($param_wishlist);

                        $this->db->where('user_id', $this->_user_id);
                        $wishlist_badge        = $this->model_generic->_count($param_wishlist);
                        $res['wishlist_badge'] = $wishlist_badge;

                        header('Content-Type: application/json');
                        echo json_encode($res, JSON_PRETTY_PRINT);

                    }
                }
            }
        }
    }
    /*****************************************************************************/
    public function wishlist_clear()
    {
        if ($this->_is_login) {
            if ($this->input->is_ajax_request()) {
                if ($this->input->post()) {
                    $param_wishlist['table'] = 'wishlist';
                    $this->db->where('user_id', $this->_user_id);
                    $this->model_generic->_del($param_wishlist);
                }
            }
        }
    }
    /*****************************************************************************/
    public function wishlist()
    {
        if ($this->_is_login) {
            $param_wishlist['table'] = 'wishlist';
            $this->db->where('wishlist.user_id', $this->_user_id);
            $this->db->join('item', 'item.item_id = wishlist.item_id', 'left');
            $wishlist_all = $this->model_generic->_get($param_wishlist);
            foreach ($wishlist_all as $value) {

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
            // opn($wishlist_all);exit();
            $this->data['wishlist_full']  = $wishlist_all ? '' : 'none';
            $this->data['wishlist_empty'] = $wishlist_all ? 'none' : '';

            $this->data['wishlist_all'] = $wishlist_all;
            $this->data['body']         = $this->parser->parse('_wishlist.html', $this->data, true);
            $this->parser->parse('welcome/_index.html', $this->data, false);
        } else {
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    public function ticket()
    {
        $this->data['body'] = $this->parser->parse('_ticket.html', $this->data, true);

        $this->parser->parse('welcome/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Account.php */
/* Location: ./application/controllers/Account.php */
