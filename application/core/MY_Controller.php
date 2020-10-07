<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    /*****************************************************************************/
    /*****************************************************************************/
    protected $_is_login  = false;
    protected $_is_admin  = false;
    protected $_user_id   = '';
    protected $_user_type = '';
    protected $_user_role = '';
    protected $_user_name = '';
    protected $_limit     = 10;
    protected $_offset    = 0;
    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        // $this->data['error_message'] = 'Belum Login';
        // $error = $this->parser->parse('error.html', $this->data, true);
        // show_error($error, 401 );

        // session_destroy();
        // opn($_SESSION);exit();
        $this->_db_ = isset($_SESSION['_db_']) ? $_SESSION['_db_'] : $this->db->database;
        $this->load->library('user/library_role');

        $domain = explode(':', $_SERVER['HTTP_HOST']);
        $domain = $domain[0];
        // opn($domain);exit();
        $this->data['domain'] = $domain;

        $subdomain_arr  = explode('.', $_SERVER['HTTP_HOST'], 2); //creates the various parts
        $subdomain_name = $subdomain_arr[0]; //assigns the first part
        // opn($subdomain_name);exit();

        // opn($_COOKIE['ci_session']);exit();

        date_default_timezone_set("Asia/Jakarta");
        $this->data['today'] = date('Y-F-d ', time());
        // opn(get_included_files());
        $base = str_replace($_SERVER['SERVER_ADDR'], $_SERVER['HTTP_HOST'], base_url());
        $base = str_replace('[', '', $base);
        $base = str_replace(']', '', $base);
        $base = rtrim($base, "/");
        define('controller', $this->router->class);
        define('method', $this->router->method);
        define('base', $base);
        define('BASE', dirname(dirname(__DIR__)));
        $this->data['BASE']       = BASE;
        $this->data['base']       = base;
        $this->data['CI_VERSION'] = CI_VERSION;
        $this->data['user_info']  = array();

        $this->data['controller'] = controller;
        $this->data['method']     = method;

        if (isset($_SESSION['user_info'])) {
            // opn($_SESSION['user_info']);exit();
            $this->_is_login = true;
            $this->_is_admin = $this->_is_allowed(array(role_admin));

            $this->_user_id                     = $_SESSION['user_info'][0]->user_id;
            $this->_user_role                   = $_SESSION['user_info'][0]->user_role;
            $this->_user_name                   = $_SESSION['user_info'][0]->user_name_display;
            $this->data['my_user_display_name'] = $_SESSION['user_info'][0]->user_name_first . ' ' . $_SESSION['user_info'][0]->user_name_last;
            // $_SESSION['user_info'][0]->user_avatar = (file_exists(BASE . '/files/images/' . $_SESSION['user_info'][0]->user_avatar)) ? $_SESSION['user_info'][0]->user_avatar : 'default.png';
            $this->data['user_info'] = $_SESSION['user_info'];
        }
        // opn($this->data['user_info']);exit();
        $this->data['my_name']    = $this->_user_name;
        $this->data['my_user_id'] = $this->_user_id;

        $this->data['is_admin']  = ($this->_is_admin) ? 'is_admin' : 'hidden destroy';
        $this->data['is_login']  = $this->_is_login ? 'is_login' : 'hidden destroy';
        $this->data['is_logout'] = !$this->_is_login ? 'is_logout' : 'hidden destroy';

        if (isset($_SESSION['skin'])) {
            $this->data['skin'] = $_SESSION['skin'];
            switch ($this->data['skin']) {
                case 'blue':
                    $this->data['skin_box'] = 'primary';
                    break;
                case 'red':
                    $this->data['skin_box'] = 'danger';
                    break;
                case 'yellow':
                    $this->data['skin_box'] = 'warning';
                    break;
                case 'green':
                    $this->data['skin_box'] = 'success';
                    break;
                default:
                    $this->data['skin_box'] = 'primary';
                    break;
            }
        } else {
            $this->data['skin']     = 'blue';
            $this->data['skin_box'] = 'primary';
        }
        /****************************************************** skin ----- end*/

        /*****************************************************************************/
        $thn_range    = range(2010, 2020);
        $tahun_ajaran = array();
        foreach ($thn_range as $key => $value) {
            $tahun_ajaran[$key]['nama_tahun'] = $value - 1 . '/' . $value;
        }
        $this->data['tahun_ajaran'] = $tahun_ajaran;

        $this->data['semester_ini'] = (date('m') > 6) ? 1 : 2;
        $this->data['tahun_ini']    = ($this->data['semester_ini'] == 2) ? date('Y') - 1 . '/' . date('Y') : date('Y') . '/' . (date('Y') + 1);
        /*****************************************************************************/

        $this->data['site_title']      = $this->config->item('site_title');
        $this->data['site_title_mini'] = $this->config->item('site_title_mini');
        $this->data['site_address']    = $this->config->item('site_address');
        $this->data['site_logo']       = $this->config->item('site_logo');
        // $this->data['user_avatar']     = 'default.png';

        $this->_limit        = isset($_SESSION['limit']) ? $_SESSION['limit'] : $this->_limit;
        $this->data['limit'] = $this->_limit;
        $this->_offset       = 0;
        if ($this->input->get('offset')) {
            $this->_offset = $this->input->get('offset');
        } else {
            if ($this->input->get('page') && $this->input->get('page') > 1) {
                $this->_offset = ($this->input->get('page') - 1) * $this->_limit;
            }
        }
        $this->data['set_limit'] = $this->parser->parse('dashboard/_set_limit.html', $this->data, true);

        $this->data['sort']      = 'asc';
        $this->data['sort']      = ($this->input->get('sort') == 'asc') ? 'desc' : 'asc';
        $this->data['sort_icon'] = ($this->input->get('sort') == 'asc') ? 'asc' : 'desc';

        $this->data['order'] = ($this->input->get('order')) ?: '';

        $query_string = $this->input->get();
        unset($query_string['offset']);
        unset($query_string['search']);
        $qq = array();
        foreach ($query_string as $key => $value) {
            $qs['key']   = $key;
            $qs['value'] = $value;
            $qq[]        = $qs;
        }
        $query_string                = http_build_query($query_string);
        $this->data['query_string']  = '?' . $query_string ?: '';
        $this->data['query_strings'] = $qq;
        $this->data['search']        = $this->input->get('search') ?: '';
        $this->data['search_box']    = $this->parser->parse('dashboard/_search_box.html', $this->data, true);

        $current_uri = $_SERVER['HTTP_HOST'];
        $current_uri .= $_SERVER["REQUEST_URI"];
        $current_uri               = str_replace('?' . $_SERVER['QUERY_STRING'], '', $current_uri);
        $this->data['current_uri'] = $current_uri;
        // opn($current_uri);exit();

        // $this->data['treeview_menu'] = controller;
        $this->load->library('language/library_language');
        $this->load->library('dashboard/library_module');

        $this->load->library('cart');
        $cart_count                      = count($this->cart->contents());
        $this->data['cart_count']        = $cart_count;
        $total                           = $this->cart->total();
        $this->data['cart_total']        = $total;
        $this->data['cart_total_format'] = number_format($total, 0);

        $table_aktif    = isset($_SESSION['table_aktif']) ? $_SESSION['table_aktif'] : 'user';
        $account_detail = array();
        if ($this->_is_login) {
            $this->data['table_aktif'] = $table_aktif;
            $param_member['table']     = $table_aktif;
            $this->db->where($table_aktif . '_id', $this->_user_id);
            $account_detail = $this->model_generic->_get($param_member);
            foreach ($account_detail as $value) {
                $value->my_name_display   = $value->{$table_aktif . '_name_display'};
                $value->my_name_first     = $value->{$table_aktif . '_name_first'};
                $value->my_name_last      = $value->{$table_aktif . '_name_last'};
                $value->my_created_format = date('F d, Y', strtotime($value->{$table_aktif . '_created'}));
                $value->my_avatar         = (file_exists('./files/images/' . $table_aktif . '/' . $value->{$table_aktif . '_avatar'} . '.png')) ? $table_aktif . '/' . $value->{$table_aktif . '_avatar'} . '.png' : 'default.png';
            }
            // opn($account_detail);exit();
            $this->data['my_account_detail'] = $account_detail;
        }else{
            $this->data['my_account_detail'] = array();
        }

        /////////////////////////////////////
        $this->data['alamat_ku'] = 'Alamat';
        /////////////////////////////////////
        if ($this->_is_allowed(array(role_member))) {
            $this->data['alamat_ku'] = 'Alamat Rumah';
        }

        if ($this->_is_allowed(array(role_seller))) {
            $this->data['alamat_ku'] = 'Alamat Toko';
        }
        if ($this->_is_allowed(array(role_admin))) {
            $this->data['alamat_ku'] = 'Alamat';
        }

        /////////////////////////////////////

        $this->data['menu_active_home']     = '';
        $this->data['menu_active_category'] = '';
        $this->data['menu_active_item']     = '';
        $this->data['menu_active_toko']     = '';
        /////////////////////////////////////
        /////////////////////////////////////
    }
    /*****************************************************************************/
    /*****************************************************************************/

    /*****************************************************************************/
    public function change_skin()
    {
        $_SESSION['skin'] = $this->input->post('skin');
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function _set_referer()
    {
        $ses_referer = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->session->set_userdata('referer', $ses_referer);
    }
    /*****************************************************************************/
    public function _get_referer()
    {
        return $this->session->userdata['referer'];
    }
    /*****************************************************************************/
    public function _goto_referer()
    {
        if (isset($this->session->userdata['referer'])) {
            redirect($this->session->userdata['referer']);
        } else {
            redirect(base);
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function _my_role()
    {
        if ($this->_is_login) {
            $my_role = $_SESSION['user_info'][0]->user_role;
            $my_role = implode(',', $my_role);
            return $my_role;
        }
        return 0;
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function _allowed_to($param)
    {
        $array1 = $param;
        $array2 = ($this->_is_login) ? $_SESSION['user_info'][0]->user_role : array();
        $x      = array_intersect($array1, $array2);
        // return $x;
        if (false == $x) {
            redirect(base);
        }
    }
    /*****************************************************************************/
    public function change_language()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('site_language_id')) {
                $site_language_id                            = $this->input->post('site_language_id');
                $this->session->userdata['site_language_id'] = $site_language_id;
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function set_limit($limit)
    {
        if ($this->input->is_ajax_request()) {
            if (isset($limit) && is_numeric($limit)) {
                $_SESSION['limit'] = $limit;
                echo $limit;
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function _is_allowed($roles)
    {
        $array1 = $roles ?: array();
        $array2 = ($this->_is_login) ? $_SESSION['user_info'][0]->user_role : array();
        return array_intersect($array1, $array2);
    }
    /*****************************************************************************/
    /************************** API CALL - BEGIN *********************************/
    /*****************************************************************************/
    /*****************************************************************************/
    public function api_file_get_contents($url, $username = null, $password = null)
    {
        $stream['http']['header'] = 'Authorization: Basic ' . base64_encode("$username:$password");
        $context                  = stream_context_create($stream);
        return @file_get_contents($url, false, $context);
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function api_call_get($url, $username = null, $password = null)
    {
        $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    /*****************************************************************************/
    public function api_call_post($url, $username = null, $password = null, $param = null)
    {
        $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    /*****************************************************************************/
    public function api_call_delete($url, $username = null, $password = null)
    {
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    /************************** API CALL - END ***********************************/
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
