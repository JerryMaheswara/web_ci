<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chat extends MY_Controller
{

  /************/
  public function __construct()
  {
    parent::__construct();

    $this->_header = $this->parser->parse('header.html', $this->data, true);
    $this->_footer = $this->parser->parse('footer.html', $this->data, true);
  }
  /************/
  /************/
  public function index()
  {

    $this->_body   = $this->parser->parse('default.html', $this->data, true);

    $this->data['header'] = $this->_header;
    $this->data['footer'] = $this->_footer;
    $this->data['body']   = $this->_body;
    $this->parser->parse('default_index.html', $this->data, false);

  }
  /************/
  public function group()
  {


    $this->_set_referer();
    if ($this->_is_login) {

      $arg = func_get_args();
      if (isset($arg[0]) && is_numeric($arg[0])) {
        $group_id = $arg[0];
        $this->data['mutakalim_id']   = $this->_user_id;
        $mutakalim_info               = $this->model_user->_get_user_each($this->_user_id);
        $this->data['mutakalim_info'] = $mutakalim_info;
        $this->_body = $this->parser->parse('chat_message_group.html', $this->data, true);
      }else{
        $this->_body = $this->parser->parse('chat_grouplist.html', $this->data, true);

      }
    } else {
      redirect(base . '/login');
    }
    $this->data['header'] = $this->_header;
    $this->data['footer'] = $this->_footer;
    $this->data['body']   = $this->_body;
    $this->parser->parse('index.html', $this->data, false);
  }
  /************/
  function private () {
    // opn($this->data);exit();
    // opn($_SESSION);exit();
    $this->_set_referer();
    if ($this->_is_login) {
      // $mutakalim_info               = $this->model_user->_get_user_each($this->_user_id);
      $this->db->where('user_id', $this->_user_id);
      $mutakalim_info = $this->db->get('user')->result();
      $this->data['mutakalim_info'] = $mutakalim_info;

      $arg = func_get_args();
      if (isset($arg[0]) && is_numeric($arg[0])) {
        $this->_set_referer();

        $mukhothob_id                 = $arg[0]; //(isset($arg[0]) && is_numeric($arg[0])) ? $arg[0] : '';
        $this->data['mukhothob_id']   = $mukhothob_id;
        $this->data['mutakalim_id']   = $this->_user_id;
        // $mukhothob_info               = $this->model_user->_get_user_each($mukhothob_id);
        $this->db->where('user_id', $mukhothob_id);
        $mukhothob_info = $this->db->get('user')->result();
        $this->data['mukhothob_info'] = $mukhothob_info;
        // opn($mukhothob_info);exit();

        $this->_body = $this->parser->parse('chat_message_private.html', $this->data, true);
      } else {

        // $my_friend = $this->model_user->_get_my_friend($this->_user_id);
        $this->db->where('user_id_a', $this->_user_id);
        $my_friend = $this->db->get('friendlist')->result();
        $this->data['my_friend'] = $my_friend;
        // opn($my_friend);exit();
        $this->_body = $this->parser->parse('chat_friendlist.html', $this->data, true);
      }
    } else {
      redirect(base . '/login');
    }
    $this->data['header'] = $this->_header;
    $this->data['footer'] = $this->_footer;
    $this->data['body']   = $this->_body;
    $this->parser->parse('index.html', $this->data, false);
  }
  /************/
  /************/
  public function render_message()
  {
    if ($this->input->is_ajax_request()) {
      if ($this->input->post('data')) {
        $param = $this->input->post('data');
        foreach ($param as $key => $value) {
        // opn($value['mutakalim']);exit();
          $param[$key]['sender_detail'][] = $value['mutakalim'];
          $param[$key]['tarikh'] = date('l, F jS, Y, h:i:s A', strtotime($value['created']));
          $param[$key]['received_or_sent'] = ($value['mukhothob'] == $this->_user_id)?'received':'sent';
          $param[$key]['show_or_hide'] = ($value['mukhothob'] == $this->_user_id)?'':'hidden destroy';
          $param[$key]['message_with_avatar'] = ($value['mukhothob'] == $this->_user_id)?'message-with-avatar':'';
        }
        krsort($param);
        $data_message               = $param;
        $this->data['data_message'] = $data_message;
      }else{
        $this->data['data_message'] = array();
      }
      // opn($data_message);exit();
      $render_message = $this->parser->parse('render_message.html', $this->data, true);
      // opn($render_message);exit();
      echo $render_message;
    }
  }
  /************/
  public function to_mukhothob()
  {
    if ($this->input->is_ajax_request()) {
      if ($this->input->post()) {
        $param                      = $this->input->post();
        foreach ($param as $key => $value) {
          $param['data']['tarikh'] = date('l, F jS, Y, h:i:s A', strtotime($value['created']));
        }
        $data_message               = $param;
        $this->data['data_message'] = $data_message;
      }
      $to_mukhothob = $this->parser->parse('chat_to_mukhothob.html', $this->data, true);
      // opn($to_mukhothob);exit();
      echo $to_mukhothob;
    }
  }
  /************/
  public function to_mutakalim()
  {
    if ($this->input->is_ajax_request()) {
      if ($this->input->post()) {
        $param = $this->input->post();
        foreach ($param as $key => $value) {
          $param['data']['tarikh'] = date('l, F jS, Y, h:i:s A', strtotime($value['created']));
        }
        // opn($param);exit();
        $data_message               = $param;
        $this->data['data_message'] = $data_message;
      }
      $to_mutakalim = $this->parser->parse('chat_to_mutakalim.html', $this->data, true);
      // opn($to_mutakalim);exit();
      echo $to_mutakalim;
    }
  }
  /************/
  /************/

}

/* End of file Chat.php */
/* Location: ./application/controllers/Chat.php */
