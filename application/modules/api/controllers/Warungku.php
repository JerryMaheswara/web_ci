<?php

class Warungku extends REST_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('model_generic');
        $this->passwordHasher = new Hautelook\Phpass\PasswordHash(64, false);

        header("access-control-allow-origin: *");
        $this->limit  = 12;
        $this->offset = 0;
        // opn($this->limit);exit();
    }

    /*****************************************************************************/
    public function login_post()
    {
        if ($this->post('user_email')) {

            $this->db->join('user_role', 'user_role.user_id = user.user_id', 'left');
            $this->db->join('role_label', 'role_label.role_label_id = user_role.role_label_id', 'left');
            $this->db->where('user_email', $this->post('user_email'));
            $user_info              = $this->db->get('user')->row();
            $user_info->user_avatar = is_file('./files/images/user/' . $user_info->user_avatar) ? base . '/files/images/user/' . $user_info->user_avatar . '.png' : base . '/files/images/default.png';
            // opn($user_info);exit();
            $res['user_id']     = $user_info->user_id;
            $res['user_name']   = $user_info->user_name_first . ' ' . $user_info->user_name_last;
            $res['user_avatar'] = $user_info->user_avatar;
            $res['user_level']  = $user_info->role_label_name;
            $passwordMatch      = $this->passwordHasher->CheckPassword($this->post('user_password'), $user_info->user_password);
            if ($passwordMatch) {
                $this->response($res, 200);
            } else {
                $this->response('Invalid email or password, please try again..!!', 200);
            }
        }
    }
    /*****************************************************************************/
    public function shopping_post()
    {
        // opn($this->post());exit();
        if ($this->post()) {

            $invoice = strtoupper(uniqid());
            // $param   = $this->post();
            // opn($param);exit();
            $cart = array($this->post());
            // opn($cart);exit();
            foreach ($cart as $key => $value) {
                // $cart[$key]['invoice'] = $invoice;

                $this->db->where('user_id', $value['member_id']);
                $billing = $this->db->get('billing')->row();
                // opn($billing);exit();
                $billing_id = $billing->billing_id;

                // $param_shopping['table']      = 'shopping';
                $param_shopping['billing_id'] = $billing_id;
                // $param_shopping['shipping_id']       = $shipping_id;
                $param_shopping['invoice']           = $invoice;
                $param_shopping['shipping_method']   = 'COD';
                $param_shopping['item_id']           = $value['id'];
                $param_shopping['item_price']        = $value['price'];
                $param_shopping['item_qty']          = $value['qty'];
                $param_shopping['item_options']      = json_encode($value['options']);
                $param_shopping['shopping_subtotal'] = $value['subtotal'];
                $param_shopping['seller_id']         = $value['seller_id'];
                $param_shopping['member_id']         = $value['member_id'];
                // opn($param_shopping);exit();
                // $this->model_generic->_insert($param_shopping);
                $this->db->insert('shopping', $param_shopping);
                $shopping_id = $this->db->insert_id();

                ////// notifikasi
                // $param_notif['table']            = 'notif';
                $param_notif['notif_table_name'] = 'shopping';
                $param_notif['notif_table_id']   = $shopping_id;
                $param_notif['notif_from']       = $value['member_id'];
                $param_notif['notif_to']         = $value['seller_id'];
                // $this->model_generic->_insert($param_notif);
                $this->db->insert('notif', $param_notif);

                /////// status
                // $param_status['table']              = 'shopping_status';
                $param_status['shopping_id']        = $shopping_id;
                $param_status['shopping_status']    = 1;
                $param_status['shopping_status_by'] = $value['member_id'];
                // $this->model_generic->_insert($param_status);
                $this->db->insert('shopping_status', $param_status);

            }
            // $param['cart'] = $cart;
            // $param['member_id'] = $this->_user_id;

            // $param_billing['table']  = 'billing';
            // $param_shipping['table'] = 'shipping';

            // opn($param);exit();
            // $total_rows = $this->model_generic->_count($param);
            $this->response('Shopping Succesfull...!', 200);
        }

    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function toko_get()
    {
        if ($this->get('id')) {
            $this->db->where('billing_id', $this->get('id'));
        }
        if ($this->get('billing_id')) {
            $this->db->where('billing_id', $this->get('billing_id'));
        }

        $this->db->where('role_label_id', role_seller);

        if ($this->get('limit')) {
            $this->limit = $this->get('limit');
        }
        if ($this->get('offset')) {
            $this->offset = $this->get('offset');
        }



        $toko_all = $this->db->get('billing', $this->limit, $this->offset)->result();
        foreach ($toko_all as $value) {
            $value->nama_toko = $value->billing_name_first;
            $this->db->where('user_id', $value->user_id);
            $item_toko = $this->db->get('item')->result();
            foreach ($item_toko as $it_value) {
                $it_value->image_ori = base . '/files/images/default-item.png';

                $this->db->where('entity_table', 'item');
                $this->db->where('entity_id', $it_value->item_id);
                $images = $this->db->get('image')->result();
                if ($images) {
                    foreach ($images as $im_key => $im_value) {
                        if (is_file('./files/images/item/' . $images[0]->image_filename . '.' . $images[0]->image_ext)) {
                            $it_value->image_ori = base . '/files/images/item/' . $images[0]->image_filename . '.' . $images[0]->image_ext;
                        }
                    }
                }
            }
            $value->item_toko = $item_toko;
        }
        $this->response($toko_all, 200);

    }
    /*****************************************************************************/

    /*****************************************************************************/
    public function item_get()
    {
        if ($this->get('id')) {
            $this->db->where('item_id', $this->get('id'));
        }
        if ($this->get('item_id')) {
            $this->db->where('item_id', $this->get('item_id'));
        }

        if ($this->input->get('limit')) {
            $this->limit = $this->input->get('limit');
        }
        if ($this->input->get('offset')) {
            $this->offset = $this->input->get('offset');
        }
        $this->db->join('unit', 'unit.unit_id = item.unit_id', 'left');
        $item_all = $this->db->get('item', $this->limit, $this->offset)->result();
        foreach ($item_all as $value) {

            $this->db->where('user_id', $value->user_id);
            $this->db->where('role_label_id', role_seller);
            $user_role        = $this->db->get('user_role')->result();
            $value->seller_id = null;
            if ($user_role) {
                foreach ($user_role as $ur_value) {
                    $value->seller_id = $value->user_id;
                }
            }
            $value->image_ori = base . '/files/images/default-item.png';

            $this->db->where('entity_table', 'item');
            $this->db->where('entity_id', $value->item_id);
            $images = $this->db->get('image')->result();
            if ($images) {
                foreach ($images as $im_key => $im_value) {
                    if (is_file('./files/images/item/' . $images[0]->image_filename . '.' . $images[0]->image_ext)) {
                        $value->image_ori = base . '/files/images/item/' . $images[0]->image_filename . '.' . $images[0]->image_ext;
                    }
                }
            }
        }
        $this->response($item_all, 200);
    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function transaksi_get()
    {
        if ($this->get('id') || $this->get('seller_id')) {
            if ($this->get('id')) {
                $this->db->where('seller_id', $this->get('id'));
            }
            if ($this->get('seller_id')) {
                $this->db->where('seller_id', $this->get('seller_id'));
            }
            $shopping = $this->db->get('shopping')->result();
            foreach ($shopping as $value) {
                $this->db->where('shopping_id', $value->shopping_id);
                $this->db->order_by('shopping_status_created', 'desc');
                $shopping_status = $this->db->get('shopping_status', 1)->result();
            }
            $this->response($shopping, 200);
        }else{
        $this->response('seller_id is required', 200);

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
