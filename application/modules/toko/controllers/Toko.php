<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Toko extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        $this->load->library('library_module');
        $this->data['_is_allowed'] = $this->_is_allowed(array(role_admin, role_seller)) ? '' : 'hidden destroy';
        $this->data['menu_active_toko']     = 'active';

        // $this->data['aside_left']  = $this->parser->parse('welcome/_aside_left.html', $this->data, true);
        // $this->data['aside_right'] = $this->parser->parse('welcome/_aside_right.html', $this->data, true);

        $param_category['table'] = 'category';
        $category_all = $this->model_generic->_get($param_category);
        $this->data['category_all'] = $category_all;
        
        $this->data['header'] = $this->parser->parse('welcome/_header.html', $this->data, true);
        $this->data['footer'] = $this->parser->parse('welcome/_footer.html', $this->data, true);

        $this->data['topbar']       = $this->parser->parse('welcome/_topbar.html', $this->data, true);
        $this->data['menu_desktop'] = $this->parser->parse('welcome/_menu_desktop.html', $this->data, true);
        $this->data['menu_mobile']  = $this->parser->parse('welcome/_menu_mobile.html', $this->data, true);
        // $this->data['customizer']     = $this->parser->parse('welcome/_customizer.html', $this->data, true);


        

    }
    /*****************************************************************************/
    public function index()
    {
        $this->_set_referer();
        if ($this->_is_allowed(array(role_seller))) {
            redirect(base . '/dashboard');
        } else {

            // $user_role = $_SESSION['user_info'][0]->user_role[0] ;
            // opn($user_role);exit();


            // $param_lokasi['table'] = 'billing';
            // $this->db->group_by('billing_district');
            // $this->db->where('role_label_id', role_seller);
            // $lokasi = $this->model_generic->_get($param_lokasi);
            // $this->data['lokasi'] = $lokasi;
            // opn($lokasi);exit();



            // kecamatan

            $param_kecamatan['table'] = 'kodepos';
            $this->db->group_by('kecamatan');
            // $this->db->where('role_label_id', role_seller);
            $this->db->where('kabupaten', 'Balikpapan');
            $kecamatan = $this->model_generic->_get($param_kecamatan);
            foreach ($kecamatan as $value) {
                $param_lokasi['table'] = 'billing';
                $this->db->group_by('billing_subdistrict');
                $this->db->where('role_label_id', role_seller);
                $this->db->like('billing_district', $value->kecamatan);
                $lokasi = $this->model_generic->_get($param_lokasi);
                $value->lokasi = $lokasi;
            }
            $this->data['kecamatan'] = $kecamatan;
            // opn($kecamatan);exit();



            $this->data['body'] = $this->parser->parse('toko_member.html', $this->data, true);
            $this->parser->parse('welcome/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    function kecamatan_ajax()
    {
        if ($this->input->post()) {
            $billing_district = $this->input->post('billing_district');
            $this->db->like('billing_district', $billing_district);
            $toko = $this->db->get('billing')->result();
            $toko = json_encode($toko, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $toko;
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    function toko_ajax()
    {

        if ($this->input->get('billing_coverage')) {

            $this->db->join('user', 'user.user_id = billing.user_id', 'left');
            $param_lokasi['table'] = 'billing'; 
            $this->db->like('billing_coverage', $this->input->get('billing_coverage'));
            $this->db->where('role_label_id', role_seller);
            $lokasi = $this->model_generic->_get($param_lokasi);
            foreach ($lokasi as $value) {
                $value->logo_warung         = (file_exists('./files/images/user/' . $value->user_avatar  . '.png')) ? 'user/' . $value->user_avatar . '.png' : 'logo-w.png';
            }
            $this->data['lokasi'] = $lokasi;
            // opn($lokasi);exit();

            $lokasi = json_encode($lokasi, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $lokasi;
        }
            
            
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function detail()
    {
        if ($this->_is_allowed(array(role_admin, role_seller))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $billing_id = $arg[0];

                if ($this->input->post()) {
                    $param_billing = $this->input->post();
                    if (!isset($param_billing['billing_coverage']) || empty($param_billing['billing_coverage'])) {
                        $param_billing['billing_coverage'][] = $param_billing['billing_subdistrict'];
                    }
                    $param_billing['billing_coverage'] = implode('|', $param_billing['billing_coverage']);
                    // opn($param_billing);exit();
                    $param_billing['table'] = 'billing';
                    $this->db->where('billing_id', $billing_id);
                    $account_address = $this->model_generic->_update($param_billing);



                    // redirect(base.'/'.controller.'/admin');
                    redirect(base.'/'.controller.'/detail/'.$billing_id);
                }else{

                    $param_kodepos['table'] = 'kodepos';
                    $this->db->like('kabupaten', 'Balikpapan');
                    $this->db->group_by('kecamatan');
                    $coverage = $this->model_generic->_get($param_kodepos);
                    foreach ($coverage as $value) {
                        $this->db->where('kecamatan', $value->kecamatan);
                        $kelurahan = $this->model_generic->_get($param_kodepos);
                        $kel = array();
                        foreach ($kelurahan as $kel_key => $kel_value) {
                            foreach ($kel_value as $kv_key=>$kv_value) {
                                $kel[$kel_key]['kel_'.$kv_key] = $kv_value;
                            }
                        }
                        $value->kelurahan = $kel;
                    }
                    $this->data['coverage'] = $coverage;
                    // opn($coverage);exit();


                    $param_billing['table'] = 'billing';
                    $this->db->where('billing_id', $billing_id);
                    $account_address = $this->model_generic->_get($param_billing);
                    foreach ($account_address as $value) {
                        // $billing_coverage = explode('|', $value->billing_coverage);
                        // $value->billing_coverage = json_encode($billing_coverage);
                    }
                    // opn($account_address);exit();
                    $this->data['account_address'] = $account_address;

                    $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
                    $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
                    $this->data['header'] = $this->parser->parse('dashboard/_header.html', $this->data, true);
                    $this->data['footer'] = $this->parser->parse('dashboard/_footer.html', $this->data, true);

                    $this->data['body'] = $this->parser->parse('toko_detail.html', $this->data, true);
                    $this->parser->parse('dashboard/_index.html', $this->data, false);
                }

            }
        }
    }
    /*****************************************************************************/
    function delete()
    {
        if ($this->_is_allowed(array(role_admin, role_seller))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $billing_id = $arg[0];

                $param_billing['table'] = 'billing';
                $this->db->where('billing_id', $billing_id);
                // $this->model_generic->_del($param_billing);
                redirect(base.'/'.controller.'/admin');
            }
        }
    }
    /*****************************************************************************/
    public function admin()
    {
        if ($this->_is_allowed(array(role_admin, role_seller))) {
            // $this->data['body']        = $this->parser->parse('welcome/_content_wrapper.html', $this->data, true);

            $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
            $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);

            $this->data['header'] = $this->parser->parse('dashboard/_header.html', $this->data, true);
            $this->data['footer'] = $this->parser->parse('dashboard/_footer.html', $this->data, true);

            $param_billing['table'] = 'billing';

            if ($this->_is_allowed(array(role_seller))) {
                $this->db->where('user_id', $this->_user_id);
            }
            $this->db->where('role_label_id', role_seller);
            $toko_all = $this->model_generic->_get($param_billing);
            $this->data['toko_all'] = $toko_all;
            // opn($toko_all);exit();

            $this->data['body'] = $this->parser->parse('toko_admin.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
}

/* End of file Toko.php */
/* Location: ./application/controllers/Toko.php */
