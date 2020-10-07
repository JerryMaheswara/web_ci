<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller {
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

	public function index()
	{
		if ($this->_is_allowed(array(role_admin, role_seller))) {
			if ($this->_is_allowed(array(role_seller))) {
				$this->db->where('seller_id', $this->_user_id);
			}
			$this->db->group_by('invoice');
			$this->db->select('shopping.*, member.user_name_first as member_name_first, member.user_name_last as member_name_last');
			$this->db->join('user member', 'member.user_id = shopping.member_id', 'left');
			$this->db->select('shopping.*, seller.user_name_first as seller_name_first, seller.user_name_last as seller_name_last');
			$this->db->join('user seller', 'seller.user_id = shopping.seller_id', 'left');
			$transaction_all = $this->db->get('shopping')->result();

			$nomor_urut = 1;
			$grand_total = 0;
			foreach ($transaction_all as $value) {
				// $nomor_urut += 1;
				$value->nomor_urut = $nomor_urut++;
				$this->db->where('invoice', $value->invoice);
				if ($this->_is_allowed(array(role_seller))) {
					$this->db->where('seller_id', $this->_user_id);
				}
				$transaction_each = $this->db->get('shopping')->result();
				$trans_total = 0;
				foreach ($transaction_each as $te_value) {
					$trans_total += $te_value->shopping_subtotal;
					
				}
				$value->trans_total = $trans_total;
				$value->shopping_created_format = date('d M Y', strtotime($value->shopping_created));
				$value->trans_total_format = number_format($trans_total);
				$grand_total += $trans_total;
			}
			$this->data['grand_total'] = $grand_total;
			$this->data['grand_total_format'] = number_format($grand_total);

			$this->data['transaction_all'] = $transaction_all;
			// opn($transaction_all);exit();
            $this->data['body'] = $this->parser->parse('report/transaksi.html', $this->data, true);

            $this->parser->parse('dashboard/_index.html', $this->data, false);

		}
	}

}

/* End of file Report.php */
/* Location: ./application/modules/report/controllers/Report.php */