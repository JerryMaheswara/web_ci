<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ticket extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        // $this->load->library('library_module');

        // $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        // $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        // $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        // $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);

        $this->data['header']       = $this->parser->parse('welcome/_header.html', $this->data, true);
        $this->data['footer']       = $this->parser->parse('welcome/_footer.html', $this->data, true);
        $this->data['topbar']       = $this->parser->parse('welcome/_topbar.html', $this->data, true);
        $this->data['menu_desktop'] = $this->parser->parse('welcome/_menu_desktop.html', $this->data, true);
        $this->data['menu_mobile']  = $this->parser->parse('welcome/_menu_mobile.html', $this->data, true);

    }
    /*****************************************************************************/
    public function index()
    {
        $this->data['content'] = $this->parser->parse('ticket.html', $this->data, true);
        $this->data['body']    = $this->parser->parse('dashboard/_content_wrapper.html', $this->data, true);

        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function detail()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $ticket_id          = $arg[0];
            $this->data['info_active'] = controller;

            $this->data['account_info']   = $this->parser->parse('account/_account_info.html', $this->data, true);

            $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
            $this->parser->parse('welcome/_index.html', $this->data, false);

        }
    }
    /*****************************************************************************/
}

/* End of file Ticket.php */
/* Location: ./application/controllers/Ticket.php */
