<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Language extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
        $this->data['content_header'] = __CLASS__;
        $this->data['treeview_menu']  = 'dashboard';

        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);
        $this->data['body']        = $this->parser->parse('dashboard/_content_wrapper.html', $this->data, true);
        // $this->data['content_header']     = isset($this->data['m_language']) ? $this->data['m_language'] : 'Language';
        // $this->data['content_header_sub'] = isset($this->data['m_control_panel']) ? $this->data['m_control_panel'] : 'Control Panel';
    }
    /*****************************************************************************/
    public function site_translate_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $site_term_id = $arg[0];
            // opn($this->_user_id);exit();
            if ($this->input->post()) {
                $param = $this->input->post();
                // opn($param);exit();
                $site_term_id     = $this->input->post('site_term_id');
                $param['user_id'] = $this->_user_id;
                $action           = $param['action'];
                unset($param['action']);
                $site_translate_id = $param['site_translate_id'];
                $param['table']    = 'site_translate';
                switch ($action) {
                    case 'add':
                    case 'update':
                    case 'edit':
                        if (isset($param['site_translate_id']) && !empty($param['site_translate_id'])) {
                            $this->db->where('site_translate_id', $site_translate_id);
                            $site_translate = $this->model_generic->_update($param);
                        } else {
                            $site_translate = $this->model_generic->_insert($param);
                        }
                        echo json_encode($site_translate);
                        break;

                    case 'delete':
                        $this->db->where('site_translate_id', $site_translate_id);
                        $site_translate = $this->model_generic->_del($param);
                        echo json_encode($site_translate);
                        break;
                }

            } else {

                $param['table'] = 'site_translate';
                $this->db->where('site_translate.site_term_id', $site_term_id);
                $this->db->join('site_term', 'site_term.site_term_id = site_translate.site_term_id', 'left');
                $this->db->join('site_language', 'site_language.site_language_id = site_translate.site_language_id', 'left');
                $this->db->join('language', 'language.language_id = site_language.language_id', 'left');
                $site_translate = $this->model_generic->_get($param);
                $site_translate = json_encode($site_translate, JSON_PRETTY_PRINT);
                header('Content-Type: application/json');
                echo $site_translate;

            }
        }
        // }
    }
    /*****************************************************************************/
    public function site_term_ajax()
    {
        // opn($this->_user_id);exit();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $param                   = $this->input->post();
                $param['user_id']        = $this->_user_id;
                $param['site_term_slug'] = slug($param['site_term_name'], '_');
                // $url                     = api . '/language/term';
                // $language                = $this->api_call_post($url, $this->_username, $this->_password, $param);
                // echo $language;
                // opn($param);exit();
                $action = $param['action'];
                unset($param['action']);
                $site_term_id   = $param['site_term_id'];
                $param['table'] = 'site_term';

                switch ($action) {
                    case 'add':
                        if (isset($param['site_term_id']) && !empty($param['site_term_id'])) {
                            $this->db->where('site_term_id', $site_term_id);
                            $site_term = $this->model_generic->_update($param);
                            echo json_encode($site_term);
                        } else {
                            $site_term = $this->model_generic->_insert($param);
                            echo json_encode($site_term);
                        }
                        break;

                    case 'delete':
                        $this->db->where('site_term_id', $site_term_id);
                        $site_term = $this->model_generic->_del($param);
                        echo json_encode($site_term);
                        break;
                }
            } else {
                $param['table'] = 'site_term';
                $arg            = func_get_args();
                if (isset($arg[0])) {
                    $this->db->where('site_term.site_term_id', $arg[0]);
                }
                $site_term = $this->model_generic->_get($param);
                foreach ($site_term as $key => $value) {
                    $param['table'] = 'site_translate';
                    $this->db->where('site_term_id', $value->site_term_id);
                    $term_translate        = $this->model_generic->_count($param);
                    $value->term_translate = $term_translate;

                    $this->db->where('site_translate.site_term_id', $value->site_term_id);
                    $this->db->join('site_term', 'site_term.site_term_id = site_translate.site_term_id', 'left');
                    $this->db->join('site_language', 'site_language.site_language_id = site_translate.site_language_id', 'left');
                    $this->db->join('language', 'language.language_id = site_language.language_id', 'left');

                    $this->db->select('site_translate.site_translate_translation');
                    $this->db->select('language.language_id');
                    $this->db->select('language.language_name');
                    $value->term_translate_detail = $this->model_generic->_get($param);

                }
                $site_term = json_encode($site_term, JSON_PRETTY_PRINT);
                header('Content-Type: application/json');
                echo $site_term;

            }
            // $language = json_decode($language);
            // opn($language);exit();
        }
    }
    /*****************************************************************************/
    public function site_language_ajax()
    {
        if ($this->input->is_ajax_request()) {

            if ($this->input->post()) {
                $param = $this->input->post();
                // $param['user_id'] = $this->_user_id;

                $action = $param['action'];
                unset($param['action']);
                $language_id    = $param['language_id'];
                $param['table'] = 'site_language';
                switch ($action) {
                    case 'add':
                        $site_language = $this->model_generic->_insert($param);
                        echo json_encode($site_language);
                        break;

                    case 'delete':
                        $this->db->where('language_id', $language_id);
                        $site_language = $this->model_generic->_del($param);
                        echo json_encode($site_language);
                        break;
                }
            } else {

                $param['table'] = 'site_language';
                $this->db->join('language', 'language.language_id = site_language.language_id', 'left');
                $this->db->select(' *, site_language.site_language_id as id, language.language_name as text');
                $language = $this->model_generic->_get($param);
                $language = json_encode($language, JSON_PRETTY_PRINT);
                header('Content-Type: application/json');
                echo $language;
            }
        }

    }
    /*****************************************************************************/
    public function language_ajax()
    {
        // if ($this->input->is_ajax_request()) {

        $param_siswa['table'] = 'site_language';
        $site_language        = $this->model_generic->_get($param_siswa);

        $inner = array();
        foreach ($site_language as $key => $value) {
            $inner[] = $value->language_id;
        }
        $inner          = implode(',', $inner);
        $outer['table'] = 'language';
        if ($inner) {
            $this->db->where('language_id NOT IN (' . $inner . ')');
        }
        $this->db->select('*, language_id as id, language_name as text, ');
        $language = $this->model_generic->_get($outer);
        $language = json_encode($language, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $language;
        // }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    public function detail()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $language_id = $arg[0];

            $param['table'] = 'language';
            $this->db->where('language_id', $language_id);
            $language = $this->model_generic->_get($param);

            $this->data['language_detail'] = $language;
            // opn($language);exit();
            $this->data['content'] = $this->parser->parse('language_detail.html', $this->data, true);

            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
        # code...
    }
    /*****************************************************************************/
    public function index()
    {
        redirect(base . '/language/admin');
    }
    /*****************************************************************************/
    public function admin()
    {
        $this->_allowed_to(array(role_admin));
        $this->data['treeview_menu'] = controller;
        // $this->data['treeview_menu']      = method;
        $this->data['content_sub_header'] = ucfirst(method);
        $this->data['body']               = $this->parser->parse('language_content.html', $this->data, true);
        $this->parser->parse('dashboard/_index.html', $this->data, false);

    }
    /*****************************************************************************/
    public function site()
    {
        # code...
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function site_term()
    {
        if ($this->_is_allowed(array(role_admin))) {
            $this->data['treeview_menu']      = 'site_term';
            $this->data['content_sub_header'] = __CLASS__;
            $arg                              = func_get_args();
            if (isset($arg[0])) {
                $site_term_id   = $arg[0];
                $param['table'] = 'site_term';
                $this->db->where('site_term_id', $site_term_id);
                $site_term_detail               = $this->model_generic->_get($param);
                $this->data['site_term_detail'] = $site_term_detail;
                $this->data['site_term_id']     = $site_term_id;
                $this->data['body']             = $this->parser->parse('site_term_detail.html', $this->data, true);
            } else {
                $this->data['body'] = $this->parser->parse('site_term_content.html', $this->data, true);
            }
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        } else {
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function change()
    {
        // if ($this->input->is_ajax_request()) {
        if ($this->input->post('site_language_id')) {
            $site_language_id             = $this->input->post('site_language_id');
            $_SESSION['site_language_id'] = $site_language_id;
            // echo $_SESSION['site_language_id'];
            echo $site_language_id;
        }
        // }
    }
    /*****************************************************************************/
    /*****************************************************************************/

}

/* End of file Language.php */
/* Location: ./application/controllers/Language.php */
