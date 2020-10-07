<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_image extends CI_Model {

    /*****************************************************************************/    

    public function _resize($param)
    {
        $upload_data                    = $param['upload_data']; 
        $image_ext                      = strtolower(pathinfo($upload_data['full_path'], PATHINFO_EXTENSION));
        $image_config['image_library']  = 'gd2';
        $image_config['source_image']   = $upload_data['full_path'];
        $image_config['quality']        = "100%";
        $image_config['maintain_ratio'] = true;
        $image_config['new_image']      = $upload_data["file_path"] . 'thumbnail/' . $upload_data['raw_name'] . '_' . $param['size'] . '.' . $image_ext;
        // opn($upload_data);exit();

        $new_image_width  = $param['new_image_width'];
        $new_image_height = $param['new_image_height'];
        $image_width      = $upload_data['image_width'];
        $image_height     = $upload_data['image_height'];

        // $image_config['width'] = $new_image_width;
        // $image_config['height'] = $new_image_height;

        $x = $image_width;
        $y = $image_height;

        $w = $new_image_width;
        $h = $new_image_height;

        $s = $w - $h;

        $a = $x - $w;
        $b = 100 - ($a * 100 / $x);
        $c = $y * $b / 100;

        $d = $y - $h;
        $e = 100 - ($d * 100 / $y);
        $f = $x * $e / 100;

        // $portrait = $image_width < $image_height;
        // $landscape = $image_width > $image_height;

        // $orientation = '';
        // if ($image_width < $image_height) {
        //     $orientation = 'portrait';
        //     $priority = 'image_height';
        //     $image_config['height']  = $new_image_height;
        // }
        // if ($image_width > $image_height) {
        //     $orientation = 'landscape';
        //     $priority = 'image_width';
        //     $image_config['width']  = $new_image_width;
        // }
        // if ($image_width == $image_height) {
            if ($new_image_width < $new_image_height) {
                $orientation = 'portrait';
                $priority = 'new_image_height : '. $new_image_height;
                $image_config['height']  = $new_image_height;
            }
            if ($new_image_width > $new_image_height) {
                $orientation = 'landscape';
                $priority = 'new_image_width : '. $new_image_width;
                $image_config['width']  = $new_image_width;
            }
        // }
        // echo $orientation;
        // echo ' \---===---/ ';
        // echo $priority;

        // if ($x == $y) {
        //     if ($new_image_width > $image_width) {
        //         $image_config['width']  = $new_image_width;
        //     }
        //     if ($new_image_height > $image_height) {
        //         $image_config['width']  = $new_image_height;
        //     }
        // }

        // if (($a < $h) && ($y > ($s + $a))) {
        //     // if ($y > ($s + $a)) {
        //         echo 'portrait-- DARI KECIL_' . $param['size'] . ' /// ';
        //         $image_config['width']  = $w;
        //         $image_config['height'] = $c;
        //         // }else{
        //         //   $image_config['width'] = $f;
        //         //   $image_config['height'] = $h;
        //         //   echo 'landscape-- DARI KECIL';
        //     // }
        // }
        // if (($a > $h) && ($y < ($s + $a))) {
        //     // if ($y > ($s + $a)) {
        //         // echo 'portrait-- DARI KECIL_' . $param['size'] . ' /// ';
        //         // $image_config['width']  = $w;
        //         // $image_config['height'] = $c;
        //         // }else{
        //         echo 'landscape-- DARI KECIL_' . $param['size'] . ' /// ';
        //           $image_config['width'] = $f;
        //           $image_config['height'] = $h;
        //     // }
        // }


        // if ($a < $h) {
        //     if ($y > ($s + $a)) {
        //         // echo 'portrait-- DARI KECIL_' . $param['size'] . ' /// ';
        //         $image_config['width']  = $w;
        //         $image_config['height'] = $c;
        //         // }else{
        //         //   $image_config['width'] = $f;
        //         //   $image_config['height'] = $h;
        //         //   echo 'landscape-- DARI KECIL';
        //     }
        // } else {
        //     if ($y > ($s + $a)) {
        //         // echo 'portrait --- DARI BESAR_' . $param['size'] . ' /// ';
        //         $image_config['width']  = $w;
        //         $image_config['height'] = $c;
        //     } else {
        //         // echo 'lanscape --- DARI BESAR_' . $param['size'] . ' /// ';
        //         $image_config['width']  = $w;
        //         $image_config['height'] = $c;
        //     }
        // }

        // opn($image_config);exit();

        // if ($image_width >= $new_image_width) {
        //   if ($image_width <= $image_height) {
        //     echo '[-------- PORTRAIT]';
        //     $r = ($image_height - $new_image_height) + $new_image_width;
        //     if ($r > $new_image_height) {
        //       $image_config['width'] = $new_image_width;
        //     } else {
        //       $image_config['height'] = $new_image_height;
        //     }
        //   } else {
        //     if ($image_height >= $new_image_height) {
        //       if ($image_width >= $image_height) {
        //         echo '[LANDSCAPE --------]';
        //         $r = ($image_width - $new_image_width) + $new_image_height;
        //         if ($r > $new_image_width) {
        //           $image_config['width'] = $new_image_width;
        //         } else {
        //           $image_config['height'] = $new_image_height;
        //         }
        //       }
        //     }
        //   }
        // } else {
        //   $image_config['width'] = $param['new_image_width'];
        //   if ($image_width <= $image_height) {
        //     echo "portrait ";
        //   } else {
        //     echo "landscape";
        //   }
        // }
        // proses resize
        $this->load->library('image_lib', $image_config);
        $this->image_lib->clear();
        $this->image_lib->initialize($image_config);
        if (!$this->image_lib->resize()) {
            echo $this->image_lib->display_errors();
        }
        /// proses crop

        $new_image = $image_config["new_image"];
        if (is_file($new_image)) {
            // $new_image_width                = $param['new_image_width'];
            // $new_image_height               = $param['new_image_height'];
            $image_config['width']          = $new_image_width;
            $image_config['height']         = $new_image_height;
            $image_config['maintain_ratio'] = false;
            $image_config['x_axis']         = $this->image_lib->width > $new_image_width ? ($this->image_lib->width - $new_image_width) / 2 : ($new_image_width - $this->image_lib->width) / -2;
            $image_config['y_axis']         = $this->image_lib->height > $new_image_height ? ($this->image_lib->height - $new_image_height) / 2 : ($new_image_height - $this->image_lib->height) / -2;
            $image_config['source_image']   = $new_image;
            $image_config['new_image']      = $new_image;
            $this->load->library('image_lib', $image_config);
            $this->image_lib->clear();
            $this->image_lib->initialize($image_config);
            // $this->image_lib->crop();
            if (!$this->image_lib->crop()) {
                echo $this->image_lib->display_errors();
            }
        }

    }
    /*****************************************************************************/    

}

/* End of file Model_image.php */
/* Location: ./application/modules/image/models/Model_image.php */