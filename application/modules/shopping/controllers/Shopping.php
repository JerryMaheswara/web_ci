<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shopping extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        $this->load->library('library_module');

        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);

    }
    /*****************************************************************************/
    /*****************************************************************************/
    public function order()
    {
        if ($this->_is_allowed(array(role_seller, role_admin))) {
            $param_shopping['table'] = 'shopping';
            $this->db->where('shopping.seller_id', $this->_user_id);
            // $total_rows = $this->model_generic->_count($param_shopping);
            $this->db->join('item', 'item.item_id = shopping.item_id', 'left');
            $this->db->join('user', 'user.user_id = shopping.member_id', 'left');
            $this->db->join('billing', 'billing.user_id = shopping.member_id', 'left');
            $this->db->select('billing.*');
            $this->db->select('shopping.*');
            $this->db->select('item.*');
            $this->db->select('user.user_name_first as member_name_first');
            $this->db->select('user.user_name_last as member_name_last');
            $shopping_all   = $this->model_generic->_get($param_shopping);
            $shopping_total = 0;
            foreach ($shopping_all as $value) {
                $shopping_total += $value->shopping_subtotal;
                $value->item_price_format        = number_format($value->item_price);
                $value->shopping_subtotal_format = number_format($value->shopping_subtotal);
                $value->billing_info             = $value->billing_address . ', '
                . $value->billing_subdistrict . ', '
                . $value->billing_district . ', '
                . $value->billing_postal_code;
                $status = $value->shopping_status;
                switch ($status) {
                    case 1:
                        $value->status_color = 'danger';
                        $value->status_icon  = 'cube';
                        $value->status_name  = 'Accepted';
                        break;
                    case 2:
                        $value->status_color = 'warning';
                        $value->status_icon  = 'hourglass-half';
                        $value->status_name  = 'In Progress';
                        break;
                    case 3:
                        $value->status_color = 'warning';
                        $value->status_icon  = 'truck';
                        $value->status_name  = 'Shipped';
                        break;
                    case 4:
                        $value->status_color = 'warning';
                        $value->status_icon  = 'gift';
                        $value->status_name  = 'Delivered';
                        break;
                    case 5:
                        $value->status_color = 'success';
                        $value->status_icon  = 'check';
                        $value->status_name  = 'Completed';
                        break;
                }
            }
            $this->data['shopping_total_format'] = number_format($shopping_total);
            // opn($shopping_all);exit();

            $this->data['shopping_all'] = $shopping_all;
            $this->data['body']         = $this->parser->parse('shopping.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function status_update()
    {
        if ($this->input->post()) {
            $param_update          = $this->input->post();
            $param_update['table'] = 'shopping';
            $this->db->where('shopping_id', $param_update['shopping_id']);
            $cek = $this->model_generic->_count($param_update);
            if ($cek) {
                $this->db->where('shopping_id', $param_update['shopping_id']);
                $this->model_generic->_update($param_update);

                $param_status['table']              = 'shopping_status';
                $param_status['shopping_id']        = $param_update['shopping_id'];
                $param_status['shopping_status']    = $param_update['shopping_status'];
                $param_status['shopping_status_by'] = $this->_user_id;
                $this->model_generic->_insert($param_status);

                redirect(base . '/' . controller . '/order');
            }
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Shopping.php */
/* Location: ./application/controllers/Shopping.php */
