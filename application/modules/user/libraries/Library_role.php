<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Library_role
{
    protected $ci;

    public function __construct()
    {
        $this->ci = &get_instance();

        $this->ci->_db_ = isset($_SESSION['_db_']) ? $_SESSION['_db_'] : $this->ci->db->database;

        $param_role_label['table'] = $this->ci->_db_ . '.role_label';
        $role_label                = $this->ci->model_generic->_get($param_role_label);
        foreach ($role_label as $key => $value) {
            if (!defined($value->role_label_slug)) {
                define('role_' . $value->role_label_slug, $value->role_label_id);
            }
        }
        // opn($role_label);exit();
        if (!defined('role_user')) {
            define('role_user', 1);
        }
        if (!defined('role_member')) {
            define('role_member', 1);
        }
        if (!defined('role_admin')) {
            define('role_admin', 2);
        }
    }

}

/* End of file Library_role.php */
/* Location: ./application/modules/user/libraries/Library_role.php */
