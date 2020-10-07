<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fontawesome extends MY_Controller
{

  /*****************************************************************************/
  public function __construct()
  {
    parent::__construct();

  }
  /*****************************************************************************/
  public function ver()
  {
    $ver = $this->get_version(base . '/files/bower_components/font-awesome/css/font-awesome.css');
    echo $ver;
  }
  /*****************************************************************************/
  public function icon()
  {
    if ($this->input->is_ajax_request()) {
      $icons  = $this->fontAwesome(base . '/files/bower_components/font-awesome/css/font-awesome.css');
      $list_i = array();
      foreach ($icons as $key => $value) {
        // $list_i[] = $key;
        $list_i[] = str_ireplace('fa-', '', $key);
      }
      sort($list_i);
      echo json_encode($list_i);
    }

  }
  /*****************************************************************************/

  public function get_version($filename)
  {
    $fileContents = file($filename);
    $pattern      = "/fontawesome-webfont.eot\?v=/";
    $linesFound   = preg_grep($pattern, $fileContents);
    sort($linesFound);
    // return $linesFound[0];
    preg_match('/\'(.*?)\'/', $linesFound[0], $matches);
    $vv = explode('=', $matches[1]);
    return $vv[1];
    // return isset($matches[1]) ? $matches[1] : FALSE;
  }

  /*****************************************************************************/
  public function fontAwesome($path)
  {
    $css     = file_get_contents($path);
    $pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
    preg_match_all($pattern, $css, $matches, PREG_SET_ORDER);
    $icons = array();
    foreach ($matches as $match) {
      $icons[$match[1]] = $match[2];
    }
    return $icons;
  }

  // -------------------------------------------------------

}

/* End of file Fontawesome.php */
/* Location: ./application/controllers/Fontawesome.php */
