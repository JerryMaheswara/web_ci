<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Card extends MY_Controller
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
        $this->load->model('model_card');
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
                redirect(base . '/card/detail');
            }
        }
        $param_card['table']   = 'card';
        $total_rows           = $this->model_generic->_count($param_card);
        $_cek_card             = $this->model_generic->_cek($param_card);
        $config_base_url      = base . '/' . controller;
        $this->data['search'] = '';

        if ($this->input->get('order') && $this->input->get('sort')) {
            $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        }

        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('card.card_name', $this->input->get('search'));
        }

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('card_disabled', 0);
        }

        $card_all = $this->model_card->_get_card($this->_limit, $this->_offset);
        // opn(lq());
        // opn($card_all);
        // exit();
        // $card_all = $this->model_generic->_get($param_card, $this->_limit, $this->_offset);
        $this->data['card_all'] = $card_all;

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
    public function setting()
    {
        if ($this->_is_allowed(array(role_admin, role_member))) {
            if ($this->input->get('organisation_id')) {
                $organisation_id = $organisation_id;
            }
            $this->data['body'] = $this->parser->parse('_setting.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }

    }
    /*****************************************************************************/
    public function admin()
    {
        $this->data['view'] = '';
        $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            $param_card['table']   = 'card';
            $total_rows           = $this->model_generic->_count($param_card);
            $_cek_card             = $this->model_generic->_cek($param_card);
            $config_base_url      = base . '/' . controller;
            $this->data['search'] = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('card.card_name', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_card);

                $this->db->or_like('card.card_name', $this->input->get('search'));

            }
            $card_all = $this->model_generic->_get($param_card, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($card_all);exit();
            $this->data['card_all']    = $card_all;
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
    public function add()
    {
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->post()) {
                $param_card             = $this->input->post();
                $param_card['table']    = 'card';
                $param_card['card_slug'] = slug($param_card['card_name'], '_');
                $this->model_generic->_insert($param_card);
                redirect(base . '/card');

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
                $card_id = $arg[0];
                if ($this->input->post()) {
                    $param_card = $this->input->post();
                    // opn($param_card);exit();

                    $param_card['card_slug'] = slug($param_card['card_name'], '_');

                    $param_card['table'] = 'card';
                    $this->db->where('card_id', $card_id);
                    $_cek_card = $this->model_generic->_cek($param_card);
                    if ($_cek_card) {
                        $this->db->where('card_id', $card_id);
                        $this->model_generic->_update($param_card);

                        redirect(base . '/card/detail/' . $card_id);
                    }
                    // opn($param);exit();
                } else {

                    $param_card['table'] = 'card';
                    $this->db->where('card_id', $card_id);
                    $card_detail = $this->model_generic->_get($param_card);
                    foreach ($card_detail as $key => $value) {
                    }
                    $this->data['card_detail'] = $card_detail;

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
            $card_id             = $arg[0];
            $param_card['table'] = 'card';
            $this->db->where('card_id', $card_id);
            $card_detail = $this->model_generic->_get($param_card);
            foreach ($card_detail as $key => $value) {
            }
            $this->data['card_detail'] = $card_detail;

            $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function delete()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $card_id = $arg[0];
            if ($this->_is_allowed(array(role_admin))) {
                $param_card['table'] = 'card';

                $this->db->where('card_id', $card_id);
                $_cek = $this->model_generic->_cek($param_card);
                if ($_cek) {

                    $this->db->where('card_id', $card_id);
                    $card = $this->model_generic->_get($param_card);
                    foreach ($card as $key => $value) {
                        $param_user['table'] = 'user';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user);

                        $param_user_role['table'] = 'user_role';
                        $this->db->where('user_id', $value->user_id);
                        $this->model_generic->_del($param_user_role);
                    }
                    $this->db->where('card_id', $card_id);
                    $this->model_generic->_del($param_card);
                }

                // redirect(base . '/' . controller);
                $this->_goto_referer();
            }
        }
    }
    /*****************************************************************************/
    public function card_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_card['table'] = 'card';
        $total_rows         = $this->model_generic->_count($param_card);

        if ($this->input->get('view') && !empty($this->input->get('view'))) {
            $config_base_url = base . '/' . controller . '?view=' . $this->input->get('view');
        } else {
            $config_base_url = base . '/' . controller . '/admin';
        }

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('card_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
            $result[0]['search'] = $this->input->get('search');
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('card_disabled', '0');
            }
            $this->db->group_start();
            $this->db->or_like('card.card_name', $this->input->get('search'));
            $this->db->group_end();

            $total_rows = $this->model_generic->_count($param_card);
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('card_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('card_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('card.card_name', $this->input->get('search'));
            $this->db->or_like('card.card_nomor', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {
            $this->db->where('card_status', $this->input->get('status'));
            $total_rows = $this->model_generic->_count($param_card);
            $this->db->where('card_status', $this->input->get('status'));
        }

        if ($this->input->get('order') && $this->input->get('sort')) {
            $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        }

        $card_all = $this->model_card->_get_card($this->_limit, $this->_offset);
        foreach ($card_all as $key => $value) {
            $value->toggle_btn  = ($value->card_disabled) ? 'default' : 'success';
            $value->toggle_icon = ($value->card_disabled) ? 'toggle-off' : 'toggle-on';
        }
        $this->data['card_all'] = $card_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($card_all);
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

        header('Content-Type: cardlication/json');
        echo json_encode($result);

        // }
    }
    /*****************************************************************************/
    public function card_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'card';
            $this->db->where('card_id', $param['card_id']);
            $this->db->where('card_disabled != ' . $param['card_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('card_id', $param['card_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['card_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['card_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['card_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['card_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

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

            if ($_FILES['file_card']) {
                $fileName = time() . $_FILES['file_card']['name'];
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

                if (!$this->upload->do_upload('file_card')) {
                    $this->upload->display_errors();
                }

                $file_card      = $this->upload->data();
                $inputFileName = './files/excel_upload/' . $file_card['file_name'];
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
                    $param_card['card_nomor']               = $rowData[0][0];
                    $param_card['card_name']                = $rowData[0][1];
                    $param_card['card_tanggal_lahir']       = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][2], 'YYYY-MM-DD');
                    $param_card['card_kategori']            = $rowData[0][3];
                    $param_card['card_foto']                = $rowData[0][4];
                    $param_card['card_jenis_kelamin']       = $rowData[0][5];
                    $param_card['card_status']              = $rowData[0][6];
                    $param_card['card_nomor_pasangan']      = $rowData[0][7];
                    $param_card['card_name_pasangan']       = $rowData[0][8];
                    $param_card['card_tanggal_anniversary'] = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][9], 'YYYY-MM-DD');
                    $param_card['card_tanggal_expired']     = \PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][10], 'YYYY-MM-DD');
                    // opn($rowData);exit();

                    $param_card['table'] = 'card';
                    // opn($param_card); //exit();
                    $this->db->where('card_nomor', $param_card['card_nomor']);
                    $cek = $this->model_generic->_cek($param_card);
                    if (!$cek) {

                        $param_user['table']           = 'user';
                        $param_user['user_password']   = $this->passwordHasher->HashPassword('123456');
                        $param_user['user_given_name'] = $param_card['card_name'];
                        $param_user['user_slug']       = slug($param_card['card_name'], ''); //. ' ' . $param['user_family_name'], '_');
                        $param_user['user_birthday']   = $param_card['card_tanggal_lahir'];
                        $param_user['user_gender']     = $param_card['card_jenis_kelamin'];

                        $ugn = strtolower(substr($param_user['user_slug'], 0, 8));
                        // $ufn = strtoupper(substr($param['user_family_name'], 0, 2));
                        $ubd = substr($param_user['user_birthday'], -5);
                        $ubd = explode('-', $ubd);
                        $ubd = $ubd[1] . $ubd[0];

                        $param_user['user_name'] = $param_card['card_nomor']; // $ugn . $ubd;

                        $this->model_generic->_insert($param_user);
                        $user_id = $this->db->insert_id();

                        $param_user_role['table'] = 'user_role';
                        $this->db->where('user_id', $user_id);
                        $this->model_generic->_del($param_user_role);

                        $param_user_role['user_id']       = $user_id;
                        $param_user_role['role_label_id'] = 1;
                        $this->model_generic->_insert($param_user_role);

                        $param_card['user_id'] = $user_id;
                        $param_card['table']   = 'card';
                        $this->model_generic->_insert($param_card);
                    } else {

                        $this->db->where('card_nomor', $param_card['card_nomor']);
                        $cek = $this->model_generic->_update($param_card);
                    }
                }
                delete_files($file_card['file_path']);
                redirect(base . '/' . controller);
            }
        } else {

            $this->data['body'] = $this->parser->parse('_import.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
}

/* End of file Card.php */
/* Location: ./cardlication/controllers/Card.php */
