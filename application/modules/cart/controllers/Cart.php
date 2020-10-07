<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cart extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
        $this->data['_is_allowed'] = $this->_is_allowed(array(role_admin, role_seller)) ? '' : 'hidden destroy';

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        
        $param_category['table'] = 'category';
        $category_all            = $this->model_generic->_get($param_category);

        $this->data['category_all'] = $category_all;

        // $this->load->library('library_module');

        // $this->cart->destroy();
        // $data = array(
        //         'id'      => 'sku_123ABC',
        //         'qty'     => 1,
        //         'price'   => 39.95,
        //         'name'    => 'T-Shirt',
        //         'options' => array('Size' => 'L', 'Color' => 'Red')
        // );

        // $this->cart->insert($data);

        //
        $data = array(
            array(
                'id'      => 'sku_123ABC',
                'qty'     => 1,
                'price'   => 39.95,
                'name'    => 'T-Shirt',
                'options' => array(
                    array(
                        'option_name'=> 'Size',
                        'option_value'=> 'L' 
                    ),
                    array(

                        'option_name'=> 'Color',
                        'option_value'=> 'Red'  
                    ),
                ),
            ),
            array(
                'id'    => 'sku_567ZYX',
                'qty'   => 1,
                'price' => 9.95,
                'name'  => 'Coffee Mug',
                'options' => array()
            ),
            array(
                'id'    => 'sku_965QRS',
                'qty'   => 1,
                'price' => 29.95,
                'name'  => 'Shot Glass',
                'options' => array()
            ),
        );

        // opn($data);exit();

        // $this->cart->insert($data);

        $data = array(
            array(
                'rowid' => 'b99ccdf16028f015540f341130b6d8ec',
                'qty'   => 3,
            ),
            array(
                'rowid' => 'xw82g9q3r495893iajdh473990rikw23',
                'qty'   => 4,
            ),
            array(
                'rowid' => 'fh4kdkkkaoe30njgoe92rkdkkobec333',
                'qty'   => 2,
            ),
        );

        // $this->cart->update($data);

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

        // opn($_SESSION);exit();

        // opn($this->cart->contents());exit();
        $cart_content = $this->cart->contents();
        foreach ($cart_content as $value) {
            // opn($_SESSION['cart_contents'][$value['rowid']]['options']);//exit();
            if (!isset($_SESSION['cart_contents'][$value['rowid']]['options'])) {
                $_SESSION['cart_contents'][$value['rowid']]['options'] = array();
            }
            // $_SESSION['cart_contents'][$value['rowid']]['subtotal_format'] = number_format($_SESSION['cart_contents'][$value['rowid']]['subtotal'],2);
        }
        $this->data['cart_content'] = $cart_content;
        // opn($cart_content);exit();
        // $r = range(41, 100);
        // foreach ($r as $value) {
        //     print_r(md5($value));
        //     echo '<br>';
        // }
        // // opn($this->cart->get_item(md5('sku_567ZYX')));exit();
        // $total = $this->cart->total();
        // $this->data['total'] = $total;
        // $this->data['total_format'] = number_format($total,2);
        // opn($this->cart->total());exit();
        if ($cart_content) {
            $this->data['body'] = $this->parser->parse('cart.html', $this->data, true);
        }else{
            $this->data['body'] = $this->parser->parse('body.html', $this->data, true);

        }

        $this->parser->parse('welcome/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function add_item()
    {
        // if ($this->input->is_ajax_request()) {
        if ($this->input->post()) {
            $param = $this->input->post();
            $param['data']['options'] = array();
            $param['data']['member_id'] = $this->_user_id;
            // $param['data']['subtotal_format'] = number_format($param['data']['subtotal'],2);
            // echo json_encode($param);
            // $param = json_decode($param, true);
            $data = array($param['data']);
            // opn($data);exit();

            $this->cart->insert($data);

            $cart_content = $this->cart->contents();
            // opn($cart_content);exit();

            $res['cart_count'] = count($this->cart->contents());
            $res['cart_total'] = $this->cart->total() ;

            echo json_encode($res);

        }
        // }
    }
    /*****************************************************************************/
    function remove_item($rowid)
    {
        $data = array(
                'rowid' => $rowid,
                'qty'   => 0
        );

        $this->cart->update($data); 
        redirect(base.'/'.controller);
    }
    /*****************************************************************************/
    function clear_cart()
    {
        if ($this->_is_login) {
            $this->cart->destroy();
            redirect(base.'/'.controller);
        }
    }
    /*****************************************************************************/
    function update_cart()
    {
        // if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $param = $this->input->post();
                $param = json_decode($param['data'], true);
                // echo json_encode($param);
                // opn($param);exit();
                $this->cart->update($param);

                
            }
            
        // }
        
    }
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Cart.php */
/* Location: ./application/controllers/Cart.php */
