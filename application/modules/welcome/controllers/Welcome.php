<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';
        $this->data['menu_active_home']   = 'active';

        $this->load->library('library_module');
        $this->data['_is_allowed'] = $this->_is_allowed(array(role_admin, role_seller)) ? '' : 'hidden destroy';

        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('_footer.html', $this->data, true);

        $param_category['table']    = 'category';
        $category_all               = $this->model_generic->_get($param_category);
        $this->data['category_all'] = $category_all;
    }
    /*****************************************************************************/
    public function index()
    {

        // slider - begin
        $param_item['table'] = 'item';
        $slider_all          = $this->model_generic->_get($param_item, 7);
        foreach ($slider_all as $value) {
            // $value->item_image = file_exists('./files/images/' . controller . '/' . $value->item_image . '.png') ? controller . '/' . $value->item_image . '.png' : 'default-item.png';
            $value->item_price_format = number_format($value->item_price, 0, ',', '.');

            $param_image['table'] = 'image';
            $this->db->where('entity_table', 'item');
            $this->db->where('entity_id', $value->item_id);
            $value->item_image_main = 'default-item.png';
            $images                 = $this->model_generic->_get($param_image);
            // $value->image_all = $images;
            if ($images) {

                foreach ($images as $im_value) {
                    $item_image_main = $images[0]->image_path . '/thumbnail/' . $images[0]->image_filename . '_256.' . $images[0]->image_ext;
                }
                $value->item_image_main = $item_image_main;
            }

        }
        $this->data['slider_all'] = $slider_all;
        // opn($slider_all);exit();
        // slider - end

        /// top_categories - begin
        $param_category['table'] = 'category';
        $top_categories          = $this->model_generic->_get($param_category, 3);
        foreach ($top_categories as $value) {

            $value->category_image = file_exists('./files/images/category/' . $value->category_image . '.png') ? 'category/' . $value->category_image . '.png' : 'default-category.png';

            $category_thumbnail_default[0]['category_thumbnail_image'] = 'default-category.png';
            $category_thumbnail_default[1]['category_thumbnail_image'] = 'default-category.png';

            // $category_thumbnail_default = $mim;
            // opn($category_thumbnail_default);exit();

            $param['table'] = 'image';
            $this->db->where('entity_table', 'category');
            $this->db->where('entity_id', $value->category_id);
            $category_thumbnail = $this->model_generic->_get($param);
            if (!empty($category_thumbnail)) {
                foreach ($category_thumbnail as $ct_value) {
                    $ct_value->category_thumbnail_image = file_exists('./files/images/category/' . $ct_value->image_filename . '.' . $ct_value->image_ext) ? 'category/' . $ct_value->image_filename . '.' . $ct_value->image_ext : 'default-category.png';
                }
                $value->category_image_additional = $category_thumbnail;
            } else {
                $value->category_image_additional = $category_thumbnail_default;
            }
            // opn($image_of);exit();
        }
        $this->data['top_categories'] = $top_categories;
        // opn($top_categories);exit();
        /// top_categories - end

        $this->data['services']       = $this->parser->parse('_services.html', $this->data, true);
        $this->data['popular_brand']  = $this->parser->parse('_popular_brand.html', $this->data, true);
        $this->data['widget']         = $this->parser->parse('_widget.html', $this->data, true);
        $this->data['featured']       = $this->parser->parse('_featured.html', $this->data, true);
        $this->data['promo']          = $this->parser->parse('_promo.html', $this->data, true);
        $this->data['top_categories'] = $this->parser->parse('_top_categories.html', $this->data, true);
        $this->data['slider']         = $this->parser->parse('_slider.html', $this->data, true);
        $this->data['topbar']         = $this->parser->parse('_topbar.html', $this->data, true);
        $this->data['menu_desktop']   = $this->parser->parse('_menu_desktop.html', $this->data, true);
        $this->data['menu_mobile']    = $this->parser->parse('_menu_mobile.html', $this->data, true);
        $this->data['customizer']     = $this->parser->parse('_customizer.html', $this->data, true);
        $this->data['body']           = $this->parser->parse('_body.html', $this->data, true);
        $this->parser->parse('_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function email()
    {

        $this->load->library('email');
        $this->email->clear();
        $config['mailtype'] = "html";
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->from('admin@maheswara.net', 'Verification');
        // $this->email->from('email@example.com', 'Website');
        $list = array('web.sisesa@gmail.com', 'rixy_peace@yahoo.co.id');
        $this->email->to($list);
        // $this->email->reply_to($param_reservation['reservation_email']);
        // $data['reservation'] = array($param_reservation);
        // $data['today'] = date('r');
        $htmlMessage = 'este Test Email'; //$this->parser->parse('email.html', $data, true);
        $this->email->subject('Test: Email Verification');
        $this->email->message($htmlMessage);
        // opn($data);exit();
        // echo $htmlMessage;exit();

        if ($this->email->send()) {
            echo 'Your email was sent, thanks chamil.';
            // $this->model_generic->_insert($param_reservation);
            // redirect(base.'/'.controller.'/success');
        } else {
            show_error($this->email->print_debugger());exit();
        }

    }
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Welcome.php */
/* Location: ./application/controllers/Welcome.php */
