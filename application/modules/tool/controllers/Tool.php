<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tool extends MY_Controller
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
        $this->passwordHasher      = new Hautelook\Phpass\PasswordHash(64, false);

    }
    /*****************************************************************************/
    public function index()
    {
        $this->data['body'] = $this->parser->parse('_index.html', $this->data, true);

        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function tool_ajax()
    {
        $res = array();

        if ($this->input->get()) {
            $param         = $this->input->get();
            $plain         = $param['plain'];
            $res['plain']  = $plain;
            $res['md5']    = md5($plain);
            $res['sha1']   = sha1($plain);
            $res['base64'] = base64_encode($plain);
            // $res['base64_decode'] = base64_encode(base64_decode($plain, true)) === $plain ? '' : base64_decode($plain);
            // $res['base64_decode'] = is_base64_encoded($plain) ? base64_decode($plain) : '';
            $res['passwordHasher'] = $this->passwordHasher->HashPassword($plain);
            $value                 = unpack('H*', $plain);
            $res['bin']            = !is_numeric($plain) ? base_convert($value[1], 16, 2) : decbin($plain);
        }

        header('Content-Type: application/json');
        echo json_encode($res, JSON_PRETTY_PRINT);

    }
    /*****************************************************************************/
}

/* End of file Tool.php */
/* Location: ./application/controllers/Tool.php */
