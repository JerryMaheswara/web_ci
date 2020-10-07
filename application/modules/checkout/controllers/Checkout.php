<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checkout extends MY_Controller
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
        $this->data['body'] = $this->parser->parse('checkout.html', $this->data, true);
        $this->parser->parse('welcome/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function address()
    {
        if ($this->_is_login) {
            if ($this->input->post()) {
                $invoice = strtoupper(uniqid());
                $param   = $this->input->post();
                // opn($param);exit();
                $cart = $this->cart->contents();
                // opn($cart);//exit();
                foreach ($cart as $key => $value) {
                    // $cart[$key]['invoice'] = $invoice;

                    $this->db->where('user_id', $this->_user_id);
                    $billing = $this->db->get('billing')->row();
                    // opn($billing);exit();
                    $billing_id = $billing->billing_id;

                    $param_shopping['table']      = 'shopping';
                    $param_shopping['billing_id'] = $billing_id;
                    // $param_shopping['shipping_id']       = $shipping_id;
                    $param_shopping['invoice']           = $invoice;
                    $param_shopping['shipping_method']   = $param['shipping_method'];
                    $param_shopping['item_id']           = $value['id'];
                    $param_shopping['item_price']        = $value['price'];
                    $param_shopping['item_qty']          = $value['qty'];
                    $param_shopping['item_options']      = json_encode($value['options']);
                    $param_shopping['shopping_subtotal'] = $value['subtotal'];
                    $param_shopping['seller_id']         = $value['seller_id'];
                    $param_shopping['member_id']         = $value['member_id'];
                    // opn($param_shopping);exit();
                    $this->model_generic->_insert($param_shopping);
                    $shopping_id = $this->db->insert_id();

                    ////// notifikasi
                    $param_notif['table']            = 'notif';
                    $param_notif['notif_table_name'] = 'shopping';
                    $param_notif['notif_table_id']   = $shopping_id;
                    $param_notif['notif_from']       = $value['member_id'];
                    $param_notif['notif_to']         = $value['seller_id'];
                    $this->model_generic->_insert($param_notif);

                    /////// status
                    $param_status['table']              = 'shopping_status';
                    $param_status['shopping_id']        = $shopping_id;
                    $param_status['shopping_status']    = 1;
                    $param_status['shopping_status_by'] = $value['member_id'];
                    $this->model_generic->_insert($param_status);

                }
                // $param['cart'] = $cart;
                // $param['member_id'] = $this->_user_id;

                // $param_billing['table']  = 'billing';
                // $param_shipping['table'] = 'shipping';

                // opn($param);exit();
                // $total_rows = $this->model_generic->_count($param);

                redirect(base . '/' . controller . '/complete');
            } else {
                $this->_set_referer();
                $param_billing['table'] = 'billing';
                $this->db->join('user', 'user.user_id = billing.user_id', 'left');
                $this->db->where('billing.user_id', $this->_user_id);
                $account_address               = $this->model_generic->_get($param_billing);
                $this->data['account_address'] = $account_address;
                // opn($account_address);exit();

                $this->data['sidebar_kanan'] = $this->parser->parse('sidebar_kanan.html', $this->data, true);
                $this->data['body']          = $this->parser->parse('address.html', $this->data, true);
                $this->parser->parse('welcome/_index.html', $this->data, false);
            }
        }
    }
    /*****************************************************************************/
    public function shipping()
    {
        if ($this->input->post()) {
            redirect(base . '/' . controller . '/payment');
        }
        $this->data['body'] = $this->parser->parse('shipping.html', $this->data, true);
        $this->parser->parse('welcome/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function payment()
    {
        if ($this->input->post()) {
            redirect(base . '/' . controller . '/review');
        }
        $this->data['body'] = $this->parser->parse('payment.html', $this->data, true);
        $this->parser->parse('welcome/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function review()
    {
        if ($this->input->post()) {
            redirect(base . '/' . controller . '/complete');
        }

        $this->data['body'] = $this->parser->parse('review.html', $this->data, true);
        $this->parser->parse('welcome/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function complete()
    {
        $this->cart->destroy();
        if ($this->_is_login) {
            $this->data['body'] = $this->parser->parse('complete.html', $this->data, true);
            $this->parser->parse('welcome/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
}

/* End of file Checkout.php */
/* Location: ./application/controllers/Checkout.php */
