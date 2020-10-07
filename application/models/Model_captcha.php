<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_captcha extends CI_Model
{

    /*****/
    public function __construct()
    {
        parent::__construct();
    }

    /*****/
    public function _delete_captcha_code($word)
    {
        $this->db->where('word', $word);
        $x = $this->db->get('captcha')->result();
        // opn($x);
        foreach ($x as $key => $value) {
            $file = BASE . '/files/captcha/' . $value->filename;
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->db->where('word', $word)->delete('captcha');
    }
    /*****/
    public function _delete_captcha($expiration)
    {
        $this->db->where('captcha_time < ', $expiration)->delete('captcha');
    }

    /*****/
    public function _create_captcha()
    {

        $captcha_path = './files/captcha';
        if (false == is_dir($captcha_path)) {
            mkdir($captcha_path);
            chmod($captcha_path, 0777);
        }
        $vals['img_path']             = './files/captcha/';
        $vals['img_url']              = base . '/files/captcha';
        $vals['font_path']            = './files/fonts/Consolas_Bold.ttf';
        $vals['img_width']            = 270;
        $vals['img_height']           = 34;
        $vals['expiration']           = 7200;
        $vals['word_length']          = 6;
        $vals['font_size']            = 20;
        $vals['img_id']               = 'Imageid';
        $vals['pool']                 = '237EFHJKLMNPRTUVWXY';
        $vals['colors']['background'] = array(255, 255, 255);
        $vals['colors']['border']     = array(155, 155, 155);
        $vals['colors']['grid']       = array(255, 40, 40);
        $vals['colors']['text']       = array(255, 40, 40);
        // $vals['colors']['text']       = array(0, 0, 0);

        $cap                  = create_captcha($vals);
        $data['captcha_time'] = $cap['time'];
        $data['ip_address']   = $this->input->ip_address();
        $data['word']         = $cap['word'];
        $data['filename']     = $cap['filename'];

        $query = $this->db->insert_string('captcha', $data);
        $this->db->query($query);
        $data['image'] = $cap['image'];
        return $data;
    }

    /*****/
    public function _get_captcha($word, $ip_address, $expiration)
    {
        // Then see if a captcha exists:
        // $sql   = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
        // $binds = array($word, $ip_address, $expiration);
        // $query = $this->db->query($sql, $binds);
        // $row   = $query->row();
        // return $row;

        $this->db->where('word', $word);
        $this->db->where('ip_address', $ip_address);
        $this->db->where('captcha_time > ', $expiration);
        return $this->db->get('captcha')->num_rows();
    }

    /*****/
}

/* End of file Model_captcha.php */

/* Location: ./application/models/Model_captcha.php */
