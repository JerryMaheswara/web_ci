<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Member extends MY_Controller
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
        $this->load->model('model_member');
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
        // $param_member['table'] = 'member';
        // $total_rows            = $this->model_generic->_count($param_member);
        // $_cek_member           = $this->model_generic->_cek($param_member);
        // $config_base_url       = base . '/' . controller;
        // $this->data['search']  = '';

        // if ($this->input->get('order') && $this->input->get('sort')) {
        //     $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        // }

        // if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
        //     $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
        //     $this->data['search'] = $this->input->get('search');
        //     $this->db->or_like('user.user_name', $this->input->get('search'));
        // }

        // if (!$this->_is_allowed(array(role_admin))) {
        //     $this->db->where('member_disabled', 0);
        // }

        // $member_all = $this->model_member->_get_member($this->_limit, $this->_offset);
        // // opn(lq());
        // // opn($member_all);
        // // exit();
        // // $member_all = $this->model_generic->_get($param_member, $this->_limit, $this->_offset);
        // foreach ($member_all as $key => $value) {
        //     $value->member_avatar = file_exists(BASE . '/files/images/' . controller . '/' . $value->member_avatar . '.png') ? $value->member_avatar : 'default';
        // }
        // $this->data['member_all'] = $member_all;

        // $this->load->library('pagination');
        // $config['base_url']             = $config_base_url;
        // $config['total_rows']           = $total_rows;
        // $config['query_string_segment'] = 'offset';
        // $config['per_page']             = $this->_limit;

        // $this->pagination->initialize($config);
        // $this->data['paging']     = $this->pagination->create_links();
        // $this->data['total_rows'] = $total_rows;
        // // opn($this->data['paging'] );exit();

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
            $param_member['table'] = 'member';
            $total_rows            = $this->model_generic->_count($param_member);
            $_cek_member           = $this->model_generic->_cek($param_member);
            $config_base_url       = base . '/' . controller;
            $this->data['search']  = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('user.user_name', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_member);

                $this->db->or_like('user.user_name', $this->input->get('search'));

            }
            $member_all = $this->model_generic->_get($param_member, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($member_all);exit();
            $this->data['member_all'] = $member_all;
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
                    $member_id = $arg[0];
                } else {
                    $member_id = $this->_user_id;
                    redirect(base . '/' . controller . '/profile');
                }
            } else {
                $member_id = $this->_user_id;
                if ($this->_is_allowed(array(role_admin))) {
                    redirect(base . '/' . controller);
                }
            }
            $this->data['member_id_before'] = $member_id;

            $param_member['table'] = 'user';
            $this->db->where('user.user_id', $member_id);
            // $this->db->join('user', 'user.user_id = member.user_id', 'left');
            $this->db->join('kodepos', 'kodepos.kodepos_id = member.kodepos_id', 'left');
            $member_detail = $this->model_generic->_get($param_member, $this->_limit, $this->_offset);
            foreach ($member_detail as $value) {
                $value->member_avatar = file_exists('./files/images/' . controller . '/' . $value->member_avatar . '.png') ? controller.'/'.$value->member_avatar . '.png': 'default.png';
                $value->member_created_format = date('F d, Y', strtotime($value->{'member_created'}));
            }
            $this->data['member_detail'] = $member_detail;
            // opn($member_detail);exit();

            $this->data['body']            = $this->parser->parse('_profile.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        } else {
            redirect(base . '/' . controller . '/login');
        }
    }
    /*****************************************************************************/
    public function add()
    {
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->post()) {
                $param_member = $this->input->post();
                opn($param_member);exit();

                $param_user['table']           = 'user';
                $param_user['user_password']   = $this->passwordHasher->HashPassword('123456');
                $param_user['user_given_name'] = $param_member['member_name'];
                $param_user['user_slug']       = slug($param_member['member_name'], ''); //. ' ' . $param['user_family_name'], '_');
                $param_user['user_birthday']   = $param_member['member_lahir'];
                $param_user['user_gender']     = $param_member['member_gender'];

                // $ugn = strtolower(substr($param_user['user_slug'], 0, 8));
                // // $ufn = strtoupper(substr($param['user_family_name'], 0, 2));
                // $ubd = substr($param_user['user_birthday'], -5);
                // $ubd = explode('-', $ubd);
                // $ubd = $ubd[1] . $ubd[0];

                $param_user['user_name'] = $param_member['member_number']; // $ugn . $ubd;

                $this->model_generic->_insert($param_user);
                $user_id = $this->db->insert_id();

                $param_user_role['table'] = 'user_role';
                $this->db->where('user_id', $user_id);
                $this->model_generic->_del($param_user_role);

                $param_user_role['user_id']       = $user_id;
                $param_user_role['role_label_id'] = 1;
                $this->model_generic->_insert($param_user_role);

                $param_member['user_id'] = $user_id;
                $param_member['table']   = 'member';
                $this->model_generic->_insert($param_member);

                // $param_user['table'] = 'user';

                // $param_user['user_password']   = $this->passwordHasher->HashPassword('123456');
                // $param_user['user_given_name'] = $param_member['member_name'];
                // $param_user['user_birthday']   = $param_member['member_lahir'];
                // $param_user['user_gender']     = $param_member['member_gender'];
                // $param_user['user_slug']       = slug($param['user_given_name'], '_'); //. ' ' . $param['user_family_name'], '_');
                // $this->model_generic->_insert($param_user);

                $param_member['user_id'] = $user_id;
                $param_member['table']   = 'member';
                $this->model_generic->_insert($param_member);
                $this->_goto_referer();
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
            $member_id = $this->_user_id;
        }

        // $this->_set_referer();
        $arg = func_get_args();
        if (isset($arg[0]) && $this->_is_allowed(array(role_admin))) {
            $member_id = $arg[0];
        }
        if ($this->input->post()) {
            $param_member = $this->input->post();
            // opn($param_member);exit();
            $member_avatar_before = $param_member['member_avatar_before'];
            unset($param_member['member_avatar_before']);

            if ($_FILES['member_avatar_file']['error'] == 0) {
                // opn($_FILES['member_avatar']);exit();
                $upload_path = './files/images/member';
                $file_name   = md5(time() . uniqid());

                if (!is_dir($upload_path)) {
                    mkdir($upload_path);
                }
                chmod($upload_path, 0777);
                $config['upload_path']   = './files/images/member';
                $config['allowed_types'] = 'png';
                // $config['max_size']         = 1024;
                $config['max_width']        = 512;
                $config['max_height']       = 512;
                $config['file_name']        = $file_name;
                $config['overwrite']        = true;
                $config['file_ext_tolower'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('member_avatar_file')) {
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
                if (file_exists('./files/images/' . controller . '/' . $member_avatar_before . '.png') && $member_avatar_before != 'default') {
                    unlink('./files/images/' . controller . '/' . $member_avatar_before . '.png');
                }
                $param_member['member_avatar'] = $file_name;
                if ($member_id == $this->_user_id) {
                    $_SESSION['user_info'][0]->user_avatar = file_exists('./files/images/' . controller . '/' . $file_name . '.png') ? controller.'/' . $file_name . '.png' : 'default.png';
                }

            }

            $param_member['table'] = 'member';
            $this->db->where('member_id', $member_id);
            $_cek_member = $this->model_generic->_cek($param_member);
            if ($_cek_member) {
                $this->db->where('member_id', $member_id);
                // $param_member['member_avatar'] = $param_member['member_number'];

                $this->model_generic->_update($param_member);
                $this->db->where('member_id', $member_id);
                $member = $this->model_generic->_get($param_member);
                // opn($_SESSION['user_info']);exit();
                redirect(base . '/' . controller . '/profile/' . $member_id);
            }
        } else {

            $param_member['table'] = 'member';
            $this->db->where('member_id', $member_id);
            $member_detail = $this->model_generic->_get($param_member);
            // opn($_SESSION['user_info']);exit();
            foreach ($member_detail as $key => $value) {
                $value->remove_button_display = file_exists('./files/images/' . controller . '/' . $value->member_avatar . '.png') ? '' : 'hidden destroy';
                $value->member_avatar         = file_exists('./files/images/' . controller . '/' . $value->member_avatar . '.png') ? controller . '/' . $value->member_avatar . '.png' : 'default.png';
            }
            $this->data['member_detail'] = $member_detail;
            // opn($member_detail);exit();

            $this->data['body'] = $this->parser->parse('_edit.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function delete()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $member_id = $arg[0];
            if ($this->_is_allowed(array(role_admin))) {
                $param_member['table'] = 'member';

                $this->db->where('member_id', $member_id);
                $_cek = $this->model_generic->_cek($param_member);
                if ($_cek) {

                    $this->db->where('member_id', $member_id);
                    $member = $this->model_generic->_get($param_member);
                    foreach ($member as $key => $value) {
                        $param_user['table'] = 'user';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user);

                        $param_user_role['table'] = 'user_role';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user_role);
                    }
                    $this->db->where('member_id', $member_id);
                    $this->model_generic->_del($param_member);
                }

                // redirect(base . '/' . controller);
                $this->_goto_referer();
            }
        }
    }
    /*****************************************************************************/
    public function member_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_member['table'] = 'user_role';
        $this->db->where('role_label_id', role_member);
        $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
        $total_rows            = $this->model_generic->_count($param_member);

        if ($this->input->get('view') && !empty($this->input->get('view'))) {
            $config_base_url = base . '/' . controller . '?view=' . $this->input->get('view');
        } else {
            $config_base_url = base . '/' . controller . '/admin';
        }

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('user_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
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
            $this->db->or_like('user.user_name', $this->input->get('search'));
            $this->db->or_like('user.user_number', $this->input->get('search'));
            $this->db->group_end();

            $this->db->where('role_label_id', role_member);
            $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
            $total_rows = $this->model_generic->_count($param_member);
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('user_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('user_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('user.user_name', $this->input->get('search'));
            $this->db->or_like('user.user_number', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {
            $this->db->where('user_status', $this->input->get('status'));
            $total_rows = $this->model_generic->_count($param_member);
            $this->db->where('user_status', $this->input->get('status'));
        }

        if ($this->input->get('order') && $this->input->get('sort')) {
            $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        }

        // $this->db->where('role_label_id', role_member);
        // $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
        $member_all = $this->model_member->_get_member($this->_limit, $this->_offset);
        foreach ($member_all as $key => $value) {
            $value->toggle_btn    = ($value->user_disabled) ? 'default' : 'success';
            $value->toggle_icon   = ($value->user_disabled) ? 'toggle-off' : 'toggle-on';
            $value->user_avatar = file_exists('./files/images/' . controller . '/' . $value->user_avatar . '.png') ? controller.'/'.$value->user_avatar . '.png': 'default.png';
            $value->user_created_format = date('F d, Y', strtotime($value->{'user_created'}));
        }
        $this->data['user_all'] = $member_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($member_all); exit();

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
    public function member_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'member';
            $this->db->where('member_id', $param['member_id']);
            $this->db->where('member_disabled != ' . $param['member_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('member_id', $param['member_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['member_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['member_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['member_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['member_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

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

            if ($_FILES['file_member']) {
                $fileName = time() . $_FILES['file_member']['name'];
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

                if (!$this->upload->do_upload('file_member')) {
                    $this->upload->display_errors();
                }

                $file_member   = $this->upload->data();
                $inputFileName = './files/excel_upload/' . $file_member['file_name'];
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
                    $param_member['member_number']          = $rowData[0][0];
                    $param_member['member_name']            = $rowData[0][1];
                    $param_member['member_lahir']           = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][2], 'YYYY-MM-DD');
                    $param_member['member_category']        = $rowData[0][3];
                    $param_member['member_avatar']          = $rowData[0][4];
                    $param_member['member_gender']   = $rowData[0][5];
                    $param_member['member_status']          = $rowData[0][6];
                    $param_member['member_couple_number'] = $rowData[0][7];
                    $param_member['member_couple_name']   = $rowData[0][8];
                    $param_member['member_anniversary']     = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][9], 'YYYY-MM-DD');
                    $param_member['member_expired']         = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][10], 'YYYY-MM-DD');
                    // opn($rowData);exit();

                    $param_member['table'] = 'member';
                    // opn($param_member); //exit();
                    $this->db->where('member_number', $param_member['member_number']);
                    $cek = $this->model_generic->_cek($param_member);
                    if (!$cek) {

                        $param_user['table']           = 'user';
                        $param_user['user_password']   = $this->passwordHasher->HashPassword('123456');
                        $param_user['user_given_name'] = $param_member['member_name'];
                        $param_user['user_slug']       = slug($param_member['member_name'], ''); //. ' ' . $param['user_family_name'], '_');
                        $param_user['user_birthday']   = $param_member['member_lahir'];
                        $param_user['user_gender']     = $param_member['member_gender'];

                        $ugn = strtolower(substr($param_user['user_slug'], 0, 8));
                        // $ufn = strtoupper(substr($param['user_family_name'], 0, 2));
                        $ubd = substr($param_user['user_birthday'], -5);
                        $ubd = explode('-', $ubd);
                        $ubd = $ubd[1] . $ubd[0];

                        $param_user['user_name'] = $param_member['member_number']; // $ugn . $ubd;

                        $this->model_generic->_insert($param_user);
                        $user_id = $this->db->insert_id();

                        $param_user_role['table'] = 'user_role';
                        $this->db->where('user_id', $user_id);
                        $this->model_generic->_del($param_user_role);

                        $param_user_role['user_id']       = $user_id;
                        $param_user_role['role_label_id'] = 1;
                        $this->model_generic->_insert($param_user_role);

                        $param_member['user_id'] = $user_id;
                        $param_member['table']   = 'member';
                        $this->model_generic->_insert($param_member);
                    } else {

                        $this->db->where('member_number', $param_member['member_number']);
                        $cek = $this->model_generic->_update($param_member);
                    }
                }
                delete_files($file_member['file_path']);
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

            $param_member['table'] = 'user';
            $this->db->where('user_email', $param['user_email']);
            $user_info        = $this->model_generic->_get($param_user);
            $passwordMatch = false;
            // opn($passwordMatch); //exit();
            $user_role = array();
            foreach ($user_info as $value) {
                $passwordMatch           = $this->passwordHasher->CheckPassword($param['user_password'], $value->user_password);
                $value->user_avatar      = file_exists('./files/images/user/' . $value->user_avatar . '.png') ? 'user/' . $value->user_avatar . '.png' : 'default.png';

                $param_user_role['table'] = 'user_role';
                $this->db->where('user_id', $value->user_id);
                $role = $this->model_generic->get($param_user_role);
                foreach ($role as $ro_value) {
                    $r[] = $ro_value->role_label_id;
                }
                $user_role        = $r;
            }
            // opn($passwordMatch); //exit();
            if ($passwordMatch && array_intersect(array(role_member), $user_role)) {
                $_SESSION['user_info'] = $user_info;
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
            $param_member = $this->input->post();
            unset($param_member['user_password_confirm']);
            unset($param_member['agree']);
            $param_member['user_verification_code'] = md5(base64_encode(md5($param_member['user_email'])));
            $param_member['user_password']          = $this->passwordHasher->HashPassword($param_member['user_password']);
            $param_member['user_slug']              = slug($param_member['user_name_first'], '_');
            // opn($param_member);exit();
            $param_member['table'] = 'user';
            $this->model_generic->_insert($param_member);

            $user_id = $this->db->insert_id();

            $param_user_role['user_id']       = $user_id;
            $param_user_role['table']         = 'user_role';
            $param_user_role['role_label_id'] = role_member;
            $this->model_generic->_insert($param_user_role);

            redirect(base . '/login');
        } else {
            $this->parser->parse('_register.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function check_email()
    {
        if ($this->input->post('member_email')) {
            $param_member['table'] = 'member';
            $this->db->where('member_email', $this->input->post('member_email'));
            $total_rows = $this->model_generic->_count($param_member);
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
            $member_id             = $arg[0];
            $param_member['table'] = 'member';
            $this->db->where('member_id', $member_id);
            $member = $this->model_generic->_get($param_member);
            foreach ($member as $value) {
                if (file_exists('./files/images/' . controller . '/' . $value->member_avatar . '.png')) {
                    unlink('./files/images/' . controller . '/' . $value->member_avatar . '.png');
                }
                $res['status']  = 1;
                $res['message'] = 'Avatar deleted.';
                echo json_encode($res);

            }
        }

    }
    /*****************************************************************************/
}

/* End of file Member.php */
/* Location: ./application/controllers/Member.php */
