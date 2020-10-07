<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        $this->load->library('library_module');

        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);

        $this->load->model('model_setting');
        $this->load->model('organisation/model_organisation');
    }
    /*****************************************************************************/
    public function index()
    {
        if ($this->_is_allowed(array(role_admin, role_member))) {

            $param_app['table']   = 'app';
            $total_rows           = $this->model_generic->_count($param_app);
            $_cek_app             = $this->model_generic->_cek($param_app);
            $config_base_url      = base . '/' . controller;
            $this->data['search'] = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                // $this->db->or_like('app.app_name', $this->input->get('search'));
            }
            $app                       = $this->model_generic->_get($param_app, $this->_limit, $this->_offset);
            $this->data['setting_all'] = $app;
            // $app = json_encode($app, JSON_PRETTY_PRINT);
            // header('Content-Type: application/json');
            // echo $app;

            $this->data['total_rows'] = $total_rows;
            $this->load->library('pagination');
            $config['base_url']             = $config_base_url;
            $config['total_rows']           = $total_rows;
            $config['query_string_segment'] = 'offset';
            $config['per_page']             = $this->_limit;
            $this->pagination->initialize($config);
            $this->data['paging'] = $this->pagination->create_links();

            $param_app['table'] = 'organisation';
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('customer_id', $this->_user_id);
            }
            $organisation               = $this->model_generic->_get($param_app);
            $this->data['organisation'] = $organisation;

            $this->data['body'] = $this->parser->parse('_index.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function card()
    {
        if ($this->_is_allowed(array(role_admin, role_member))) {
            if ($this->input->get('organisation_id')) {
                $organisation_id   = $this->input->get('organisation_id');
                $app_id            = $this->model_setting->_get_app_id(method);
                $_is_mine          = $this->model_organisation->_is_mine($organisation_id, $this->_user_id);
                $_is_subsscribed   = $this->model_setting->_is_subsscribed($organisation_id, $app_id);
                $_subscribe_status = $this->model_setting->_get_subscribe_status($organisation_id, $app_id);
                // opn($_is_subsscribed);exit();

                if ($_is_subsscribed && ($_is_mine || $this->_is_allowed(array(role_admin)))) {

                    if ($this->input->post()) {
                        $param_card                    = $this->input->post();
                        $param_card['organisation_id'] = $organisation_id;
                        $tmp['logo_align']             = $param_card['logo_align'];
                        $tmp['logo_width']             = $param_card['logo_width'];
                        $tmp['logo_top']               = $param_card['logo_top'];
                        $tmp['logo_left_right']        = $param_card['logo_left_right'];

                        $tmp['photo_align']      = $param_card['photo_align'];
                        $tmp['photo_width']      = $param_card['photo_width'];
                        $tmp['photo_top']        = $param_card['photo_top'];
                        $tmp['photo_left_right'] = $param_card['photo_left_right'];

                        $tmp['info_align']      = $param_card['info_align'];
                        $tmp['info_width']      = $param_card['info_width'];
                        $tmp['info_top']        = $param_card['info_top'];
                        $tmp['info_left_right'] = $param_card['info_left_right'];

                        $param_card['card_setting'] = json_encode(array($tmp));

                        unset($param_card['logo_align']);
                        unset($param_card['logo_width']);
                        unset($param_card['logo_top']);
                        unset($param_card['logo_left_right']);

                        unset($param_card['photo_align']);
                        unset($param_card['photo_width']);
                        unset($param_card['photo_top']);
                        unset($param_card['photo_left_right']);

                        unset($param_card['info_align']);
                        unset($param_card['info_width']);
                        unset($param_card['info_top']);
                        unset($param_card['info_left_right']);

                        // // card_background - begin
                        // // card_background - begin
                        // // card_background - begin
                        // // card_background - begin
                        // // card_background - begin
                        // opn($param_card);
                        $card_background_before = $param_card['card_background_before'];
                        unset($param_card['card_background_before']);
                        if (!isset($param_card['card_background']) || empty($param_card['card_background'])) {
                            $param_card['card_background'] = $card_background_before;
                        }
                        // echo '<br>';
                        // echo '<br>';
                        // echo strpos($card_background_before, 'sample');
                        // opn($card_background_before);
                        // exit();
                        if ($_FILES['card_background_file']['error'] == 0) {
                            // opn($_FILES['card_background']);exit();
                            $upload_path = './files/images/card';
                            $file_name   = md5(time() . uniqid());

                            if (!is_dir($upload_path)) {
                                mkdir($upload_path);
                            }
                            chmod($upload_path, 0777);

                            $config['upload_path']   = $upload_path;
                            $config['allowed_types'] = 'png';
                            // $config['max_size']         = 1024;
                            $config['max_width']        = 540;
                            $config['max_height']       = 350;
                            $config['file_name']        = $file_name;
                            $config['overwrite']        = true;
                            $config['file_ext_tolower'] = true;

                            $this->load->library('upload', $config);

                            if (!$this->upload->do_upload('card_background_file')) {
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

                            } else {
                                if (strpos($card_background_before, 'sample') !== false) {
                                } else {
                                    if (file_exists('./files/images/card/' . $card_background_before . '.png')) {
                                        unlink('./files/images/card/' . $card_background_before . '.png');
                                    }
                                }

                                $param_card['card_background'] = $file_name;
                            }
                        } else {
                            if (strpos($card_background_before, 'sample') !== false) {
                                echo 'ini adalah sample';
                                // exit();
                                // opn($card_background_before);
                                // opn($param_card['card_background']);
                            } else {
                                if (strpos($param_card['card_background'], 'sample') !== false) {
                                    echo 'ini bukan sample dan akan diganti dengan sample';
                                    if (file_exists('./files/images/card/' . $card_background_before . '.png')) {
                                        echo 'file ada';
                                        unlink('./files/images/card/' . $card_background_before . '.png');
                                    }
                                }
                            }
                        }
                        // // card_background - end
                        // // card_background - end
                        // // card_background - end
                        // // card_background - end
                        // // card_background - end

                        // opn($param_card);exit();

                        $param_card['table'] = 'card';
                        $this->db->where('organisation_id', $organisation_id);
                        $_cek = $this->model_generic->_cek($param_card);
                        if ($_cek) {
                            $this->db->where('organisation_id', $organisation_id);
                            $this->model_generic->_update($param_card);
                        } else {
                            $this->model_generic->_insert($param_card);
                        }
                        redirect(base . '/' . controller);

                    } else {

                        $param_card['table'] = 'card';
                        $this->db->where('card.organisation_id', $organisation_id);

                        $_cek = $this->model_generic->_get($param_card);
                        if (!$_cek) {
                            $param_card['organisation_id'] = $organisation_id;
                            $this->model_generic->_insert($param_card);
                        }

                        $this->db->where('card.organisation_id', $organisation_id);
                        $this->db->join('organisation', 'organisation.organisation_id = card.organisation_id', 'left');
                        $card_detail = $this->model_generic->_get($param_card);
                        if ($card_detail) {

                            foreach ($card_detail as $value) {
                                $value->organisation_logo = file_exists('./files/images/organisation/' . $value->organisation_logo . '.png') ? $value->organisation_logo : '../default-logo';
                                $value->card_background   = file_exists('./files/images/card/' . $value->card_background . '.png') ? $value->card_background : '../default-card';
                                // $value->remove_button_card = file_exists('./files/images/card/' . $value->card_background . '.png') ? '' : 'hidden';

                                if (isset($value->card_setting)) {
                                    $as['template_content'] = json_decode($value->card_setting, true);
                                    $value->template_detail = array($as);
                                } else {

                                    $template_id             = isset($value->template_id) ? $value->template_id : 1;
                                    $param_template['table'] = 'template';
                                    $this->db->where('template_id', $template_id);
                                    $template_detail = $this->model_generic->_get($param_template);
                                    foreach ($template_detail as $td_value) {
                                        $template_content[]         = json_decode($td_value->template_content, true);
                                        $td_value->template_content = $template_content;
                                    }
                                    $value->template_detail = $template_detail;
                                }

                            }
                            // opn($card_detail);exit();
                            $this->data['card_detail'] = $card_detail;

                            // $template_all               = $this->model_generic->_get($param_template);
                            // $this->data['template_all'] = $template_all;
                            $this->data['body'] = $this->parser->parse('_setting_card.html', $this->data, true);
                            $this->parser->parse('dashboard/_index.html', $this->data, false);
                        } else {
                            $error['status']        = 0;
                            $error['message']       = 'Organisation not connected with Card app.';
                            $this->data['error'][0] = $error;

                            $this->data['body'] = $this->parser->parse('_error.html', $this->data, true);
                            $this->parser->parse('dashboard/_index.html', $this->data, false);
                        }
                    }
                } else {

                    // $error['status']        = 0;
                    // $error['message']       = 'Organisation not subscribed to Card app.';
                    // $this->data['error'][0] = $error;

                    // $this->data['body'] = $this->parser->parse('_error.html', $this->data, true);
                    // $this->parser->parse('dashboard/_index.html', $this->data, false);
                    redirect(base . '/organisation/detail/' . $organisation_id);
                }
            } else {
                redirect(base);
            }
        } else {
            redirect(base . '/customer/login');
        }
    }
    /*****************************************************************************/
    public function event()
    {
        if ($this->_is_allowed(array(role_admin, role_member))) {
            if ($this->input->get('organisation_id')) {
                $organisation_id   = $this->input->get('organisation_id');
                $app_id            = $this->model_setting->_get_app_id(method);
                $_is_mine          = $this->model_organisation->_is_mine($organisation_id, $this->_user_id);
                $_is_subsscribed   = $this->model_setting->_is_subsscribed($organisation_id, $app_id);
                $_subscribe_status = $this->model_setting->_get_subscribe_status($organisation_id, $app_id);
                // opn($_is_subsscribed);exit();

                if ($_is_subsscribed && ($_is_mine || $this->_is_allowed(array(role_admin)))) {

                    if ($this->input->post()) {
                        $param_event                     = $this->input->post();
                        $param_event['organisation_id']  = $organisation_id;
                        $param_event['table']            = 'event';
                        $param_event['event_date_start'] = date('Y-m-d h:i:s', strtotime($param_event['event_date_start']));
                        $param_event['event_date_end']   = date('Y-m-d h:i:s', strtotime($param_event['event_date_end']));
                        // opn($param_event);exit();
                        if (isset($param_event['event_id'])) {
                            $this->db->where('event_id', $param_event['event_id']);
                            $this->model_generic->_update($param_event);
                        } else {
                            $this->model_generic->_insert($param_event);
                        }
                        redirect(base . '/' . controller . '/' . method . '?organisation_id=' . $organisation_id);
                    } else {
                        $param_event['table'] = 'event';
                        $total_rows           = $this->model_generic->_count($param_event);
                        $this->db->where('organisation_id', $organisation_id);
                        $event_all = $this->model_generic->_get($param_event);
                        // opn($event_all);exit();
                        $this->data['event_all'] = $event_all;
                        $this->data['body']      = $this->parser->parse('_setting_event.html', $this->data, true);
                        $this->parser->parse('dashboard/_index.html', $this->data, false);
                    }
                } else {
                    redirect(base . '/organisation/detail/' . $organisation_id);
                }
            } else {
                redirect(base . '/customer');
            }
        } else {
            redirect(base . '/customer/login');
        }
    }
    /*****************************************************************************/
    public function action_ajax()
    {
        if ($this->input->post()) {
            $param = $this->input->post();
            switch ($param['action']) {
                case 'delete':
                    $entity_table = $param['entity_table'];
                    $entity_ref   = $param['entity_ref'];
                    $entity_id    = $param['entity_id'];
                    unset($param['entity_table']);
                    unset($param['entity_ref']);
                    unset($param['entity_id']);
                    $param['table'] = $entity_table;
                    $this->db->where($entity_ref, $entity_id);
                    $this->model_generic->_del($param);
                    $res['status']  = 1;
                    $res['message'] = 'Delete successful.';
                    echo json_encode($res);
                    break;
                case 'edit':
                case 'update':

                    break;
                case 'insert':
                    break;
            }
        }
    }
    /*****************************************************************************/
    public function lottery()
    {
        if ($this->_is_allowed(array(role_admin, role_member))) {
            if ($this->input->get('organisation_id')) {
                $organisation_id   = $this->input->get('organisation_id');
                $app_id            = $this->model_setting->_get_app_id(method);
                $_is_mine          = $this->model_organisation->_is_mine($organisation_id, $this->_user_id);
                $_is_subsscribed   = $this->model_setting->_is_subsscribed($organisation_id, $app_id);
                $_subscribe_status = $this->model_setting->_get_subscribe_status($organisation_id, $app_id);
                // opn($_is_subsscribed);exit();

                if ($_is_subsscribed && ($_is_mine || $this->_is_allowed(array(role_admin)))) {

                    $this->db->where('organisation_id', $organisation_id);
                    $organisation = $this->model_organisation->_get_organisation();
                    $this->data['organisation'] = $organisation;
                    $this->data['body'] = $this->parser->parse('_setting_lottery.html', $this->data, true);
                    $this->parser->parse('dashboard/_index.html', $this->data, false);
                } else {
                    redirect(base . '/organisation/detail/' . $organisation_id);
                }

            }
        }

    }
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Setting.php */
/* Location: ./application/controllers/Setting.php */
