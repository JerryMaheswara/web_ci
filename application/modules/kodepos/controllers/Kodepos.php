<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kodepos extends MY_Controller 
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        $this->load->library('library_module');
        $this->load->library('datatables');

        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);

    }
    /*****************************************************************************/
    public function index()
    {
        $this->data['content'] = $this->parser->parse('kodepos.html', $this->data, true);
        $this->data['body']   = $this->parser->parse('dashboard/_content_wrapper.html', $this->data, true);
 
        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    /*****************************************************************************/
    function datatables()
    {
        // opn($this->db->database);exit();
        // $this->datatables->set_database($this->db->database);

        $this->datatables->select('kodepos, kelurahan, kecamatan, kabupaten')
                // ->unset_column('kodepos_id')
                ->from('kodepos');

        // opn($this->datatables->generate());exit();
        echo $this->datatables->generate('json', '');
    }
    /*****************************************************************************/
}

/* End of file Kodepos.php */
/* Location: ./application/controllers/Kodepos.php */
