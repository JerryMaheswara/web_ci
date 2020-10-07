<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Anggota extends MY_Controller
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
        $this->load->model('model_anggota');
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
                redirect(base . '/anggota/profile');
            }
        }
        $param_anggota['table'] = 'anggota';
        $total_rows             = $this->model_generic->_count($param_anggota);
        $_cek_anggota           = $this->model_generic->_cek($param_anggota);
        $config_base_url        = base . '/' . controller;
        $this->data['search']   = '';

        if ($this->input->get('order') && $this->input->get('sort')) {
            $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        }

        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('anggota.anggota_nama', $this->input->get('search'));
        }

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('anggota_disabled', 0);
        }

        $anggota_all = $this->model_anggota->_get_anggota($this->_limit, $this->_offset);
        // opn(lq());
        // opn($anggota_all);
        // exit();
        // $anggota_all = $this->model_generic->_get($param_anggota, $this->_limit, $this->_offset);
        foreach ($anggota_all as $key => $value) {
            $value->anggota_foto = file_exists(BASE . '/files/images/foto_anggota/' . $value->anggota_foto . '.jpg') ? $value->anggota_foto : 'default';
        }
        $this->data['anggota_all'] = $anggota_all;

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
            $param_anggota['table'] = 'anggota';
            $total_rows             = $this->model_generic->_count($param_anggota);
            $_cek_anggota           = $this->model_generic->_cek($param_anggota);
            $config_base_url        = base . '/' . controller;
            $this->data['search']   = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('anggota.anggota_nama', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_anggota);

                $this->db->or_like('anggota.anggota_nama', $this->input->get('search'));

            }
            $anggota_all = $this->model_generic->_get($param_anggota, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($anggota_all);exit();
            $this->data['anggota_all'] = $anggota_all;
            $this->data['total_rows']  = $total_rows;

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

        if ($this->_is_allowed(array(role_admin, role_user))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                if ($this->_is_allowed(array(role_admin))) {
                    $user_id = $arg[0];
                } else {
                    $user_id = $this->_user_id;
                    redirect(base . '/anggota/profile');
                }
            } else {
                $user_id = $this->_user_id;
                if ($this->_is_allowed(array(role_admin))) {
                    redirect(base.'/'.controller);
                }
            }

            $param_anggota['table'] = 'anggota';
            $this->db->where('anggota.user_id', $user_id);
            // $this->db->join('user', 'user.user_id = anggota.user_id', 'left');
            $anggota  = $this->model_generic->_get($param_anggota, $this->_limit, $this->_offset);
            $_user_id = '';
            foreach ($anggota as $key => $value) {
                $_user_id                              = $value->user_id;
                $value->anggota_foto                   = file_exists(BASE . '/files/images/foto_anggota/' . $value->anggota_foto . '.jpg') ? $value->anggota_foto : 'default';
                $value->anggota_tanggal_expired_format = date('m/Y', strtotime($value->anggota_tanggal_expired));
            }
            $this->data['anggota_detail'] = $anggota;
            // opn($anggota);exit();

            $param_user['table'] = 'user';
            $this->db->where('user_id', $_user_id);
            $user          = $this->model_generic->_get($param_user);
            $passwordMatch = false;
            foreach ($user as $key => $value) {
                $passwordMatch = $this->passwordHasher->CheckPassword('123456', $value->user_password);
            }

            $this->data['force_change_password'] = ($passwordMatch && $_user_id == $this->_user_id) ? 'change' : 'no_change';
            $this->data['_is_mine']              = ($this->_user_id == $user_id) ? '' : 'hidden destroy';
            // opn($this->data['force_change_password']);exit();

            $this->parser->parse('_profile.html', $this->data, false);
        }else{
            redirect(base.'/login');
        }
    }
    /*****************************************************************************/
    public function add()
    {
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->post()) {
                $param_anggota = $this->input->post();
                opn($param_anggota);exit();

                $param_user['table']           = 'user';
                $param_user['user_password']   = $this->passwordHasher->HashPassword('123456');
                $param_user['user_given_name'] = $param_anggota['anggota_nama'];
                $param_user['user_slug']       = slug($param_anggota['anggota_nama'], ''); //. ' ' . $param['user_family_name'], '_');
                $param_user['user_birthday']   = $param_anggota['anggota_tanggal_lahir'];
                $param_user['user_gender']     = $param_anggota['anggota_jenis_kelamin'];

                // $ugn = strtolower(substr($param_user['user_slug'], 0, 8));
                // // $ufn = strtoupper(substr($param['user_family_name'], 0, 2));
                // $ubd = substr($param_user['user_birthday'], -5);
                // $ubd = explode('-', $ubd);
                // $ubd = $ubd[1] . $ubd[0];

                $param_user['user_name'] = $param_anggota['anggota_nomor']; // $ugn . $ubd;

                $this->model_generic->_insert($param_user);
                $user_id = $this->db->insert_id();

                $param_user_role['table'] = 'user_role';
                $this->db->where('user_id', $user_id);
                $this->model_generic->_del($param_user_role);

                $param_user_role['user_id']       = $user_id;
                $param_user_role['role_label_id'] = 1;
                $this->model_generic->_insert($param_user_role);

                $param_anggota['user_id'] = $user_id;
                $param_anggota['table']   = 'anggota';
                $this->model_generic->_insert($param_anggota);

                // $param_user['table'] = 'user';

                // $param_user['user_password']   = $this->passwordHasher->HashPassword('123456');
                // $param_user['user_given_name'] = $param_anggota['anggota_nama'];
                // $param_user['user_birthday']   = $param_anggota['anggota_tanggal_lahir'];
                // $param_user['user_gender']     = $param_anggota['anggota_jenis_kelamin'];
                // $param_user['user_slug']       = slug($param['user_given_name'], '_'); //. ' ' . $param['user_family_name'], '_');
                // $this->model_generic->_insert($param_user);

                $param_anggota['user_id'] = $user_id;
                $param_anggota['table']   = 'anggota';
                $this->model_generic->_insert($param_anggota);
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
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $anggota_id = $arg[0];
                if ($this->input->post()) {
                    $param_anggota = $this->input->post();
                    // opn($param_anggota);exit();

                    if ($_FILES['anggota_foto']) {
                        // opn($_FILES);exit();

                        $config['upload_path']      = './files/images/foto_anggota';
                        $config['allowed_types']    = 'jpg';
                        $config['max_size']         = 1024;
                        $config['max_width']        = 512;
                        $config['max_height']       = 512;
                        $config['file_name']        = $param_anggota['anggota_nomor'];
                        $config['overwrite']        = true;
                        $config['file_ext_tolower'] = true;

                        $this->load->library('upload', $config);

                        if (!$this->upload->do_upload('anggota_foto')) {
                            $error['message']       = strip_tags($this->upload->display_errors());
                            $error['allowed_types'] = $config['allowed_types'];
                            $error['max_size']      = $config['max_size'] . ' KB ';
                            $error['max_width']     = $config['max_width'] . ' pixels';
                            $error['max_height']    = $config['max_height'] . ' pixels';
                            opn($error);
                            echo '<center><br><h2><a href="javascript:history.back()"
                            style="text-decoration:none; border:6px double #ccc;padding:10px 20px;
                            border-radius:7px; background-color:#eee; color:#000;
                            "> &lArr; Back</a></center>';
                            exit();
                        }

                    }

                    $param_anggota['table'] = 'anggota';
                    // $param_anggota['user_id'] = $this->_user_id;
                    $this->db->where('anggota_id', $anggota_id);
                    $_cek_anggota = $this->model_generic->_cek($param_anggota);
                    if ($_cek_anggota) {
                        $this->db->where('anggota_id', $anggota_id);
                        $param_anggota['anggota_foto'] = $param_anggota['anggota_nomor'];
                        $this->model_generic->_update($param_anggota);

                        $this->db->where('anggota_id', $anggota_id);
                        $anggota = $this->model_generic->_get($param_anggota);
                        // $this->_goto_referer();
                        redirect(base . '/anggota/profile/' . $anggota[0]->user_id);
                    }
                    // opn($param);exit();
                } else {

                    $param_anggota['table'] = 'anggota';
                    $this->db->where('anggota_id', $anggota_id);
                    $anggota_detail = $this->model_generic->_get($param_anggota);
                    foreach ($anggota_detail as $key => $value) {
                        $value->anggota_foto = file_exists('./files/images/foto_anggota/' . $value->anggota_foto . '.jpg') ? $value->anggota_foto : 'default';
                    }
                    $this->data['anggota_detail'] = $anggota_detail;

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
            $anggota_id             = $arg[0];
            $param_anggota['table'] = 'anggota';
            $this->db->where('anggota_id', $anggota_id);
            $anggota_detail = $this->model_generic->_get($param_anggota);
            foreach ($anggota_detail as $key => $value) {
                $value->anggota_foto                   = file_exists('./files/images/foto_anggota/' . $value->anggota_foto . '.jpg') ? $value->anggota_foto : 'default';
                $value->anggota_tanggal_expired_format = date('m/Y', strtotime($value->anggota_tanggal_expired));
            }
            $this->data['anggota_detail'] = $anggota_detail;

            $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function delete()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $anggota_id = $arg[0];
            if ($this->_is_allowed(array(role_admin))) {
                $param_anggota['table'] = 'anggota';

                $this->db->where('anggota_id', $anggota_id);
                $_cek = $this->model_generic->_cek($param_anggota);
                if ($_cek) {

                    $this->db->where('anggota_id', $anggota_id);
                    $anggota = $this->model_generic->_get($param_anggota);
                    foreach ($anggota as $key => $value) {
                        $param_user['table'] = 'user';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user);

                        $param_user_role['table'] = 'user_role';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user_role);
                    }
                    $this->db->where('anggota_id', $anggota_id);
                    $this->model_generic->_del($param_anggota);
                }

                // redirect(base . '/' . controller);
                $this->_goto_referer();
            }
        }
    }
    /*****************************************************************************/
    public function anggota_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_anggota['table'] = 'anggota';
        $total_rows             = $this->model_generic->_count($param_anggota);

        if ($this->input->get('view') && !empty($this->input->get('view'))) {
            $config_base_url = base . '/' . controller . '?view=' . $this->input->get('view');
        } else {
            $config_base_url = base . '/' . controller . '/admin';
        }

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('anggota_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
            $result[0]['search'] = $this->input->get('search');
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('anggota_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('anggota_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('anggota.anggota_nama', $this->input->get('search'));
            $this->db->or_like('anggota.anggota_nomor', $this->input->get('search'));
            $this->db->group_end();

            $total_rows = $this->model_generic->_count($param_anggota);
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('anggota_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('anggota_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('anggota.anggota_nama', $this->input->get('search'));
            $this->db->or_like('anggota.anggota_nomor', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {
            $this->db->where('anggota_status', $this->input->get('status'));
            $total_rows = $this->model_generic->_count($param_anggota);
            $this->db->where('anggota_status', $this->input->get('status'));
        }

        if ($this->input->get('order') && $this->input->get('sort')) {
            $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        }

        $anggota_all = $this->model_anggota->_get_anggota($this->_limit, $this->_offset);
        foreach ($anggota_all as $key => $value) {
            $value->toggle_btn   = ($value->anggota_disabled) ? 'default' : 'success';
            $value->toggle_icon  = ($value->anggota_disabled) ? 'toggle-off' : 'toggle-on';
            $value->anggota_foto = file_exists('./files/images/foto_anggota/' . $value->anggota_foto . '.jpg') ? $value->anggota_foto : 'default';
        }
        $this->data['anggota_all'] = $anggota_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($anggota_all);
        // exit();

        $this->load->library('pagination');
        $config['base_url']             = $config_base_url;
        $config['total_rows']           = $total_rows;
        $config['query_string_segment'] = 'offset';
        $config['per_page']             = $this->_limit;
        $config['num_links'] = 1;

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
    public function anggota_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'anggota';
            $this->db->where('anggota_id', $param['anggota_id']);
            $this->db->where('anggota_disabled != ' . $param['anggota_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('anggota_id', $param['anggota_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['anggota_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['anggota_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['anggota_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['anggota_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

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

            if ($_FILES['file_anggota']) {
                $fileName = time() . $_FILES['file_anggota']['name'];
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

                if (!$this->upload->do_upload('file_anggota')) {
                    $this->upload->display_errors();
                }

                $file_anggota  = $this->upload->data();
                $inputFileName = './files/excel_upload/' . $file_anggota['file_name'];
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
                    $param_anggota['anggota_nomor']               = $rowData[0][0];
                    $param_anggota['anggota_nama']                = $rowData[0][1];
                    $param_anggota['anggota_tanggal_lahir']       = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][2], 'YYYY-MM-DD');
                    $param_anggota['anggota_kategori']            = $rowData[0][3];
                    $param_anggota['anggota_foto']                = $rowData[0][4];
                    $param_anggota['anggota_jenis_kelamin']       = $rowData[0][5];
                    $param_anggota['anggota_status']              = $rowData[0][6];
                    $param_anggota['anggota_nomor_pasangan']      = $rowData[0][7];
                    $param_anggota['anggota_nama_pasangan']       = $rowData[0][8];
                    $param_anggota['anggota_tanggal_anniversary'] = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][9], 'YYYY-MM-DD');
                    $param_anggota['anggota_tanggal_expired']     = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][10], 'YYYY-MM-DD');
                    // opn($rowData);exit();

                    $param_anggota['table'] = 'anggota';
                    // opn($param_anggota); //exit();
                    $this->db->where('anggota_nomor', $param_anggota['anggota_nomor']);
                    $cek = $this->model_generic->_cek($param_anggota);
                    if (!$cek) {

                        $param_user['table']           = 'user';
                        $param_user['user_password']   = $this->passwordHasher->HashPassword('123456');
                        $param_user['user_given_name'] = $param_anggota['anggota_nama'];
                        $param_user['user_slug']       = slug($param_anggota['anggota_nama'], ''); //. ' ' . $param['user_family_name'], '_');
                        $param_user['user_birthday']   = $param_anggota['anggota_tanggal_lahir'];
                        $param_user['user_gender']     = $param_anggota['anggota_jenis_kelamin'];

                        $ugn = strtolower(substr($param_user['user_slug'], 0, 8));
                        // $ufn = strtoupper(substr($param['user_family_name'], 0, 2));
                        $ubd = substr($param_user['user_birthday'], -5);
                        $ubd = explode('-', $ubd);
                        $ubd = $ubd[1] . $ubd[0];

                        $param_user['user_name'] = $param_anggota['anggota_nomor']; // $ugn . $ubd;

                        $this->model_generic->_insert($param_user);
                        $user_id = $this->db->insert_id();

                        $param_user_role['table'] = 'user_role';
                        $this->db->where('user_id', $user_id);
                        $this->model_generic->_del($param_user_role);

                        $param_user_role['user_id']       = $user_id;
                        $param_user_role['role_label_id'] = 1;
                        $this->model_generic->_insert($param_user_role);

                        $param_anggota['user_id'] = $user_id;
                        $param_anggota['table']   = 'anggota';
                        $this->model_generic->_insert($param_anggota);
                    } else {

                        $this->db->where('anggota_nomor', $param_anggota['anggota_nomor']);
                        $cek = $this->model_generic->_update($param_anggota);
                    }
                }
                delete_files($file_anggota['file_path']);
                redirect(base . '/' . controller);
            }
        } else {

            $this->data['body'] = $this->parser->parse('_import.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
}

/* End of file Anggota.php */
/* Location: ./application/controllers/Anggota.php */
