<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
        $this->data['treeview_menu'] = '';
        $this->passwordHasher        = new Hautelook\Phpass\PasswordHash(64, false);

    }
    /*****************************************************************************/
    public function index()
    {
        // super admin & staff = user
        // seller = seller
        // buyer = buyer

        if ($this->input->post()) {
            $param = $this->input->post();
            // opn($param);exit();
            $table_aktif = 'user';
            if (!isset($param['table_aktif'])) {
                $param_user['table'] = 'user';
                $this->db->where('user_email', $param['user_email']);
                $_cek_user = $this->model_generic->_cek($param_user);
                if ($_cek_user) {
                    $table_aktif = 'user';
                }
                $param_seller['table'] = 'seller';
                $this->db->where('seller_email', $param['user_email']);
                $_cek_seller = $this->model_generic->_cek($param_seller);
                if ($_cek_seller) {
                    $table_aktif = 'seller';
                }
                $param_member['table'] = 'member';
                $this->db->where('member_email', $param['user_email']);
                $_cek_member = $this->model_generic->_cek($param_member);
                if ($_cek_member) {
                    $table_aktif = 'member';
                }
            } else {
                $table_aktif = $param['table_aktif'];
            }
            // opn($param);exit();
            $table_aktif = 'user'; /// ini untuk baypass pakai table user aja

            $param_table_aktif['table'] = $table_aktif;
            $this->db->where($table_aktif . '_email', $param['user_email']);
            $user_info = $this->model_generic->_get($param_table_aktif);
            if ($user_info) {
                $passwordMatch = false;
                $user_id       = false;
                foreach ($user_info as $value) {
                    $user_id                  = $value->user_id;
                    $passwordMatch            = $this->passwordHasher->CheckPassword($param['user_password'], $value->{$table_aktif . '_password'});
                    $value->table_aktif       = $table_aktif;
                    $value->user_id           = $value->{$table_aktif . '_id'};
                    $value->user_avatar       = is_file('./files/images/' . $table_aktif . '/' . $value->{$table_aktif . '_avatar'}) ? $table_aktif . '/' . $value->{$table_aktif . '_avatar'} . '.png' : 'default.png';
                    $value->user_name_display = $value->{$table_aktif . '_name_display'};
                    $value->user_name_first   = $value->{$table_aktif . '_name_first'};
                    $value->user_name_last    = '';
                    switch ($table_aktif) {
                        default:
                        case 'user':

                            $param_user_role['table'] = 'user_role';
                            $this->db->where('user_id', $value->user_id);
                            $user_role     = $this->model_generic->_get($param_user_role);
                            $role_label_id = array();
                            foreach ($user_role as $ur_key => $ur_value) {
                                $role_label_id[] = $ur_value->role_label_id;
                            }
                            $value->user_role = $role_label_id ?: array(1);
                            break;
                        case 'seller':
                            $value->user_role = array(role_seller);
                            // $value->user_name_display = $value->{$table_aktif . '_name_display'};
                            // $value->user_name_first   = $value->{$table_aktif . '_name_first'};
                            // $value->user_name_last    = '';
                            break;
                        case 'member':
                            $value->user_role = array(role_member);
                            // $value->user_name_display = $value->{$table_aktif . '_name_display'};
                            // $value->user_name_first   = $value->{$table_aktif . '_name_display'};
                            // $value->user_name_last    = '';
                            break;
                    }
                }
                // opn($passwordMatch);
                // opn($user_info);exit();
                if ($passwordMatch !== false) {
                    $_SESSION['user_info']   = $user_info;
                    $_SESSION['table_aktif'] = $table_aktif;

                    $param_billing['table'] = 'billing';
                    $this->db->where('user_id', $user_id);
                    $cek_address = $this->model_generic->_cek($param_billing);
                    if ($cek_address) {
                        redirect(base);
                    } else {
                        redirect(base . '/account/address');
                    }
                } else {

                    echo '
                        <div class="alert alert-danger alert-dismissible text-center" >
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-warning"></i> Error!</h4>
                            Authentication Failed. <br>
                        </div>
                    ';
                    $this->parser->parse('login.html', $this->data, false);

                }
            } else {
                if ($this->_is_login) {
                    session_destroy();
                }
                $this->parser->parse('login.html', $this->data, false);
            }
            // opn($user_info);exit();

        } else {
            session_destroy();

            $this->parser->parse('login.html', $this->data, false);
        }

    }
    /*****************************************************************************/
    public function index_lama()
    {
        // opn(session_id());exit();

        if ($this->input->post()) {
            $param = $this->input->post();
            // opn($param);exit();
            // $user_password = md5(md5(md5(md5(md5(md5(md5(md5(md5(md5($param['user_password']))))))))));
            $param['table'] = 'user';
            // $where[0] = array('user', 'user_email', $param['user_email']);
            // $where[1] = array('user', 'user_password', $user_password);
            // $param['where'] = $where;
            $this->db->where('user_email', $param['user_email']);
            $this->db->or_where('user_name', $param['user_email']);
            $this->db->or_where('user_slug', $param['user_email']);
            // $this->db->where('user_password', $user_password);
            $user_info = $this->model_generic->_get($param);
            // opn($user_info);exit();
            $passwordMatch = false;
            $user_id       = '';
            foreach ($user_info as $key => $value) {
                $user_id                  = $value->user_id;
                $passwordMatch            = $this->passwordHasher->CheckPassword($param['user_password'], $value->user_password);
                $param_user_role['table'] = 'user_role';
                // $user_role_where[0] = array('user_role', 'user_id', $value->user_id);
                // $param_user_role['where'] = $user_role_where;
                $this->db->where('user_id', $value->user_id);
                $user_role     = $this->model_generic->_get($param_user_role);
                $role_label_id = array();
                foreach ($user_role as $ur_key => $ur_value) {
                    $role_label_id[] = $ur_value->role_label_id;
                }
                $value->user_role = $role_label_id ?: array(1);
                // $param['table'] = 'avatar';
                // $this->db->where('user_id', $value->user_id);
                // $this->db->where('avatar_disabled', 0);
                // $avatar       = $this->model_generic->_get($param);
                // $avatar_image = '';
                // foreach ($avatar as $key => $value) {
                //     $avatar_image = $value->avatar_name;

                // }
                $param_image['table'] = 'image';
                // $param_image['entity_table'] = 'user';
                // $param_image['entity_id'] = $user_id;
                $this->db->where('entity_table', 'user');
                $this->db->where('entity_id', $value->user_id);
                $image = $this->model_generic->_get($param_image);
                if ($image) {
                    $value->user_avatar = $image[0]->image_path . '/' . $image[0]->image_filename . '.' . $image[0]->image_ext;
                } else {
                    $value->user_avatar = 'default.png';
                }
                // $value->user_avatar = $avatar_image ?: 'default.png';
            }
            // opn($passwordMatch);exit();
            if ($passwordMatch) {

                $param_ci_session['table'] = 'ci_session';
                // $this->db->where('id', session_id());
                $this->db->where('user_id', $user_id);
                $_cek = $this->model_generic->_cek($param_ci_session);
                // opn($_cek);exit();
                if (1 == 1) {
                    // if (!$_cek) {
                    $this->db->where('id', session_id());
                    $param_ci_session['user_id'] = $user_id;
                    $this->model_generic->_update($param_ci_session);
                    $_SESSION['user_info'] = $user_info;
                    // echo '
                    // <div class="row">
                    //   <div class="col-sm-4 col-sm-offset-4 col-xs-12">
                    //     <div class="alert alert-success alert-dismissible">
                    //         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    //         <h4><i class="icon fa fa-warning"></i> Success!</h4>
                    //         Authentication Granted. <br>
                    //         Please wait while loading dashboard ...!
                    //         <i class="fa fa-fw fa-spinner fa-pulse"></i>
                    //     </div>
                    //   </div>
                    // </div>
                    // ';
                    // <meta http-equiv="refresh" content="0; url=' . base . '/dashboard" />
                    // redirect(base . '/dashboard');

                    if ($this->_is_allowed(array(role_admin))) {
                        redirect(base . '/dashboard');
                    } else {
                        redirect(base . '/seller');
                    }
                } else {
                    echo '
                        <div class="alert alert-danger alert-dismissible text-center" >
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-warning"></i> Error!</h4>
                            Authentication Failed. <br>
                            You are already logged in.
                        </div>
                    ';
                    // <meta http-equiv="refresh" content="0; url=' . base . '/dashboard" />
                }
            } else {
                echo '
                    <div class="alert alert-danger alert-dismissible text-center">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-warning"></i> Error!</h4>
                        Authentication Failed.
                    </div>
                ';
            }

        } else {

            // if ($this->_is_login) {
            //     redirect(base . '/logout');
            //     // $this->_goto_referer();
            // } else {
            //     // $this->parser->parse('login.html', $this->data, false);
            // }
            session_destroy();
            $this->parser->parse('login.html', $this->data, false);
        }

    }

}

/* End of file Login.php */
/* Location: ./application/controllers/Login.php */
