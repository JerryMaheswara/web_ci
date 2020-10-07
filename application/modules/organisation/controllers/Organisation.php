<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Organisation extends MY_Controller
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
        $this->load->model('model_organisation');
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
            redirect(base . '/seller/login');
        } else {
            if ($this->_is_allowed(array(role_user))) {
                // redirect(base . '/' . controller . '/admin');
            }
        }
        // $param_organisation['table'] = 'organisation';
        // $total_rows                 = $this->model_generic->_count($param_organisation);
        // $_cek_organisation           = $this->model_generic->_cek($param_organisation);
        // $config_base_url            = base . '/' . controller;
        // $this->data['search']       = '';

        // if ($this->input->get('order') && $this->input->get('sort')) {
        //     $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
        // }

        // if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
        //     $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
        //     $this->data['search'] = $this->input->get('search');
        //     $this->db->or_like('organisation.organisation_name', $this->input->get('search'));
        // }

        // if (!$this->_is_allowed(array(role_admin))) {
        //     $this->db->where('organisation_disabled', 0);
        // }

        // $organisation_all = $this->model_organisation->_get_organisation($this->_limit, $this->_offset);
        // // opn(lq());
        // // opn($organisation_all);
        // // exit();
        // // $organisation_all = $this->model_generic->_get($param_organisation, $this->_limit, $this->_offset);
        // foreach ($organisation_all as $key => $value) {
        //     $value->organisation_logo = file_exists(BASE . '/files/images/' . controller . '/' . $value->organisation_logo . '.jpg') ? $value->organisation_logo : '../default-logo';
        // }
        // $this->data['organisation_all'] = $organisation_all;

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
        if ($this->_is_allowed(array(role_admin, role_member))) {
            // $this->data['_is_allowed'] = '';
            $this->data['view'] = '';
            $this->_set_referer();
            // $arg = func_get_args();
            // if (isset($arg[0]) && $this->_is_allowed(array(role_admin))) {
            //     $seller_id = $arg[0];
            // }
            // if ($this->_is_allowed(array(role_member))) {
            //     $seller_id = $this->_user_id;
            // }
            // $param_organisation['table'] = 'organisation';
            // $this->db->where('seller_id', $seller_id);
            // $total_rows = $this->model_generic->_count($param_organisation);
            // $this->db->where('seller_id', $seller_id);
            // $_cek_organisation     = $this->model_generic->_cek($param_organisation);
            // $config_base_url      = base . '/' . controller;
            // $this->data['search'] = '';
            // if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            //     $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            //     $this->data['search'] = $this->input->get('search');
            //     $this->db->or_like('organisation.organisation_nama', $this->input->get('search'));
            //     $total_rows = $this->model_generic->_count($param_organisation);

            //     $this->db->or_like('organisation.organisation_nama', $this->input->get('search'));

            // }
            // $this->db->where('seller_id', $seller_id);
            // $organisation_all = $this->model_generic->_get($param_organisation, $this->_limit, $this->_offset);
            // // opn(lq());
            // // opn($organisation_all);exit();
            // $this->data['organisation_all'] = $organisation_all;
            // $this->data['total_rows']      = $total_rows;

            // $this->load->library('pagination');
            // $config['base_url']             = $config_base_url;
            // $config['total_rows']           = $total_rows;
            // $config['query_string_segment'] = 'offset';
            // $config['per_page']             = $this->_limit;
            // $this->pagination->initialize($config);
            // $this->data['paging']    = $this->pagination->create_links();
            $this->data['searchbar'] = $this->parser->parse('_searchbar.html', $this->data, true);

            // $this->data['view'] = 'grid';
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

            // $this->data['body'] = $this->parser->parse('_container_view_list.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        } else {
            redirect(base . '/seller/login');
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    public function add()
    {
        // $this->_set_referer();
        $arg = func_get_args();
        if (isset($arg[0]) && $this->_is_allowed(array(role_admin))) {
            $seller_id = $arg[0];
        }
        if ($this->_is_allowed(array(role_member))) {
            $seller_id = $this->_user_id;
        }
        // opn($seller_id);exit();
        if ($this->input->post()) {
            $param_organisation = $this->input->post();

            if ($_FILES['organisation_logo']['error'] == 0) {
                // opn($_FILES['organisation_logo']);exit();
                $upload_path = './files/images/' . controller;
                $file_name   = md5(time() . uniqid());

                if (!is_dir($upload_path)) {
                    mkdir($upload_path);
                }
                chmod($upload_path, 0777);
                if (!is_dir($upload_path . '/icon')) {
                    mkdir($upload_path . '/icon');
                }
                chmod($upload_path . '/icon', 0777);

                $config['upload_path']   = './files/images/' . controller;
                $config['allowed_types'] = 'png';
                // $config['max_size']         = 1024;
                $config['max_width']        = 256;
                $config['max_height']       = 256;
                $config['file_name']        = $file_name;
                $config['overwrite']        = true;
                $config['file_ext_tolower'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('organisation_logo')) {
                    $error['message']       = strip_tags($this->upload->display_errors());
                    $error['allowed_types'] = $config['allowed_types'];
                    // $error['max_size']      = $config['max_size'] . ' KB ';
                    $error['max_width']  = $config['max_width'] . ' pixels';
                    $error['max_height'] = $config['max_height'] . ' pixels';
                    // opn($error);

                    $this->data['error'] = array($error);
                    $this->data['body']  = $this->parser->parse('_error.html', $this->data, true);
                    $parse               = $this->parser->parse('dashboard/_index.html', $this->data, false);
                    echo ($parse);
                    exit();
                }
                $param_organisation['organisation_logo'] = $file_name;

                $source      = './files/images/' . controller . '/' . $file_name . '.png';
                $destination = './files/images/' . controller . '/icon/' . $file_name . '.ico';

                if (is_file($source)) {
                    $sizes = array(
                        array(32, 32),
                    );
                    $ico_lib = new PHP_ICO($source, $sizes);
                    $ico_lib->save_ico($destination);
                }
            }

            $param_organisation['table']                  = 'organisation';
            $param_organisation['seller_id']            = $seller_id;
            $param_organisation['organisation_subdomain'] = slug(strtolower($param_organisation['organisation_subdomain']), '-');
            $this->model_generic->_insert($param_organisation);
            redirect(base . '/' . controller . '/');
        } else {

            $this->data['body'] = $this->parser->parse('_add.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function edit()
    {

        if ($this->input->post()) {
            $param_organisation = $this->input->post();
            // echo strpos('sample-card-2', 'sample');
            // opn($param_organisation);exit();

            // organisation_logo - begin
            $organisation_logo_before = $param_organisation['organisation_logo_before'];
            unset($param_organisation['organisation_logo_before']);

            if ($_FILES['organisation_logo']['error'] == 0) {
                // opn($_FILES['organisation_logo']);exit();
                $upload_path = './files/images/' . controller;
                $file_name   = md5(time() . uniqid());

                if (!is_dir($upload_path)) {
                    mkdir($upload_path);
                }
                chmod($upload_path, 0777);
                if (!is_dir($upload_path . '/icon')) {
                    mkdir($upload_path . '/icon');
                }
                chmod($upload_path . '/icon', 0777);

                $config['upload_path']   = './files/images/' . controller;
                $config['allowed_types'] = 'png';
                // $config['max_size']         = 1024;
                $config['max_width']        = 256;
                $config['max_height']       = 256;
                $config['file_name']        = $file_name;
                $config['overwrite']        = true;
                $config['file_ext_tolower'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('organisation_logo')) {
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
                if (file_exists('./files/images/' . controller . '/' . $organisation_logo_before . '.png') && $organisation_logo_before != '../default-logo') {
                    unlink('./files/images/' . controller . '/' . $organisation_logo_before . '.png');
                    unlink('./files/images/' . controller . '/icon/' . $organisation_logo_before . '.ico');
                }
                $param_organisation['organisation_logo'] = $file_name;

                $source      = './files/images/' . controller . '/' . $file_name . '.png';
                $destination = './files/images/' . controller . '/icon/' . $file_name . '.ico';

                if (is_file($source)) {
                    $sizes = array(
                        array(32, 32),
                    );
                    $ico_lib = new PHP_ICO($source, $sizes);
                    $ico_lib->save_ico($destination);
                }
            }
            // organisation_logo - end
            // opn($_FILES['organisation_card_design_file']);exit();

            // $tmp['logo_align']      = $param_organisation['logo_align'];
            // $tmp['logo_width']      = $param_organisation['logo_width'];
            // $tmp['logo_top']        = $param_organisation['logo_top'];
            // $tmp['logo_left_right'] = $param_organisation['logo_left_right'];

            // $tmp['photo_align']      = $param_organisation['photo_align'];
            // $tmp['photo_width']      = $param_organisation['photo_width'];
            // $tmp['photo_top']        = $param_organisation['photo_top'];
            // $tmp['photo_left_right'] = $param_organisation['photo_left_right'];

            // $tmp['info_align']      = $param_organisation['info_align'];
            // $tmp['info_width']      = $param_organisation['info_width'];
            // $tmp['info_top']        = $param_organisation['info_top'];
            // $tmp['info_left_right'] = $param_organisation['info_left_right'];

            // $param_organisation['organisation_card_template'] = json_encode(array($tmp));

            // unset($param_organisation['logo_align']);
            // unset($param_organisation['logo_width']);
            // unset($param_organisation['logo_top']);
            // unset($param_organisation['logo_left_right']);

            // unset($param_organisation['photo_align']);
            // unset($param_organisation['photo_width']);
            // unset($param_organisation['photo_top']);
            // unset($param_organisation['photo_left_right']);

            // unset($param_organisation['info_align']);
            // unset($param_organisation['info_width']);
            // unset($param_organisation['info_top']);
            // unset($param_organisation['info_left_right']);

            // opn($param_organisation);exit();

            $param_organisation['table'] = 'organisation';
            $this->db->where('organisation_id', $param_organisation['organisation_id']);
            $_cek = $this->model_generic->_cek($param_organisation);
            if ($_cek) {
                $this->db->where('organisation_id', $param_organisation['organisation_id']);
                // $param_organisation['organisation_logo'] = $param_organisation['organisation_nomor'];
                $param_organisation['organisation_subdomain'] = slug(strtolower($param_organisation['organisation_subdomain']), '-');

                $this->model_generic->_update($param_organisation);
                $this->db->where('organisation_id', $param_organisation['organisation_id']);
                $organisation = $this->model_generic->_get($param_organisation);
                // opn($_SESSION['user_info']);exit();
                redirect(base . '/' . controller . '/detail/' . $organisation[0]->organisation_id);
            }
        } else {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $organisation_id = $arg[0];
            }
            $ismine = $this->model_organisation->_is_mine($organisation_id, $this->_user_id);
            if ($ismine || $this->_is_admin) {
                $param_organisation['table'] = 'organisation';
                $this->db->where('organisation_id', $organisation_id);
                $this->db->join('seller', 'seller.seller_id = organisation.seller_id', 'left');
                $organisation_detail = $this->model_generic->_get($param_organisation);
                // opn($_SESSION['user_info']);exit();
                // $organisation_card_template = '';
                foreach ($organisation_detail as $key => $value) {
                    $value->remove_button_logo              = file_exists('./files/images/' . controller . '/' . $value->organisation_logo . '.png') ? '' : 'hidden';
                    $value->organisation_logo               = file_exists('./files/images/' . controller . '/' . $value->organisation_logo . '.png') ? $value->organisation_logo : '../default-logo';
                    // $value->remove_button_card              = file_exists('./files/images/card/' . $value->organisation_card_design . '.png') ? '' : 'hidden';
                    // $value->organisation_card_design_select = isset($value->organisation_card_design) ? $value->organisation_card_design : '';
                    // $value->organisation_card_design        = file_exists('./files/images/card/' . $value->organisation_card_design . '.png') ? $value->organisation_card_design : '../default-card';
                    // $value->organisation_logo_before = controller . '/' . $value->organisation_logo;
                    // $value->organisation_logo = controller . '/' . $value->organisation_logo;
                    $value->expired = date('Y-m-d', strtotime('+3 month', strtotime($value->seller_created)));
                    // $template[]['template_content'] = json_decode($value->organisation_card_template, true);
                    // $organisation_card_template      = $template;
                    // $value->organisation_card_design = ()?$value->organisation_card_design:'../default-card';
                }
                $this->data['organisation_detail'] = $organisation_detail;
                // opn($organisation_detail);exit();

                // $param_template['table'] = 'template';
                // $this->db->where('template_id', 1);
                // $template_detail = $this->model_generic->_get($param_template);
                // foreach ($template_detail as $value) {
                //     $template_content[]      = json_decode($value->template_content, true);
                //     $value->template_content = $template_content;
                // }
                // $this->data['template_detail'] = $template_detail;
                // $this->data['template_detail'] = $organisation_card_template != '' ? $organisation_card_template : $template_detail;
                // $this->data['template_detail'] = isset($organisation_card_template[0]['template_content']) ? $organisation_card_template : $template_detail;

                // $template_all = $this->model_generic->_get($param_template);
                // foreach ($template_all as $value) {
                //     $template_content        = array(json_decode($value->template_content, true));
                //     $value->template_content = json_encode($template_content);
                // }
                // opn($template_all);exit();
                // $this->data['template_all'] = $template_all;

                $this->data['body'] = $this->parser->parse('_edit.html', $this->data, true);
                $this->parser->parse('dashboard/_index.html', $this->data, false);
            } else {
                redirect(base . '/' . controller . '/admin');
            }
        }
    }
    /*****************************************************************************/
    public function detail()
    { 
        if ($this->_is_allowed(array(role_admin, role_member))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $organisation_id = $arg[0];
                $this->data['organisation_id'] = $organisation_id;
                $_is_mine        = $this->model_organisation->_is_mine($organisation_id, $this->_user_id);
                if ($_is_mine || $this->_is_admin) {
                    $this->data['organisation_id_before'] = $organisation_id;
                    $param_organisation['table']          = 'organisation';
                    $this->db->where('organisation_id', $organisation_id);
                    $this->db->join('kodepos', 'kodepos.kodepos_id = organisation.kodepos_id', 'left');
                    $this->db->join('seller', 'seller.seller_id = organisation.seller_id', 'left');
                    // $this->db->join('template', 'template.template_id = organisation.template_id', 'left');
                    $organisation_detail = $this->model_generic->_get($param_organisation);
                    // $organisation_card_template = '';
                    foreach ($organisation_detail as $value) {
                        $value->organisation_logo          = file_exists('./files/images/' . controller . '/' . $value->organisation_logo . '.png') ? $value->organisation_logo : '../default-logo';
                        $value->organisation_subdomain_url = str_replace("supersite", $value->organisation_subdomain, $_SERVER['HTTP_HOST']);
                        // $template[0]['template_content']    = array(json_decode($value->template_content, true));
                        // $value->template_detail            = $template;
                        // unset($value->template_content);
                        // $value->organisation_card_design = file_exists('./files/images/card/' . $value->organisation_card_design . '.png') ? $value->organisation_card_design : '../default-card';

                        // $param_template['table'] = 'template';
                        // $template_id             = $value->template_id ?: 1;
                        // $this->db->where('template_id', $template_id);
                        // $template_detail = $this->model_generic->_get($param_template);
                        // foreach ($template_detail as $td_value) {
                        //     $template_content           = json_decode($td_value->template_content, true);
                        //     $td_value->template_content = array($template_content);
                        // }
                        // $value->template_detail = $template_detail;

                        // if (!empty($value->organisation_card_template)) {
                        //     $as['template_content']           = json_decode($value->organisation_card_template, true);

                        //     $value->template_detail = array($as);
                        // }

                        // $value->template_detail = (empty($value->organisation_card_template) && ($value->template_id < 1 || empty($value->template_id))) ? $template_detail : array(json_decode($value->organisation_card_template));
                    }
                    // opn($organisation_card_template);exit();
                    // opn($organisation_detail);exit();
                    $this->data['organisation_detail'] = $organisation_detail;

                    // $param_template['table'] = 'template';
                    // $this->db->where('template_id', 1);
                    // $template_detail = $this->model_generic->_get($param_template);
                    // foreach ($template_detail as $value) {
                    //     $template_content[]      = json_decode($value->template_content, true);
                    //     $value->template_content = $template_content;
                    // }
                    // opn($template_detail);exit();
                    // $this->data['template_detail'] = isset($organisation_card_template[0]['template_content']) ? $organisation_card_template : $template_detail;
                    $this->load->model('app/model_app');
                    $app_all = $this->model_app->_get_app();
                    foreach ($app_all as $value) {
                        $value->nomor_urut_app          = $value->nomor_urut;
                        $value->subscribe_status        = $this->model_app->_subscribe_status($organisation_id, $value->app_id);
                        // $value->subscribe_status_button = ($value->subscribe_status == false) ? '' : 'hidden';
                        // $value->subscribe_status_icon   = ($value->subscribe_status == false) ? 'square-o' : 'check-square-o';
                        // $value->subscribe_status_color  = ($value->subscribe_status == false) ? 'danger' : 'success';
                        // $value->subscribe_status_text   = ($value->subscribe_status == false) ? 'Unsubscribed' : 'Subscribed';
                        // $value->subscribe_status_check  = ($value->subscribe_status == false) ? '' : 'hidden destroy';
                    }
                    $this->data['app_all'] = $app_all;
                    // opn($app_all);exit();
                    $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
                    $this->parser->parse('dashboard/_index.html', $this->data, false);

                } else {
                    redirect(base . '/' . controller . '/admin');
                }
            }
            // opn($organisation_id);exit();
        } else {
            redirect(base . '/seller/login');
        }

    }
    /*****************************************************************************/
    public function delete()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $organisation_id = $arg[0];
            if ($this->_is_allowed(array(role_admin))) {
                $param_organisation['table'] = 'organisation';

                $this->db->where('organisation_id', $organisation_id);
                $_cek = $this->model_generic->_cek($param_organisation);
                if ($_cek) {

                    $this->db->where('organisation_id', $organisation_id);
                    $organisation = $this->model_generic->_get($param_organisation);
                    foreach ($organisation as $key => $value) {
                        
                        if (file_exists('./files/images/' . controller . '/' . $value->organisation_logo . '.png')) {
                            unlink('./files/images/' . controller . '/' . $value->organisation_logo . '.png');
                            unlink('./files/images/' . controller . '/icon/' . $value->organisation_logo . '.ico');
                        }
                        // $param_user['table'] = 'user';
                        // $this->db->where('user_id', $value->user_id);
                        // $this->model_generic->_del($param_user);

                        // $param_user_role['table'] = 'user_role';
                        // $this->db->where('user_id', $value->user_id);
                        // $this->model_generic->_del($param_user_role);
                    }
                    $this->db->where('organisation_id', $organisation_id);
                    $this->model_generic->_del($param_organisation);
                }

                // redirect(base . '/' . controller);
                $this->_goto_referer();
            }
        }
    }
    /*****************************************************************************/
    public function organisation_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        if ($this->_is_allowed(array(role_admin, role_member))) {
            // $this->data['_is_allowed'] = '';

            $arg = func_get_args();
            if (isset($arg[0]) && $this->_is_allowed(array(role_admin))) {
                $seller_id = $arg[0];
            }
            if ($this->_is_allowed(array(role_member))) {
                $seller_id = $this->_user_id;
            }

            $param_organisation['table'] = 'organisation';

            if ($this->_is_allowed(array(role_member))) {
                $this->db->where('seller_id', $seller_id);
            }
            $total_rows = $this->model_generic->_count($param_organisation);

            if ($this->input->get('view') && !empty($this->input->get('view'))) {
                $config_base_url = base . '/' . controller . '?view=' . $this->input->get('view');
            } else {
                $config_base_url = base . '/' . controller . '/admin';
            }

            if (!$this->_is_allowed(array(role_admin))) {
                // $this->db->where('organisation_disabled', '0');
            }
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
                $result[0]['search'] = $this->input->get('search');
                if (!$this->_is_allowed(array(role_admin))) {
                    // $this->db->where('organisation_disabled', '0');
                }
                if ($this->input->get('status')) {
                    $this->db->group_start();
                    $this->db->where('organisation_status', $this->input->get('status'));
                    $this->db->group_end();
                }
                $this->db->group_start();
                $this->db->or_like('organisation.organisation_nama', $this->input->get('search'));
                $this->db->or_like('organisation.organisation_nomor', $this->input->get('search'));
                $this->db->group_end();

                if ($this->_is_allowed(array(role_member))) {
                    $this->db->where('organisation.seller_id', $seller_id);
                }
                $total_rows = $this->model_generic->_count($param_organisation);
                if (!$this->_is_allowed(array(role_admin))) {
                    // $this->db->where('organisation_disabled', '0');
                }
                if ($this->input->get('status')) {
                    $this->db->group_start();
                    $this->db->where('organisation_status', $this->input->get('status'));
                    $this->db->group_end();
                }
                $this->db->group_start();
                $this->db->or_like('organisation.organisation_nama', $this->input->get('search'));
                $this->db->or_like('organisation.organisation_nomor', $this->input->get('search'));
                $this->db->group_end();

            }

            if (is_numeric($this->input->get('status'))) {
                $this->db->where('organisation_status', $this->input->get('status'));

                if ($this->_is_allowed(array(role_member))) {
                    $this->db->where('organisation.seller_id', $seller_id);
                }
                $total_rows = $this->model_generic->_count($param_organisation);
                $this->db->where('organisation_status', $this->input->get('status'));
            }

            if ($this->input->get('order') && $this->input->get('sort')) {
                $this->db->order_by($this->input->get('order'), $this->input->get('sort'));
            }

            if ($this->_is_allowed(array(role_member))) {
                $this->db->where('organisation.seller_id', $seller_id);
            }
            $this->db->join('seller', 'seller.seller_id = organisation.seller_id', 'left');
            // $this->db->join('template', 'template.template_id = organisation.template_id', 'left');
            $organisation_all = $this->model_generic->_get($param_organisation, $this->_limit, $this->_offset);
            foreach ($organisation_all as $key => $value) {

                // opn($template_detail);
                $value->toggle_btn                 = ($value->organisation_disabled) ? 'default' : 'success';
                $value->toggle_icon                = ($value->organisation_disabled) ? 'toggle-off' : 'toggle-on';
                $value->organisation_logo          = file_exists('./files/images/' . controller . '/' . $value->organisation_logo . '.png') ? $value->organisation_logo : '../default-logo';
                $value->organisation_subdomain_url = str_replace("supersite", $value->organisation_subdomain, $_SERVER['HTTP_HOST']);
                // $value->organisation_card_design   = file_exists('./files/images/card/' . $value->organisation_card_design . '.png') ? $value->organisation_card_design : '../default-card';
                $value->total_member = 0;
                if (isset($value->organisation_id) && !empty($value->organisation_id)) {
                    // $this->db->where('organisation_id', $value->organisation_id);
                    // $param_member['table'] = 'member';
                    // $value->total_member = $this->model_generic->_count($param_member);
                }

            }
            $this->data['organisation_all'] = $organisation_all;

            // opn($total_rows);exit();
            // opn(lq());
            // opn($organisation_all); exit();

            $this->load->library('pagination');
            $config['base_url']             = $config_base_url;
            $config['total_rows']           = $total_rows;
            $config['query_string_segment'] = 'offset';
            $config['per_page']             = $this->_limit;
            $config['num_links']            = 1;

            $config['first_link'] = '&laquo;';
            $config['last_link']  = '&raquo;';
            $config['next_link']  = '&rarr;';
            $config['prev_link']  = '&larr;';

            $this->pagination->initialize($config);

            $result[0]['paging']     = $this->pagination->create_links();
            $result[0]['_tbody']     = $this->parser->parse('_data_view_list.html', $this->data, true);
            $result[0]['_gridview']  = $this->parser->parse('_data_view_grid.html', $this->data, true);
            $result[0]['total_rows'] = $total_rows;

            header('Content-Type: application/json');
            echo json_encode($result);
        }

        // }
    }
    /*****************************************************************************/
    public function organisation_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'organisation';
            $this->db->where('organisation_id', $param['organisation_id']);
            $this->db->where('organisation_disabled != ' . $param['organisation_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('organisation_id', $param['organisation_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['organisation_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['organisation_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['organisation_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['organisation_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

                echo json_encode($return);
            }
            // opn($_cek_module);exit();

        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function create_favicon()
    {
        $source      = BASE . '/files/images/default-logo.png';
        $destination = BASE . '/files/images/default-logo.ico';

        // $source      = './files/images/' . controller . '/adidas4.png';
        // $destination = './files/images/' . controller . '/adidas4.ico';

        if (is_file($source)) {
            $sizes = array(
                array(32, 32),
            );
            $ico_lib = new PHP_ICO($source, $sizes);
            $ico_lib->save_ico($destination);
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function check_subdomain()
    {
        if ($this->input->get('subdomain')) {
            $subdomain                   = $this->input->get('subdomain');
            $param_organisation['table'] = 'organisation';
            $this->db->where('organisation_subdomain', $subdomain);
            $_cek = $this->model_generic->_cek($param_organisation);
            if ($_cek) {
                $res['status']  = 1;
                $res['message'] = 'Subdomain is already taken, please choose another one ...!';
            } else {
                $res['status']  = 0;
                $res['message'] = 'OK';
            }
            echo json_encode($res);
        }
    }
    /*****************************************************************************/
    public function remove_logo()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $organisation_id             = $arg[0];
            $param_organisation['table'] = 'organisation';
            $this->db->where('organisation_id', $organisation_id);
            $organisation = $this->model_generic->_get($param_organisation);
            foreach ($organisation as $value) {
                if (file_exists('./files/images/' . controller . '/' . $value->organisation_logo . '.png')) {
                    unlink('./files/images/' . controller . '/' . $value->organisation_logo . '.png');
                    unlink('./files/images/' . controller . '/icon/' . $value->organisation_logo . '.ico');
                }
                $res['status']  = 1;
                $res['message'] = 'Logo deleted.';
                echo json_encode($res);

            }
        }

    }
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Organisation.php */
/* Location: ./application/controllers/Organisation.php */
