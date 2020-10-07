<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Template extends MY_Controller
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

    }
    /*****************************************************************************/
    public function index()
    {
        redirect(base . '/' . controller . '/admin');
        // $this->data['content'] = $this->parser->parse('template.html', $this->data, true);
        // $this->data['body']   = $this->parser->parse('dashboard/_content_wrapper.html', $this->data, true);

        // $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function admin()
    {

        if ($this->_is_allowed(array(role_admin))) {
            $param_template['table']    = 'template';
            $template_all               = $this->model_generic->_get($param_template);
            $this->data['template_all'] = $template_all;
            $this->data['body']         = $this->parser->parse('_admin.html', $this->data, true);

            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }else{
            redirect(base.'/customer');
        }
    }
    /*****************************************************************************/
    public function add()
    {
        if ($this->_is_allowed(array(role_admin))) {

            if ($this->input->post()) {
                $param_template = $this->input->post();

                // opn($param_template);exit();
                $template_name    = $param_template['template_name'];
                $template_content = json_encode($param_template);
                foreach ($param_template as $key => $value) {
                    unset($param_template[$key]);
                }
                $param_template['table']            = 'template';
                $param_template['template_name']    = $template_name;
                $param_template['template_content'] = $template_content;
                // opn($param_template);exit();
                $this->model_generic->_insert($param_template);
                // opn($param_template);exit();
                redirect(base . '/' . controller . '/');
            } else {
                $this->data['body'] = $this->parser->parse('_add.html', $this->data, true);
                $this->parser->parse('dashboard/_index.html', $this->data, false);
            }
        }
    }
    /*****************************************************************************/
    public function edit()
    {
        if ($this->_is_allowed(array(role_admin, role_member))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $template_id = $arg[0];

                if ($this->input->post()) {
                    $param_template = $this->input->post();
                    // opn($param_template);exit();
                    $template_name    = $param_template['template_name'];
                    $template_content = json_encode($param_template);
                    foreach ($param_template as $key => $value) {
                        unset($param_template[$key]);
                    }
                    $param_template['table']            = 'template';
                    $param_template['template_name']    = $template_name;
                    $param_template['template_content'] = $template_content;
                    $this->db->where('template_id', $template_id);
                    $_cek = $this->model_generic->_cek($param_template);
                    if ($_cek) {
                        $this->db->where('template_id', $template_id);
                        $this->model_generic->_update($param_template);
                        redirect(base . '/' . controller . '/detail/' . $template_id);
                    }
                } else {
                    $this->db->where('template_id', $template_id);

                    $param_template['table'] = 'template';
                    $template_detail         = $this->model_generic->_get($param_template);
                    foreach ($template_detail as $value) {
                        $template_content[]      = json_decode($value->template_content, true);
                        $value->template_content = $template_content;
                    }
                    // opn($template_detail);exit();
                    $this->data['template_detail'] = $template_detail;

                    $this->data['body'] = $this->parser->parse('_edit.html', $this->data, true);
                    $this->parser->parse('dashboard/_index.html', $this->data, false);
                }

            }
        }
    }
    /*****************************************************************************/
    public function detail()
    {
        if ($this->_is_allowed(array(role_admin, role_member))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $template_id = $arg[0];
                $this->db->where('template_id', $template_id);

                $param_template['table'] = 'template';
                $template_detail         = $this->model_generic->_get($param_template);
                foreach ($template_detail as $value) {
                    $template_content[]      = json_decode($value->template_content, true);
                    $value->template_content = $template_content;
                }
                // opn($template_detail);exit();
                $this->data['template_detail'] = $template_detail;

                $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
                $this->parser->parse('dashboard/_index.html', $this->data, false);
            }
        }
    }
    /*****************************************************************************/
    public function template_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $template_id = $arg[0];
            $this->db->where('template_id', $template_id);
        }
        $param_template['table'] = 'template';
        $template                = $this->model_generic->_get($param_template);
        foreach ($template as $value) {
            $value->id   = $value->template_id;
            $value->text = 'Template ' . $value->template_id;
        }
        $template = json_encode($template, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $template;

        // }

    }
    /*****************************************************************************/
}

/* End of file Template.php */
/* Location: ./application/controllers/Template.php */
