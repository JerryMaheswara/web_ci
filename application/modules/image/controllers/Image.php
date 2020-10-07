<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Image extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();
        $this->load->library('library_image');
        $this->load->model('model_image');
    }
    /*****************************************************************************/
    public function index()
    {
        $entity_table = 'room_class';
        $entity_id    = '1';
        // $this->data['entity_table'] = $entity_table;
        // $this->data['entity_id']    = $entity_id;

        $param['entity_table'] = $entity_table;
        $param['entity_id']    = $entity_id;

        $this->data['image_upload'] = $this->library_image->add_image($param);

        // $this->data['content'] = $this->parser->parse('admin/news.html', $this->data, true);
        // $this->_body           = $this->parser->parse('image.html', $this->data, true);
        $this->data['header'] = '';
        $this->data['footer'] = '';
        $this->data['body']   = $this->parser->parse('image.html', $this->data, true);
        $this->parser->parse('dashboard/_index.html', $this->data, false);

    }
    /*****************************************************************************/
    public function delete()
    {
        // if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->is_ajax_request()) {
                if ($this->input->post()) {
                    $param = $this->input->post();
                    // opn($param);exit();
                    $param['table'] = 'image';
                    $this->db->where('image_id', $param['image_id']);
                    $this->model_generic->_del($param);

                    $path = BASE . '/files/images/' . $param['image_path'];
                    unlink($path . '/' . $param['image_filename'] .'.'.$param['image_ext']); // hapus file utama
                    $image_filename = explode('.', $param['image_filename']);
                    $thumbnails     = glob($path . '/thumbnail/' . $image_filename[0] . '*');
                    array_walk($thumbnails, function ($thumbnail) {
                        unlink($thumbnail); // hapus file thumbnail
                    });

                }
            }
        // }
    }
    /*****************************************************************************/
    public function refresh()
    {
        // if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->is_ajax_request()) {
                if ($this->input->post()) {
                    $param          = $this->input->post();
                    $param['table'] = 'image';
                    echo $this->library_image->refresh($param);
                }
            }
        // }
    }
    /*****************************************************************************/
    public function upload()
    {
        // if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->is_ajax_request()) {
                if ($this->input->post()) {
                    $param = $this->input->post();
                    // opn($param);exit();
                    $upload_path = './files/images/' . $param['entity_table'];
                    $thumbnail   = './files/images/' . $param['entity_table'] . '/thumbnail';
                    if (false == is_dir($upload_path)) {
                        mkdir($upload_path);
                        chmod($upload_path, 0777);
                        if (false == is_dir($thumbnail)) {
                            mkdir($thumbnail);
                            chmod($thumbnail, 0777);
                        }
                    } else {
                        chmod($upload_path, 0777);
                        if (false == is_dir($thumbnail)) {
                            mkdir($thumbnail);
                            chmod($thumbnail, 0777);
                        }
                    }

                    if ($_FILES) {
                        // opn($_FILES);exit();
                        foreach ($_FILES as $key => $value) {
                            if (is_array($value['name'])) {
                                foreach ($value['name'] as $n_key => $n_value) {
                                    if (!$value['error'][$n_key]) {
                                        $_FILES[$key]['name']       = $value['name'][$n_key];
                                        $_FILES[$key]['type']       = $value['type'][$n_key];
                                        $_FILES[$key]['tmp_name']   = $value['tmp_name'][$n_key];
                                        $_FILES[$key]['error']      = $value['error'][$n_key];
                                        $_FILES[$key]['size']       = $value['size'][$n_key];
                                        $image_ext                  = strtolower(pathinfo($value['name'][$n_key], PATHINFO_EXTENSION));
                                        $config['upload_path']      = $upload_path;
                                        $config['allowed_types']    = 'gif|jpg|png';
                                        $config['file_name']        = 'i' . sprintf("%'.06d", $param['entity_id']) . md5(time('now'));
                                        $config['overwrite']        = false;
                                        $config['file_ext_tolower'] = true;

                                        $this->load->library('upload');
                                        $this->upload->initialize($config);
                                        if (!$this->upload->do_upload($key)) {
                                            echo $this->upload->display_errors();
                                        } else {
                                            $upload_data = $this->upload->data();
                                            // opn($upload_data);
                                            $param_image['table']          = 'image';
                                            $param_image['entity_table']   = $param['entity_table'];
                                            $param_image['entity_id']      = $param['entity_id'];
                                            $param_image['image_path']     = $param['entity_table'];
                                            $param_image['image_filename'] = $upload_data['raw_name'];
                                            $param_image['image_size']     = $upload_data['file_size'];
                                            $param_image['image_width']    = $upload_data['image_width'];
                                            $param_image['image_height']   = $upload_data['image_height'];
                                            $param_image['image_mime']     = $upload_data['file_type'];
                                            $param_image['image_ext']      = $image_ext;
                                            $param_image['image_id']       = '';
                                            $param_image['user_id']        = $this->_user_id;
                                            // resize & crop
                                            $resize_param['upload_data']      = $upload_data;

                                            $resize_param['size']             = 'landscape_large';
                                            $resize_param['new_image_width']  = 960;
                                            $resize_param['new_image_height'] = 640;
                                            $this->model_image->_resize($resize_param);
                                            $resize_param['size']             = 'landscape_medium';
                                            $resize_param['new_image_width']  = 555;
                                            $resize_param['new_image_height'] = 370;
                                            $this->model_image->_resize($resize_param);
                                            $resize_param['size']             = 'landscape_small';
                                            $resize_param['new_image_width']  = 279;
                                            $resize_param['new_image_height'] = 185;
                                            $this->model_image->_resize($resize_param);

                                            $resize_param['size']             = 'portrait_large';
                                            $resize_param['new_image_width']  = 640;
                                            $resize_param['new_image_height'] = 960;
                                            $this->model_image->_resize($resize_param);
                                            $resize_param['size']             = 'portrait_medium';
                                            $resize_param['new_image_width']  = 370;
                                            $resize_param['new_image_height'] = 555;
                                            $this->model_image->_resize($resize_param);
                                            $resize_param['size']             = 'portrait_small';
                                            $resize_param['new_image_width']  = 185;
                                            $resize_param['new_image_height'] = 279;
                                            $this->model_image->_resize($resize_param);


                                            $resize_param['size']             = '512';
                                            $resize_param['new_image_width']  = $resize_param['size'];
                                            $resize_param['new_image_height'] = $resize_param['size'];
                                            $this->model_image->_resize($resize_param);
                                            $resize_param['size']             = '256';
                                            $resize_param['new_image_width']  = $resize_param['size'];
                                            $resize_param['new_image_height'] = $resize_param['size'];
                                            $this->model_image->_resize($resize_param);
                                            $resize_param['size']             = '128';
                                            $resize_param['new_image_width']  = $resize_param['size'];
                                            $resize_param['new_image_height'] = $resize_param['size'];
                                            $this->model_image->_resize($resize_param);
                                            $resize_param['size']             = '64';
                                            $resize_param['new_image_width']  = $resize_param['size'];
                                            $resize_param['new_image_height'] = $resize_param['size'];
                                            $this->model_image->_resize($resize_param);
                                            $resize_param['size']             = '32';
                                            $resize_param['new_image_width']  = $resize_param['size'];
                                            $resize_param['new_image_height'] = $resize_param['size'];
                                            $this->model_image->_resize($resize_param);
                                            // save to database
                                            $this->model_generic->_insert($param_image);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        // }
    }
    /*****************************************************************************/
    // public function _resize($param)
    // {
    //     $upload_data                    = $param['upload_data'];
    //     $image_ext                      = strtolower(pathinfo($upload_data['full_path'], PATHINFO_EXTENSION));
    //     $image_config['image_library']  = 'gd2';
    //     $image_config['source_image']   = $upload_data['full_path'];
    //     $image_config['quality']        = "100%";
    //     $image_config['maintain_ratio'] = true;
    //     $image_config['new_image']      = $upload_data["file_path"] . 'thumbnail/' . $upload_data['raw_name'] . '_' . $param['size'] . '.' . $image_ext;
    //     // opn($upload_data);exit();

    //     $new_image_width  = $param['new_image_width'];
    //     $new_image_height = $param['new_image_height'];
    //     $image_width      = $upload_data['image_width'];
    //     $image_height     = $upload_data['image_height'];

    //     // $image_config['width'] = $new_image_width;
    //     // $image_config['height'] = $new_image_height;

    //     $x = $image_width;
    //     $y = $image_height;

    //     $w = $new_image_width;
    //     $h = $new_image_height;

    //     $s = $w - $h;

    //     $a = $x - $w;
    //     $b = 100 - ($a * 100 / $x);
    //     $c = $y * $b / 100;

    //     $d = $y - $h;
    //     $e = 100 - ($d * 100 / $y);
    //     $f = $x * $e / 100;

    //     if ($a < $h) {
    //         if ($y > ($s + $a)) {
    //             // echo 'portrait-- DARI KECIL_' . $param['size'] . ' /// ';
    //             $image_config['width']  = $w;
    //             $image_config['height'] = $c;
    //             // }else{
    //             //   $image_config['width'] = $f;
    //             //   $image_config['height'] = $h;
    //             //   echo 'landscape-- DARI KECIL';
    //         }
    //     } else {
    //         if ($y > ($s + $a)) {
    //             // echo 'portrait --- DARI BESAR_' . $param['size'] . ' /// ';
    //             $image_config['width']  = $w;
    //             $image_config['height'] = $c;
    //         } else {
    //             // echo 'lanscape --- DARI BESAR_' . $param['size'] . ' /// ';
    //             $image_config['width']  = $w;
    //             $image_config['height'] = $c;
    //         }
    //     }

    //     // opn($image_config);exit();

    //     // if ($image_width >= $new_image_width) {
    //     //   if ($image_width <= $image_height) {
    //     //     echo '[-------- PORTRAIT]';
    //     //     $r = ($image_height - $new_image_height) + $new_image_width;
    //     //     if ($r > $new_image_height) {
    //     //       $image_config['width'] = $new_image_width;
    //     //     } else {
    //     //       $image_config['height'] = $new_image_height;
    //     //     }
    //     //   } else {
    //     //     if ($image_height >= $new_image_height) {
    //     //       if ($image_width >= $image_height) {
    //     //         echo '[LANDSCAPE --------]';
    //     //         $r = ($image_width - $new_image_width) + $new_image_height;
    //     //         if ($r > $new_image_width) {
    //     //           $image_config['width'] = $new_image_width;
    //     //         } else {
    //     //           $image_config['height'] = $new_image_height;
    //     //         }
    //     //       }
    //     //     }
    //     //   }
    //     // } else {
    //     //   $image_config['width'] = $param['new_image_width'];
    //     //   if ($image_width <= $image_height) {
    //     //     echo "portrait ";
    //     //   } else {
    //     //     echo "landscape";
    //     //   }
    //     // }
    //     // proses resize
    //     $this->load->library('image_lib', $image_config);
    //     $this->image_lib->clear();
    //     $this->image_lib->initialize($image_config);
    //     if (!$this->image_lib->resize()) {
    //         echo $this->image_lib->display_errors();
    //     }
    //     /// proses crop

    //     $new_image = $image_config["new_image"];
    //     if (is_file($new_image)) {
    //         // $new_image_width                = $param['new_image_width'];
    //         // $new_image_height               = $param['new_image_height'];
    //         $image_config['width']          = $new_image_width;
    //         $image_config['height']         = $new_image_height;
    //         $image_config['maintain_ratio'] = false;
    //         $image_config['x_axis']         = $this->image_lib->width > $new_image_width ? ($this->image_lib->width - $new_image_width) / 2 : 0;
    //         $image_config['y_axis']         = $this->image_lib->height > $new_image_height ? ($this->image_lib->height - $new_image_height) / 2 : ($new_image_height - $this->image_lib->height) / -2;
    //         $image_config['source_image']   = $new_image;
    //         $image_config['new_image']      = $new_image;
    //         $this->load->library('image_lib', $image_config);
    //         $this->image_lib->clear();
    //         $this->image_lib->initialize($image_config);
    //         // $this->image_lib->crop();
    //         if (!$this->image_lib->crop()) {
    //             echo $this->image_lib->display_errors();
    //         }
    //     }

    // }
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file Image.php */
/* Location: ./application/controllers/Image.php */
