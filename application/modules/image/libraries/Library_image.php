<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Library_image
{
    protected $ci;

    /************/
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('model_generic');
    }
    /************/
    public function refresh($param)
    {
        $this->ci->data['entity_table'] = $param['entity_table'];
        $this->ci->data['entity_id']    = $param['entity_id'];
        $param['table']                 = 'image';
        $this->ci->db->where('entity_table', $param['entity_table']);
        $this->ci->db->where('entity_id', $param['entity_id']);
        $image_of = $this->ci->model_generic->_get($param);
        foreach ($image_of as $key => $value) {
            $image_filename = explode('.', $value->image_filename);
            // opn($image_filename);exit();
            if (isset($value->image_ext)) {
                // $value->image_filename        = $image_filename[0] . '.' . $value->image_ext;
                // $value->image_filename_small  = $image_filename[0] . '_small' . '.' . $value->image_ext;
                // $value->image_filename_medium = $image_filename[0] . '_medium' . '.' . $value->image_ext;
                // $value->image_filename_large  = $image_filename[0] . '_large' . '.' . $value->image_ext;
            } else {
                // $value->image_filename = $image_filename[0].'.'.$value->image_ext;
                // $value->image_filename_small  = $image_filename[0] . '_small' . '.' . $image_filename[1];
                // $value->image_filename_medium = $image_filename[0] . '_medium' . '.' . $image_filename[1];
                // $value->image_filename_large  = $image_filename[0] . '_large' . '.' . $image_filename[1];
            }

        }
        // opn($image_of);exit();
        $this->ci->data['image_of']      = $image_of;
        $this->ci->data['image_display'] = $image_of ? '' : 'hidden destroy';
        return $this->ci->parser->parse('image/refresh.html', $this->ci->data, true);
    }
    /************/
    public function add_image($param)
    {
        // opn($param);exit();
        $this->ci->data['entity_table'] = $param['entity_table'];
        $this->ci->data['entity_id']    = $param['entity_id'];
        return $this->ci->parser->parse('image/image.html', $this->ci->data, true);
    }
    /************/

}

/* End of file Library_image.php */
/* Location: ./application/libraries/Library_image.php */
