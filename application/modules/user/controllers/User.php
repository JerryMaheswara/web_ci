<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends MY_Controller
{
    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
        $this->data['content_header']     = __CLASS__;
        $this->data['content_sub_header'] = __CLASS__;
        $this->data['treeview_menu']      = controller;
        $this->load->model('model_captcha');

        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);
        $this->data['body']        = $this->parser->parse('dashboard/_content_wrapper.html', $this->data, true);

        $this->data['role_label_id'] = '';
        $this->data['search']        = '';

        $_module_id               = $this->library_module->_module_id(strtolower(controller));
        $this->data['breadcrumb'] = $this->library_module->_module_breadcrumb($_module_id);
        $this->passwordHasher     = new Hautelook\Phpass\PasswordHash(64, false);

    }
    /*****************************************************************************/
    public function index()
    {
        if ($this->_is_allowed(array(role_admin))) {
            redirect(base . '/user/admin');
        } else {
            redirect(base . '/user/profile');
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function level()
    {
        $_is_allowed = $this->_is_allowed(array(role_admin));
        if ($_is_allowed) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $role_label_id               = $arg[0];
                $this->data['role_label_id'] = $role_label_id;
                $param['table']              = 'user_role';
                $this->db->where('user_role.role_label_id', $role_label_id);
                $total_rows      = $this->model_generic->_count($param);
                $config_base_url = base . '/user/level/' . $role_label_id;

                if ($this->input->get('order') && $this->input->get('sort')) {
                    $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
                }

                $this->db->join('role_label', 'role_label.role_label_id = user_role.role_label_id', 'left');
                $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
                $this->db->join('country', 'country.country_id = user.country_id', 'left');
                $this->db->where('user_role.role_label_id', $role_label_id);
                $user_all = $this->model_generic->_get($param, $this->_limit, $this->_offset);

                $nomor = 1;
                foreach ($user_all as $key => $value) {
                    $value->nomor_user  = $nomor + $this->_offset++;
                    $value->level       = $value->role_label_name;
                    $value->user_id_nya = $value->user_id;

                    $param_role['table'] = 'user_role';
                    $this->db->where('user_role.user_id', $value->user_id);
                    $this->db->join('role_label', 'role_label.role_label_id = user_role.role_label_id', 'left');
                    // $this->db->where('user_role.role_label_id', $role_label_id);
                    $user_role       = $this->model_generic->_get($param_role);
                    $role_label_id   = array();
                    $role_label_name = array();
                    foreach ($user_role as $ur_key => $ur_value) {
                        $role_label_id[]   = $ur_value->role_label_id;
                        $role_label_name[] = $ur_value->role_label_name;
                    }
                    $value->level     = implode(', ', $role_label_name);
                    $value->user_role = $user_role;
                    // $value->role_label_id    = json_encode($role_label_id);
                    $value->user_role_before = json_encode($role_label_id);
                    // $value->role_label_name  = $role_label_name;
                }
                // opn($user_all);exit();

                $this->load->library('pagination');
                $config['base_url']             = $config_base_url; //base . '/user/admin?search=' . $this->input->get('search');
                $config['total_rows']           = $total_rows;
                $config['query_string_segment'] = 'offset';
                $config['per_page']             = $this->_limit;
                $this->pagination->initialize($config);
                $this->data['paging'] = $this->pagination->create_links();

                $this->data['user_all'] = $user_all;
                $this->data['content']  = $this->parser->parse('user_admin.html', $this->data, true);
                $this->parser->parse('dashboard/_index.html', $this->data, false);

            }
        } else {
            redirect(base);
        }
    }
    /*****************************************************************************/
    public function adduser()
    {

        $this->_is_allowed(array(role_admin));
        if ($this->input->post()) {
            $param = $this->input->post();
            // opn($param);exit();
            $this->model_captcha->_delete_captcha_code($param['captcha']);
            unset($param['confirm_password']);
            unset($param['captcha']);
            $param['table']                  = 'user';
            $param['user_verification_code'] = md5(base64_encode(md5($param['user_email'])));
            $param['user_password']          = $this->passwordHasher->HashPassword($param['user_password']);
            $param['user_slug']              = slug($param['user_given_name'] . ' ' . $param['user_family_name'], '_');
            $param['user_name']              = $param['user_slug'];

            $role_label_id = isset($param['role_label_id']) ? $param['role_label_id'] : 21;
            unset($param['role_label_id']);
            $this->model_generic->_insert($param);
            $user_id = $this->db->insert_id();

            $param_user_role['user_id']       = $user_id;
            $param_user_role['table']         = 'user_role';
            $param_user_role['role_label_id'] = $role_label_id;
            $this->model_generic->_insert($param_user_role);
            // $user_id = $this->db->insert_id();
            echo $user_id;
            // echo 'Register Sussessful...';

        } else {

            $expiration = time() - 7200;
            $this->model_captcha->_delete_captcha($expiration);
            $this->data['captcha'][] = $this->model_captcha->_create_captcha();

            $this->data['body'] = $this->parser->parse('adduser.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/

    public function admin()
    {
        $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            $this->data['treeview_menu'] = 'user';
            $param['table']              = 'user';
            $total_rows                  = $this->model_generic->_count($param);
            $config_base_url             = base . '/user/admin';
            // opn($this->_limit);exit();
            // $offset = 0;
            // if ($this->input->get('offset')) {
            //     $offset = $this->input->get('offset');
            // }
            if ($this->input->get('order') && $this->input->get('sort')) {
                $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
            }

            if ($this->input->get('search')) {
                $this->data['search'] = $this->input->get('search');
                $config_base_url      = base . '/user/admin?search=' . $this->input->get('search');

                $this->db->like('user.user_given_name', $this->input->get('search'));
                $this->db->or_like('user.user_family_name', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param);

                $this->db->like('user.user_given_name', $this->input->get('search'));
                $this->db->or_like('user.user_family_name', $this->input->get('search'));
                // $this->db->join('country', 'country.country_id = user.country_id', 'left');
                // $user_all = $this->model_generic->_get($param, $this->_limit, $this->_offset);
            }

            $param['table'] = 'user_role';

            $this->db->join('role_label', 'role_label.role_label_id = user_role.role_label_id', 'left');
            $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
            $this->db->join('country', 'country.country_id = user.country_id', 'left');
            // $this->db->where('user_role.role_label_id', $role_label_id);
            $this->db->group_by('user_role.user_id');
            $user_all = $this->model_generic->_get($param, $this->_limit, $this->_offset);

            // opn($user_all);exit();
            // $param['table'] = 'user';
            // $this->db->select('*, user_id as user_idnya');
            // $this->db->join('country', 'country.country_id = user.country_id', 'left');
            // $user_all             = $this->model_generic->_get($param, $this->_limit, $this->_offset);

            $nomor = 1;
            foreach ($user_all as $key => $value) {
                $value->nomor_user  = $nomor + $this->_offset++;
                $value->user_id_nya = $value->user_id;

                $param_role['table'] = 'user_role';
                $this->db->where('user_role.user_id', $value->user_id);
                $this->db->join('role_label', 'role_label.role_label_id = user_role.role_label_id', 'left');
                $user_role       = $this->model_generic->_get($param_role);
                $role_label_id   = array();
                $role_label_name = array();
                foreach ($user_role as $ur_key => $ur_value) {
                    $role_label_id[]   = $ur_value->role_label_id;
                    $role_label_name[] = $ur_value->role_label_name;
                }
                $value->level     = implode(', ', $role_label_name);
                $value->user_role = $user_role;
                // $value->role_label_id    = json_encode($role_label_id);
                $value->user_role_before = json_encode($role_label_id);
                // $value->role_label_name  = $role_label_name;
                // $param_bank_account['table'] = 'bank_account';
                // $this->db->where('user_id', $value->user_id);
                // $value->total_receiver = $this->model_generic->_count($param_bank_account);
                // $value->user_id_nya = $value->user_id;
                
                // $value->session_id  = $value->id;
                // $this->db->join('ci_session', 'ci_session.user_id = user.user_id', 'left');
                // $value->is_logged_in = ($value->session_id) ? '' : 'hidden destroy';
                $param_role['table'] = 'ci_session';
                $this->db->where('ci_session.user_id', $value->user_id);
                $ci_session = $this->model_generic->_get($param_role,1);
                $value->ip_address = '';
                if ($ci_session) {
                    foreach ($ci_session as $cs_key => $cs_value) {
                        $value->ip_address = $cs_value->ip_address;
                    }
                }
                $value->is_logged_in = ($ci_session) ? '' : 'hidden destroy';


            }
            $this->data['user_all'] = $user_all;
            // opn($user_all);exit();

            $this->load->library('pagination');
            $config['base_url']             = $config_base_url; //base . '/user/admin?search=' . $this->input->get('search');
            $config['total_rows']           = $total_rows;
            $config['query_string_segment'] = 'offset';
            $config['per_page']             = $this->_limit;
            $this->pagination->initialize($config);
            $this->data['paging'] = $this->pagination->create_links();

            // $this->data['content'] = $this->parser->parse('user/admin.html', $this->data, true);
            $this->data['body'] = $this->parser->parse('user_admin.html', $this->data, true);
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
    public function change_password()
    {
        if ($this->_is_allowed(array(role_admin, role_user))) {
            if ($this->input->post()) {
                $param = $this->input->post();
                unset($param['user_confirm_password']);
                $param['user_password'] = $this->passwordHasher->HashPassword($param['user_password']);
                $param['table']         = 'user';
                // opn($param);exit();
                $this->db->where('user_id', $param['user_id']);
                $cek = $this->model_generic->_cek($param);
                if ($cek) {
                    // $this->db->where('user_id', $param['user_id']);
                    if ($this->_is_allowed(array(role_user)) && $param['user_id'] == $this->_user_id) {
                        $this->db->where('user_id', $this->_user_id);
                        $this->model_generic->_update($param);
                    } else {
                        if ($this->_is_allowed(array(role_admin))) {
                            $this->db->where('user_id', $param['user_id']);
                            $this->model_generic->_update($param);
                        }
                    }
                }
            }
        }
    }
    /*****************************************************************************/
    public function user_role_action()
    {
        if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->post()) {
                $param                 = $this->input->post();
                $user_role             = isset($param['user_role']) ? $param['user_role'] : array();
                $param_role['user_id'] = $param['user_id'];
                $param_role['table']   = 'user_role';
                $this->db->where('user_id', $param_role['user_id']);
                $this->model_generic->_del($param_role);
                foreach ($user_role as $key => $value) {
                    $param_role['role_label_id'] = $value;
                    $this->model_generic->_insert($param_role);
                }
                // opn($param_role);exit();
                redirect(base . '/user/admin');
                echo 'Add/update succesfull...';
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function action()
    {
        if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->post()) {
                $param  = $this->input->post();
                $action = $param['action'];
                unset($param['action']);
                $user_role = isset($param['user_role']) ? $param['user_role'] : array();
                unset($param['user_role']);
                // opn($user_role);exit();
                // $user_role_type_id = $param['user_role_type_id'];
                switch ($action) {
                    case 'add':
                    case 'edit':
                    case 'update':
                        $param['table']     = 'user';
                        $param['user_name'] = slug($param['user_display_name'], '_');
                        $param['user_slug'] = slug($param['user_display_name'], '_');
                        // $this->db->where('user_role_type_id', $user_role_type_id);
                        $this->model_generic->_set($param);
                        $param_role['user_id'] = !empty($param['user_id']) ? $param['user_id'] : $this->db->insert_id();
                        $param_role['table']   = 'user_role';
                        $this->db->where('user_id', $param_role['user_id']);
                        $this->model_generic->_del($param_role);
                        foreach ($user_role as $key => $value) {
                            $param_role['role_label_id'] = $value;
                            $this->model_generic->_set($param_role);
                        }
                        echo 'Add/update succesfull...';
                        break;

                    case 'delete':
                        // resctict to delete user number 1
                        if ($param['user_id'] != 1) {
                            // delete role dulu
                            $param_role['table'] = 'user_role';
                            $this->db->where('user_id', $param['user_id']);
                            $this->model_generic->_del($param_role);
                            // baru delete user
                            $param['table'] = 'user';
                            $this->db->where('user_id', $param['user_id']);
                            $this->model_generic->_del($param);
                            echo 'Delete succesfull...';
                        } else {
                            echo 'No...';
                        }
                        break;
                }
            }
        }
    }
    /*****************************************************************************/
    public function edit()
    {
        // $this->_is_allowed(array(role_admin));

        $this->data['treeview_menu'] = 'user';

        if ($this->_is_login) {
            $arg = func_get_args();
            if ($this->_is_allowed(array(role_admin))) {
                $user_id = isset($arg[0]) ? $arg[0] : $this->_user_id;
            } else {
                $user_id = $this->_user_id;
                if (isset($arg[0]) && $arg[0] != $this->_user_id) {
                    redirect(base . '/user/edit');
                }
            }

            if ($this->input->post()) {
                $param = $this->input->post();
                // $param['user_slug'] = slug($param['user_given_name'] . ' ' . $param['user_family_name'], '_');
                $param['user_slug'] = slug($param['user_given_name'] . ' ' . $param['user_family_name'], '');

                $param['table'] = 'user';
                $this->db->where('user_id', $user_id);
                $cek = $this->model_generic->_cek($param);
                if ($cek) {

                    $ugn = strtoupper(substr($param['user_slug'], 0, 8));
                    // $ugn                = strtoupper(substr($param['user_given_name'], 0, 2));
                    // $ufn                = strtoupper(substr($param['user_family_name'], 0, 2));
                    $ubd = substr($param['user_birthday'], -5);
                    $ubd = explode('-', $ubd);
                    $ubd = $ubd[1] . $ubd[0];
                    // $param['user_name'] = $ugn . $ubd;

                    $this->db->where('user_id', $user_id);
                    $this->model_generic->_update($param);

                    if ($user_id == $this->_user_id) {

                        $param_image['table'] = 'image';
                        $this->db->where('entity_table', 'user');
                        $this->db->where('entity_id', $user_id);
                        $image = $this->model_generic->_get($param_image);
                        if ($image) {
                            $_SESSION['user_info'][0]->user_avatar = $image[0]->image_path . '/' . $image[0]->image_filename . '.' . $image[0]->image_ext;
                        } else {
                            $_SESSION['user_info'][0]->user_avatar = 'default.png';
                        }

                    }

                    // $role_label_id = $param['role_label_id'];
                    // unset($param['role_label_id']);
                    // $branch_id = $param['branch_id'];
                    // unset($param['branch_id']);
                    // $this->model_generic->_insert($param);
                    // $user_id                          = $this->db->insert_id();

                    // $param_user_role['user_id']       = $user_id;
                    // $param_user_role['table']         = 'user_role';
                    // $param_user_role['role_label_id'] = $role_label_id;
                    // $this->db->where('user_id', $user_id);
                    // $this->db->where('role_label_id != 2');
                    // $this->model_generic->_del($param_user_role);
                    // $this->model_generic->_insert($param_user_role);

                    redirect(base . '/user/profile/' . $user_id);

                }
            } else {
                // $arg = func_get_args();
                // if (isset($arg[0])) {
                // $user_id        = $arg[0];
                $param['table'] = 'user';
                $this->db->where('user_id', $user_id);
                $user_detail = $this->model_generic->_get($param);
                foreach ($user_detail as $key => $value) {
                    $value->user_gender = ($value->user_gender == 'M' || $value->user_gender == 'L') ? 'M' : "F";
                }
                // opn($user_detail);exit();

                $this->load->library('image/library_image');

                $param_image['entity_table'] = 'user';
                $param_image['entity_id']    = $user_id;
                $this->data['image_upload']  = $this->library_image->add_image($param_image);

                $this->data['user_detail'] = $user_detail;
                $this->data['body']        = $this->parser->parse('user_edit.html', $this->data, true);
                $this->parser->parse('dashboard/_index.html', $this->data, false);
                // }
            }
        }
    }
    /*****************************************************************************/
    public function profile()
    {
        // opn($_SESSION['user_info']);exit();
        if ($this->_is_allowed(array(role_admin, role_user))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $user_id = $arg[0];
            } else {
                $user_id = $this->_user_id;
            }
            // opn($user_id);exit();
            $param_user['table'] = 'user';
            $this->db->join('country', 'country.country_id = user.country_id', 'left');
            $this->db->where('user_id', $user_id);
            $user_detail = $this->model_generic->_get($param_user);

            // foreach ($user_detail as $key => $value) {
            //     $value->user_avatar = 'default.png';
            // }

            $this->load->library('image/library_image');

            // $param_image['entity_table'] = 'user';
            // $param_image['entity_id']    = $user_id;
            // $this->library_image->refresh($param_image);

            $param_image['table'] = 'image';
            // $param_image['entity_table'] = 'user';
            // $param_image['entity_id'] = $user_id;
            $this->db->where('entity_table', 'user');
            $this->db->where('entity_id', $user_id);
            $image = $this->model_generic->_get($param_image);
            // opn($image);exit();
            foreach ($user_detail as $key => $value) {
                // $value->user_gender = ($value->user_gender == 1 || $value->user_gender == 'M')?'Male':'Female';
                if ($image) {
                    $value->user_avatar_nya = $image[0]->image_path . '/thumbnail/' . $image[0]->image_filename . '_128.' . $image[0]->image_ext;
                } else {
                    $value->user_avatar_nya = 'default.png';
                }
                $value->user_gender = ($value->user_gender == 1 || $value->user_gender == 'M' || $value->user_gender == 'L') ? 'Male' : "Female";

            }
            // opn($user_detail);exit();

            $this->data['user_detail'] = $user_detail;
            $this->data['body']        = $this->parser->parse('user_detail.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        } else {
            redirect(base . '/login');
        }

    }
    /*****************************************************************************/
    public function role_label_delete()
    {
        if ($this->_is_allowed(array(role_admin))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $role_label_id             = $arg[0];
                $param_role_label['table'] = $this->_db_ . '.role_label';
                $this->db->where('role_label_id', $role_label_id);
                $_cek_role_label = $this->model_generic->_cek($param_role_label);
                if ($_cek_role_label) {
                    if ($role_label_id != 1 && $role_label_id != 2) {
                        $this->db->where('role_label_id', $role_label_id);
                        $this->model_generic->_del($param_role_label);

                    }
                    redirect(base . '/user/role_label');
                }
            }
        }
    }
    /*****************************************************************************/
    public function role_label()
    {
        $this->data['treeview_menu'] = 'user';

        if ($this->_is_allowed(array(role_admin))) {

            // if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $param_role_label                    = $this->input->post();
                $param_role_label['table']           = $this->_db_ . '.role_label';
                $param_role_label['role_label_slug'] = slug($param_role_label['role_label_name'], '_');
                // opn($param_role_label);exit();
                if (isset($param_role_label['role_label_id']) && !empty($param_role_label['role_label_id'])) {
                    $this->db->where('role_label_id', $param_role_label['role_label_id']);
                    $_cek_role_label = $this->model_generic->_cek($param_role_label);
                    if ($_cek_role_label) {
                        if ($param_role_label['role_label_id'] != 1 && $param_role_label['role_label_id'] != 2) {
                            $this->db->where('role_label_id', $param_role_label['role_label_id']);
                            $this->model_generic->_update($param_role_label);
                        }
                    }
                } else {
                    $this->model_generic->_insert($param_role_label);
                    // echo 'Insert succesfull.';
                }
            }
            // }

            $param_role_label['table'] = $this->_db_ . '.role_label';
            $total_rows                = $this->model_generic->_count($param_role_label);
            $role_label                = $this->model_generic->_get($param_role_label);
            foreach ($role_label as $key => $value) {
                $value->role_label_id_nya = $value->role_label_id;
            }
            $this->data['total_rows']     = $total_rows;
            $this->data['role_label_all'] = $role_label;
            // opn($role_label);exit();

            $this->data['body'] = $this->parser->parse('role_label.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function user_ajax()
    {
        if ($this->_is_allowed(array(role_admin))) {
            // if ($this->input->is_ajax_request()) {
            $param['table'] = 'user';
            $user           = $this->model_generic->_get($param);
            foreach ($user as $key => $value) {
                $param_role['table'] = 'user_role';
                $this->db->where('user_role.user_id', $value->user_id);
                $this->db->join('role_label', 'role_label.role_label_id = user_role.role_label_id', 'left');
                $user_role       = $this->model_generic->_get($param_role);
                $role_label_id   = array();
                $role_label_name = array();
                foreach ($user_role as $ur_key => $ur_value) {
                    $role_label_id[]   = $ur_value->role_label_id;
                    $role_label_name[] = $ur_value->role_label_name;
                }
                $value->role_label_id    = $role_label_id;
                $value->user_role_before = $role_label_id;
                $value->role_label_name  = $role_label_name;
                $value->id               = $value->user_id;
                $value->text             = $value->user_given_name . ' ' . $value->user_family_name;
            }

            $user = json_encode($user, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $user;
            // }
        }
    }
    /*****************************************************************************/

    /*****************************************************************************/
    public function role_ajax()
    {
        if ($this->input->is_ajax_request()) {
            $param['table'] = 'role_label';
            $this->db->select('role_label_id as id, role_label_name as text');
            $role_label = $this->model_generic->_get($param);
            $role_label = json_encode($role_label, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $role_label;
        }
    }
    /*****************************************************************************/
    public function issuer_ajax()
    {
        if ($this->input->is_ajax_request()) {
            $param['table'] = 'user';
            $this->db->select('user_nik_issuer');
            $this->db->where('user_nik_issuer !=', '');
            $this->db->group_by('user_nik_issuer');
            $issuer = $this->model_generic->_get($param);
            foreach ($issuer as $key => $value) {
                if ($value->user_nik_issuer == 'Other') {
                    unset($issuer[$key]);
                }
                $value->id   = $value->user_nik_issuer;
                $value->text = $value->user_nik_issuer;
            }
            sort($issuer);
            $issuer = json_encode($issuer, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $issuer;

        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function ajax_email()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $param          = $this->input->post();
                $param['table'] = 'user';
                $this->db->where('user_email', $param['user_email']);
                $user = $this->model_generic->_get($param);
                // echo($x);exit();
                if ($user) {
                    $res['status']  = 1;
                    $res['message'] = 'Email already registered. Choose other email.';
                } else {
                    $res['status'] = 0;
                }
                echo json_encode($res);
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function ajax_captcha()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $expiration = time() - 7200;
                $ip_address = $this->input->ip_address();
                $word       = $this->input->post('captcha');
                $this->model_captcha->_delete_captcha($expiration);
                $x = $this->model_captcha->_get_captcha($word, $ip_address, $expiration);
                if ($x) {
                    $res['status'] = 1;
                } else {
                    $res['status']  = 0;
                    $res['message'] = 'Invalid Captcha code.';
                }
                echo json_encode($res);
            }
        }

    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function reload_captcha()
    {
        if ($this->input->is_ajax_request()) {
            $captcha    = $this->model_captcha->_create_captcha();
            $expiration = time() - 7200;
            $this->model_captcha->_delete_captcha($expiration);
            echo $captcha['image'];
        }
    }
    /*****************************************************************************/
    public function force_logout()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $user_id = $arg[0];
            $param_ci_session['table'] = 'ci_session';
            $this->db->where('user_id', $user_id);
            $total_rows = $this->model_generic->_del($param_ci_session); 
            
            $this->_goto_referer();
        }

    }
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file User.php */
/* Location: ./application/controllers/User.php */
