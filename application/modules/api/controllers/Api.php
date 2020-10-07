<?php

class Api extends REST_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_generic');
        $this->passwordHasher = new Hautelook\Phpass\PasswordHash(64, false);

        header("access-control-allow-origin: *");
    }

    /*****************************************************************************/
    public function rate_get()
    {
        $this->db->select('*');
        $this->db->join('currency as from', 'from.currency_id = exchange_rate.currency_id_from', 'left');
        $this->db->select('from.currency_code as currency_code_from');
        $this->db->select('from.currency_symbol as currency_symbol_from');
        $this->db->select('from.currency_spelling as currency_spelling_from');
        $this->db->select('from.country_name as country_name_from');
        $this->db->join('currency as to', 'to.currency_id = exchange_rate.currency_id_to', 'left');
        $this->db->select('to.currency_code as currency_code_to');
        $this->db->select('to.currency_symbol as currency_symbol_to');
        $this->db->select('to.currency_spelling as currency_spelling_to');
        $this->db->select('to.country_name as country_name_to');

        $param_exchange_rate['table'] = 'exchange_rate';

        if ($this->get('id')) {
            $this->db->where('exchange_rate_id', $this->get('id'));
        }
        $exchange_rate = $this->model_generic->_get($param_exchange_rate);
        if ($exchange_rate) {
            foreach ($exchange_rate as $key => $value) {
                $value->flag_to   = strtolower($value->country_name_to);
                $value->flag_from = strtolower($value->country_name_from);
            }
            $this->response($exchange_rate, 200);
        } else {
            $this->response(null, 400);

        }
    }
    /*****************************************************************************/
    public function branch_get()
    {

        $param_branch_member['table'] = 'branch_member';

        if ($this->get('id')) {
            $this->db->where('branch_member_id', $this->get('id'));
        }
        if ($this->get('user_id')) {
            $this->db->where('user_id', $this->get('user_id'));
        }
        $_cek = $this->model_generic->_cek($param_branch_member);
        if ($_cek) {
            if ($this->get('id')) {
                $this->db->where('branch_member_id', $this->get('id'));
            }
            if ($this->get('user_id')) {
                $this->db->where('user_id', $this->get('user_id'));
            }

            $branch_member = $this->model_generic->_get($param_branch_member);
            $this->response($branch_member, 200);
        } else {
            $this->response(null, 400);

        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function user_get()
    {

        if ($this->get('id') || $this->get('user_id')) {

            if ($this->get('id')) {
                $this->db->where('user_id', $this->get('id'));
            }
            if ($this->get('user_id')) {
                $this->db->where('user_id', $this->get('user_id'));
            }

            $param['table'] = 'user';
            // $this->db->where('user_id', $this->get('id'));
            // $this->db->or_where('user_id', $this->get('user_id'));
            // $this->db->select('user_id');
            // $this->db->select('user_name');
            // $this->db->select('user_given_name');
            // $this->db->select('user_family_name');
            // $this->db->select('user_email');
            // $this->db->select('user_nik_expired');
            $user = $this->model_generic->_get($param);
            foreach ($user as $key => $value) {
                unset($value->user_password);
                unset($value->user_slug);
                unset($value->user_verification_code);
                unset($value->user_security_pin);
                unset($value->nomor);
                unset($value->nomor_user);
                unset($value->user_team);
                unset($value->user_message);
                unset($value->country_id);
                unset($value->currency_id);
                unset($value->language_id);
                unset($value->user_signature);
                unset($value->user_blood);
                unset($value->user_company_name);
                unset($value->user_facebook);
                unset($value->user_twitter);
                unset($value->user_type);
                unset($value->user_theme);
                unset($value->user_signature_format);
            }

            if ($user) {
                $this->response($user, 200); // 200 being the HTTP response code
            } else {
                $this->response('null', 404);
            }

        } else {
            $this->response(null, 400);
        }

        // if (!$this->get('id') || !$this->get('user_id')) {
        //     $this->response(null, 400);
        // }

    }

    /*****************************************************************************/
    public function login_post()
    {
        if ($this->post()) {
            $param          = $this->post();
            $param['table'] = 'user';
            $this->db->where('user_email', $param['user_email']);
            $this->db->or_where('user_name', $param['user_email']);
            $user_info = $this->model_generic->_get($param, 1);

            $passwordMatch = array();
            $user_id       = null;
            foreach ($user_info as $value) {
                $passwordMatch[$key] = $this->passwordHasher->CheckPassword($param['user_password'], $value->user_password);
                $user_id             = $value->user_id;

                unset($value->user_password);
                unset($value->user_slug);
                unset($value->user_verification_code);
                unset($value->user_security_pin);
                unset($value->nomor);
                unset($value->nomor_user);
                unset($value->user_team);
                unset($value->user_message);
                unset($value->country_id);
                unset($value->currency_id);
                unset($value->language_id);
                unset($value->user_signature);
                unset($value->user_blood);
                unset($value->user_company_name);
                unset($value->user_facebook);
                unset($value->user_twitter);
                unset($value->user_type);
                unset($value->user_theme);
                unset($value->user_signature_format);

            }
            $res['user_id'] = $user_id;
            if ($passwordMatch) {
                $this->response($user_id, 200);
            } else {
                $this->response('Error, User not found', 200);
            }
            // $this->response($user_id, 200);

        }
    }
    /*****************************************************************************/
    public function recipients_get()
    {
        ////////// user recipient
        if ($this->get('user_id')) {
            $this->db->where('user_id', $this->get('user_id'));
        }
        ////////// detail recipient
        if ($this->get('id')) {
            $this->db->where('bank_account_id', $this->get('id'));
        }
        $param_bank_account['table'] = 'bank_account';
        $_cek                        = $this->model_generic->_cek($param_bank_account);
        if ($_cek) {
            ////////// user recipient
            if ($this->get('user_id')) {
                $this->db->where('user_id', $this->get('user_id'));
            }
            ////////// detail recipient
            if ($this->get('id')) {
                $this->db->where('bank_account_id', $this->get('id'));
            }
            $this->db->join('bank', 'bank.bank_id = bank_account.bank_id', 'left');
            $bank_account = $this->model_generic->_get($param_bank_account);
            foreach ($bank_account as $value) {
                unset($value->bank_account_email);

            }
            $this->response($bank_account, 200);
        } else {
            $this->response('Error! Recipient not found.', 400);
        }

    }
    /*****************************************************************************/
    function purpose_get()
    {

        $param_transaction_purpose['table'] = 'transaction_purpose';
        $transaction_purpose = $this->model_generic->_get($param_transaction_purpose);

        if ($transaction_purpose) {

            $this->response($transaction_purpose, 200);
        } else {
            $this->response(null, 400);
        }
    }
    /*****************************************************************************/
    function transaction_post()
    {
        if ($this->post()) {
            $param_transaction = $this->post();
            $param_transaction['table'] = 'transaction';

            
            if ($param['transaction_purpose_id'] == 'other') {
                $param_transaction_purpose['table']                    = 'transaction_purpose';
                $param_transaction_purpose['transaction_purpose_name'] = $param['transaction_purpose_name'];
                $transaction_purpose                                   = $this->model_generic->_insert($param_transaction_purpose);
                $transaction_purpose_id                                = $this->db->insert_id();

                $param['transaction_purpose_id'] = $transaction_purpose_id;
            }

            if (isset($param_transaction['transaction_id'])) {
                $transaction_id = $param_transaction['transaction_id'];
                $this->db->where('transaction_id', $transaction_id);
                $this->model_generic->_update($param_transaction);
            }else{

                $param_transaction['transaction_invoice'] = 'T' . sprintf("%'.011d", time());
                $this->model_generic->_insert($param_transaction);
                $transaction_id = $this->db->insert_id();
            }

            $param_transaction_status['table']                     = 'transaction_status';
            $param_transaction_status['transaction_id']            = $transaction_id;
            $param_transaction_status['transaction_status_by']     = $this->_user_id;
            $param_transaction_status['transaction_sender']        = $param_transaction_status['user_id'];
            $param_transaction_status['transaction_status_number'] = 1;
            $param_transaction_status['transaction_status_note']   = $param['transaction_comment'];
            $this->model_generic->_insert($param_transaction_status);


            $this->db->where('transaction_id', $transaction_id);
            $transaction = $this->model_generic->_get($param_transaction);
            if ($transaction) {
                
                $this->response($transaction, 200);
            }else{
                $this->response('Error: Transaction failed!', 200);

            }
            
        }else{
            $this->response($transaction, 200);
        }
    }
    /*****************************************************************************/
    function transaction_get()
    {
        if ($this->get()) {
            $transaction = array();
            $param_transaction['table'] = 'transaction';
            if ($this->get('user_id')) {
                $this->db->where('user_id', $this->get('user_id'));
            $transaction = $this->model_generic->_get($param_transaction);
            }
            if ($this->get('user_id') && $this->get('id')) {
                $this->db->where('user_id', $this->get('user_id'));
                $this->db->where('transaction_id', $this->get('id'));
            $transaction = $this->model_generic->_get($param_transaction);
            }



            if ($transaction) {
                
                $this->response($transaction, 200);
            } else {
                $this->response(null, 400);
            }
        }else{
                $this->response('Error: Required user_id or id of transaction', 200);

        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function user_post()
    {
        // $this->response($this->post(), 200);

        $param_user = $this->post();
        // $param_user['user_slug'] = slug($param_user['user_name'], '_');
        if (isset($param_user['user_password'])) {
            // $param_user['user_password'] = md5(md5(md5(md5(md5(md5(md5(md5(md5(md5($param_user['user_password']))))))))));
            $param_user['user_password'] = $this->passwordHasher->HashPassword($param_user['user_password']);
        }
        $param_user['user_verification_code'] = md5(base64_encode(md5($param_user['user_email'])));
        $param_user['user_slug']              = slug($param_user['user_given_name'] . ' ' . $param_user['user_family_name'], '_');

        $ugn = strtoupper(substr($param_user['user_given_name'], 0, 2));
        $ufn = strtoupper(substr($param_user['user_family_name'], 0, 2));
        $ubd = substr($param_user['user_birthday'], -5);
        $ubd = explode('-', $ubd);
        $ubd = $ubd[1] . $ubd[0];

        $param_user['user_name'] = $ugn . $ufn . $ubd;

        $param_user['table'] = 'user';
        $this->db->where('user_id', $param_user['user_id']);
        $_cek = $this->model_generic->_cek($param_user);
        if ($_cek) {
            $result = $this->model_generic->_update($param_user);
        } else {
            $result = $this->model_generic->_insert($param_user);
        }

        // $result                          = $this->model_user->_set_user($this->post('user_id'), $param);
        if ($result === false) {
            $this->response(array('status' => 'failed'),200);
        } else {
            $this->response(array('status' => 'success'),200);
        }

    }

    /*****************************************************************************/
    /*****************************************************************************/
    public function users_count_get()
    {
        // $this->response($this->get(),200);
        $param['q']      = $this->get('q');
        $param['limit']  = $this->get('limit');
        $param['offset'] = $this->get('offset');
        $param['type']   = $this->get('type');

        $param_user['table'] = 'user';
        $_count              = $this->model_generic->_count($param_user);

        // $users = $this->model_user->_get_users_count($param);
        $last = $this->db->last_query();

        // $this->response($last, 200);
        if ($_count) {
            $this->response($_count, 200);
        } else {
            $this->response(0, 404);
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function user_delete($id)
    {
        $param['table'] = 'user';

        $param_user['table'] = 'user';
        $this->db->where('user_id', $id);
        $_cek = $this->model_generic->_cek($param_user);
        if ($_cek) {
            $this->db->where('user_id', $id);
            $this->model_generic->_del($param);
            $this->response('Delete Success', 200);
        } else {
            $this->response(array('status' => 'Delete failed! User not found.'));
        }

    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function user_put()
    {
        $this->response($this->post(), 200);
    }
    /*****************************************************************************/
}
