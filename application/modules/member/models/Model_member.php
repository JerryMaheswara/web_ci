<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_member extends CI_Model
{
    /*****************************************************************************/
    /*****************************************************************************/

    public function _get_member($limit = null, $offset = null)
    {
        
        $param_member['table'] = 'user_role';
        $this->db->where('role_label_id', role_member);
        $this->db->join('user', 'user.user_id = user_role.user_id', 'left');
        $member                = $this->model_generic->_get($param_member, $limit, $offset);
        // opn($member);exit();
        return $member;
    }

    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/

}

/* End of file Model_member.php */
/* Location: ./application/modules/member/model/Model_member.php */
